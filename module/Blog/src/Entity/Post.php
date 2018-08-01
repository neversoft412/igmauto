<?php

namespace Blog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tag\Entity\Tag;

/**
 * @ORM\Entity(repositoryClass="PostRepository")
 * @ORM\Table(name="posts")
 */
class Post
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateAdded;

    /**
     * @var PostLanguage[]
     * @ORM\OneToMany(targetEntity="PostLanguage", mappedBy="post", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="postId")
     */
    private $postLanguages;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="Tag\Entity\Tag", inversedBy="posts")
     * @ORM\JoinTable(name="post_tag",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $tags;

    /**
     * @param array $languages
     */
    public function __construct(array $languages)
    {
        $this->postLanguages = new ArrayCollection();
        $this->tags = new ArrayCollection();

        foreach ($languages as $language) {
            $this->addPostLanguage(new PostLanguage($language));
        }
    }

    /**
     * @return int
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
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param mixed $dateAdded
     */
    public function setDateAdded($dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return ArrayCollection
     */
    public function getPostLanguages()
    {
        return $this->postLanguages;
    }

    /**
     * @param PostLanguage $postLanguage
     */
    public function addPostLanguage(PostLanguage $postLanguage): void
    {
        if ($this->postLanguages->contains($postLanguage)) {
            return;
        }

        $this->postLanguages->add($postLanguage);
        $postLanguage->setPost($this);
    }

    /**
     * @param PostLanguage $postLanguage
     */
    public function removePostLanguage(PostLanguage $postLanguage): void
    {
        if (!$this->postLanguages->contains($postLanguage)) {
            return;
        }

        $this->postLanguages->removeElement($postLanguage);
    }

    /**
     * @param string $localization
     *
     * @return PostLanguage|null
     */
    public function getPostLanguage(string $localization): ?PostLanguage
    {
        foreach ($this->postLanguages as $postLanguage) {
            if ($postLanguage->getLanguage()->getCode() === $localization) {
                return $postLanguage;
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection|Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param $tag
     */
    public function addTag($tag): void
    {
        $this->tags->add($tag);
    }

    /**
     * @param $tag
     */
    public function removeTag($tag): void
    {
        $this->tags->removeElement($tag);
    }
}
