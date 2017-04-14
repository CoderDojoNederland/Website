<?php

namespace CoderDojo\WebsiteBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CoderDojo\WebsiteBundle\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\Id
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $published;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Category", inversedBy="articles")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $category;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @param           $uuid
     * @param           $title
     * @param           $body
     * @param           $image
     * @param Category  $category
     * @param User    $author
     */
    public function __construct($uuid, $title, $body, $image, Category $category, User $author)
    {
        $this->uuid = $uuid;
        $this->title = $title;
        $slugger = new Slugify();
        $this->slug = $slugger->slugify($title);
        $this->body = $body;
        $this->image = $image;
        $this->category = $category;
        $this->author = $author;
        $this->createdAt = new \DateTime();
        $this->published = false;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param \DateTime $when
     */
    public function publish(\DateTime $when = null)
    {
        $this->published = true;

        if ($this->publishedAt) {
            return;
        }

        $this->publishedAt = $when ?: new \DateTime();
    }

    /**
     * @param bool $withDate
     */
    public function unPublish($withDate = false)
    {
        $this->published = false;

        if ($withDate) {
            $this->publishedAt = null;
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}