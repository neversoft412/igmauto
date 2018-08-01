<?php

namespace Application\Validator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use Zend\Validator\AbstractValidator;

class Unique extends AbstractValidator
{

    public const NOT_UNIQUE_VALUE = 'notUniqueValue';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_UNIQUE_VALUE => 'The value is not unique',
    ];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param array $options
     *
     * @return AbstractValidator
     * @throws InvalidArgumentException
     */
    public function setOptions($options = [])
    {
        if (isset($options['object_manager'])) {
            $this->setEntityManager($options['object_manager']);
        } else {
            throw new InvalidArgumentException('Missing option "object_manager"');
        }

        if (isset($options['target_class'])) {
            $this->setTargetClass($options['target_class']);
        } else {
            throw new InvalidArgumentException('Missing option "target_class"');
        }

        if (isset($options['unique_column'])) {
            $this->setUniqueColumn($options['unique_column']);
        } else {
            throw new InvalidArgumentException('Missing option "unique_column"');
        }

        return parent::setOptions($options);
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $targetClass
     *
     * @throws InvalidArgumentException
     */
    public function setTargetClass(string $targetClass)
    {
        if (class_exists($targetClass)) {
            $this->options['target_class'] = $targetClass;
        } else {
            throw new InvalidArgumentException('Option "target_class" is not a valid class');
        }
    }

    /**
     * @param string $uniqueColumn
     */
    public function setUniqueColumn(string $uniqueColumn)
    {
        $this->options['unique_column'] = $uniqueColumn;
    }

    /**
     * @return string
     */
    public function getTargetClass(): string
    {
        return $this->options['target_class'];
    }

    /**
     * @return string
     */
    public function getUniqueColumn(): string
    {
        return $this->options['unique_column'];
    }

    /**
     * @param mixed $value
     *
     * @param null  $context
     *
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('COUNT(Entity.' . $this->getUniqueColumn() . ')')
            ->from($this->getTargetClass(), 'Entity')
            ->where('Entity.' . $this->getUniqueColumn() . ' = :value')
            ->setParameter('value', $value);

        if (isset($context['id'])) {
            $queryBuilder->andWhere('Entity.id != :id')
                ->setParameter('id', $context['id']);
        }

        try {
            $result = $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        if ($result > 0) {
            $this->error(self::NOT_UNIQUE_VALUE);
            return false;
        }

        return true;
    }
}
