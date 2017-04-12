<?php

namespace CoderDojo\WebsiteBundle\DataFixtures\ORM;

use CoderDojo\WebsiteBundle\Entity\Article;
use CoderDojo\WebsiteBundle\Entity\Category;
use CoderDojo\WebsiteBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @codeCoverageIgnore
 */
class LoadArticleData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $articles = [
            [
                'title' => 'Some Awesome Title',
                'body' => 'Lorem Ipsum Dolor Sit Amet. ',
                'image' => 'http://lorempixel.com/800/600',
                'publishedAt' => '-2 days',
                'category' => 'category_organisation',
                'author' => 'user',
                'reference' => 'article_1'
            ],
            [
                'title' => 'Another Super Cool Blog',
                'body' => 'Lorem Ipsum Dolor Sit Amet. ',
                'image' => 'http://lorempixel.com/1000/400?v=2',
                'publishedAt' => '-1 week',
                'category' => 'category_worldwide',
                'author' => 'user',
                'reference' => 'article_2'
            ],
        ];

        foreach ($articles as $article) {
            /** @var User $author */
            $author = $this->getReference($article['author']);
            /** @var Category $category */
            $category = $this->getReference($article['category']);

            $body ='<p>';
            for($i=0; $i < rand(50,100); $i++) {
                $body .= $article['body'];
            }
            $body .= '</p>';

            $entity = new Article(
                (string) Uuid::uuid4(),
                $article['title'],
                $body,
                $article['image'],
                new \DateTime($article['publishedAt']),
                $category,
                $author
            );

            $this->setReference($article['reference'], $entity);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
