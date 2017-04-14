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
        $articles = [];

        for ($i = 0; $i < 40; $i++) {
            $rand = rand(1,5);
            $published = rand(0,1);

            $articles[] = [
                'title' => 'Some Awesome Title '.$i,
                'body' => 'Lorem Ipsum Dolor Sit Amet. ',
                'image' => 'http://lorempixel.com/800/600?v='.$i,
                'publishedAt' => '-'.$i.' days',
                'category' => 'category_'.$rand,
                'reference' => 'article_'.$i,
                'published' => $published,
                'author' => 'user',
            ];
        }

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
                $category,
                $author
            );

            if($article['published']) {
                $entity->publish(new \DateTime($article['publishedAt']));
            }

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
