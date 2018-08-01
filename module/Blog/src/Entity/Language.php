<?php
/**
 * Created by PhpStorm.
 * User: pear
 * Date: 5/17/18
 * Time: 10:50 AM
 */

namespace Blog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="LanguageRepository")
 * @ORM\Table(name="languages")
 */
class Language
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $languageName;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $languageCode;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->languageName;
    }

    /**
     * @param mixed $languageName
     */
    public function setName($languageName): void
    {
        $this->languageName = $languageName;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->languageCode;
    }

    /**
     * @param mixed $languageCode
     */
    public function setCode($languageCode): void
    {
        $this->languageCode = $languageCode;
    }
}
