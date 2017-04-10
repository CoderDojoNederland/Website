<?php

namespace CoderDojo\BlogBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CoderDojo\BlogBundle\Repository\AuthorRepository")
 */
class Author
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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var Article[]
     *
     * @ORM\OneToMany(targetEntity="CoderDojo\BlogBundle\Entity\Article", mappedBy="author")
     */
    private $articles;

    /**
     * @param string $uuid
     * @param string $name
     */
    public function __construct($uuid, $name)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $slugger = new Slugify();
        $this->slug = $slugger->slugify($name);
        $this->articles = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     */
    public function addArticle(Article $article)
    {
        $this->articles->add($article);
    }
}
