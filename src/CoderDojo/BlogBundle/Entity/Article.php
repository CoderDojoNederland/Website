<?php

namespace CoderDojo\BlogBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CoderDojo\BlogBundle\Repository\ArticleRepository")
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
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\BlogBundle\Entity\Category", inversedBy="articles")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $category;

    /**
     * @var Author
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\BlogBundle\Entity\Author", inversedBy="articles")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $author;

    /**
     * @param           $uuid
     * @param           $title
     * @param           $body
     * @param           $image
     * @param \DateTime $publishedAt
     * @param Category  $category
     * @param Author    $author
     */
    public function __construct($uuid, $title, $body, $image, \DateTime $publishedAt, Category $category, Author $author)
    {
        $this->uuid = $uuid;
        $this->title = $title;
        $slugger = new Slugify();
        $this->slug = $slugger->slugify($title);
        $this->body = $body;
        $this->image = $image;
        $this->publishedAt = $publishedAt;
        $this->category = $category;
        $this->author = $author;
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
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }
}