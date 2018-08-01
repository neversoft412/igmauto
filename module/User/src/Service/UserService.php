<?php

namespace User\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use User\Entity\User;
use User\Entity\UserRepository;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\Headers;
use Zend\Http\PhpEnvironment\Request;
use Zend\Permissions\Acl\Role\{
    GenericRole, RoleInterface
};
use Zend\Session\Container;

class UserService
{
    public const USER_ID = 'user_id';
    public const REMEMBER_ME = 'remember_me';
    public const GUEST_ROLE = 'guest';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Container
     */
    private $userSession;

    /**
     * @var null|Headers
     */
    private $headers;

    /**
     * @var null|Request
     */
    private $request;

    /**
     * UserService constructor.
     *
     * @param EntityManager  $entityManager
     * @param UserRepository $userRepository
     * @param Container      $userSessionContainer
     * @param null|Headers   $headers
     * @param null|Request   $request
     */
    public function __construct(
        EntityManager $entityManager,
        UserRepository $userRepository,
        Container $userSessionContainer,
        ?Headers $headers,
        ?Request $request
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userSession = $userSessionContainer;
        $this->headers = $headers;
        $this->request = $request;
    }

    /**
     * @param $username
     * @param $password
     * @param $rememberMe
     *
     * @return bool
     */
    public function logIn($username, $password, $rememberMe): bool
    {
        $user = $this->userRepository
            ->getUserByUsername($username);

        if ($user
            && password_verify(
                base64_encode(
                    hash('sha384', $password, true)
                ),
                $user->getPassword()
            )
        ) {
            $this->userSession->offsetSet(self::USER_ID, $user->getId());

            if ($rememberMe) {
                $generatedToken = $this->generateToken();
                $cookieToken = $user->getSelector() . ':' . $generatedToken;
                $expires = new DateTime();
                $expires->modify('+1 year');

                $userCookie = new SetCookie(
                    self::REMEMBER_ME,
                    $cookieToken,
                    $expires,
                    '/'
                );

                if ($this->headers) {
                    $this->headers->addHeader($userCookie);
                    $user->setToken(hash('sha256', $generatedToken));
                    $user->setExpires($expires);

                    try {
                        $this->entityManager->flush();
                    } catch (OptimisticLockException $e) {
                    } catch (ORMException $e) {
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function logout(): void
    {
        if ($this->isLogged()) {
            $this->userSession->offsetUnset(self::USER_ID);

            /** @var Request $request */
            $request = $this->request;
            $requestCookie = $request->getCookie();

            if ($requestCookie instanceof Cookie
                && $requestCookie->offsetExists(self::REMEMBER_ME)
            ) {
                /** @var DateTime $expires */
                $expires = new DateTime();
                $expires->modify('-1 day');

                /** @var SetCookie $cookie */
                $cookie = new SetCookie();
                $cookie->setName(self::REMEMBER_ME);
                $cookie->setExpires($expires);

                if ($this->headers) {
                    $this->headers->addHeader($cookie);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        // Try to remember the user, using the information from the cookie.
        if (!$this->userSession->offsetExists(self::USER_ID)) {
            $this->rememberMe();
        }

        return $this->userSession->offsetExists(self::USER_ID);
    }

    /**
     * @return null|User
     */
    public function getLoggedUser(): ?User
    {
        $user = null;

        if ($this->isLogged()) {
            /** @var User $user */
            $user = $this->userRepository
                ->find($this->userSession->offsetGet(self::USER_ID));
        }

        return $user;
    }

    public function rememberMe(): void
    {
        $cookie = $this->request->getCookie();
        if ($cookie instanceof Cookie && $cookie->offsetExists(self::REMEMBER_ME)) {
            $cookieValue = $cookie->offsetGet(self::REMEMBER_ME);
            [$cookieSelector, $cookieToken] = explode(':', $cookieValue);

            $user = $this->userRepository->getUserBySelector($cookieSelector);
            if ($user
                && hash_equals(
                    $user->getToken(),
                    hash('sha256', $cookieToken)
                )
            ) {
                $this->userSession->offsetSet(self::USER_ID, $user->getId());
            }
        }
    }

    /**
     * @return array|RoleInterface[]
     */
    public function getCurrentRoles(): array
    {
        if ($this->isLogged()) {
            /** @var User $user */
            $user = $this->getLoggedUser();

            $currentRoles = [];
            foreach ($user->getRoles() as $role) {
                $currentRoles[] = new GenericRole($role->getName());
            }

            return $currentRoles;
        }

        return [
            new GenericRole(self::GUEST_ROLE),
        ];
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateToken(int $length = 12): string
    {
        try {
            $token = bin2hex(random_bytes($length / 2));
        } catch (Exception $e) {
            $characters = array_merge(
                range(0, 9),
                range('a', 'z'),
                range('A', 'Z')
            );
            $max = \count($characters) - 1;
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $rand = random_int(0, $max);
                $token .= $characters[$rand];
            }
        }

        return $token;
    }
}
