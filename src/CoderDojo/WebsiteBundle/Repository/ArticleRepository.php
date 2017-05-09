<?php

namespace CoderDojo\WebsiteBundle\Repository;

use CoderDojo\WebsiteBundle\Entity\Article;
use CoderDojo\WebsiteBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPublishedQueryBuilder(Category $category = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.published = true');

        if ($category) {
            $qb->andWhere('a.category = :category');
            $qb->setParameter('category', $category);
        }

        $qb->orderBy('a.publishedAt', 'DESC');

        return $qb;
    }

    /**
     * @return Article[]
     */
    public function getLatest(int $amount)
    {
        $qb = $this->createQueryBuilder('a')
                   ->where('a.published = true')
                   ->setMaxResults($amount)
                   ->orderBy('a.publishedAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}