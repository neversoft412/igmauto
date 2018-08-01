<?php
/**
 * Created by PhpStorm.
 * User: pear
 * Date: 5/17/18
 * Time: 10:54 AM
 */

namespace Blog\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="postLanguage")
 */
class PostLanguage
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
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="postLanguages")
     * @ORM\JoinColumn(name="postId", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="postLanguages")
     * @ORM\JoinColumn(name="languageId", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $language;

    /**
     * @param Language $language
     */
    public function __construct(?Language $language = null)
    {
        $this->language = $language;
    }

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
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Sets associated post.
     *
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
        $post->addPostLanguage($this);
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
