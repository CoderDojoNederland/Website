<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Repository;

use CoderDojo\WebsiteBundle\Entity\Club100;
use Doctrine\ORM\EntityRepository;

class Club100Repository extends EntityRepository
{
    public const PUBLIC_ONLY = true;

    /**
     * @return Club100[]
     */
    public function getAllActiveWithImage(): array
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->where($qb->expr()->andX(
                $qb->expr()->isNotNull('c.avatar'),
                $qb->expr()->neq('c.avatar', $qb->expr()->literal('')),
                $qb->expr()->isNull('c.unsubscribedAt'),
                $qb->expr()->eq('c.confirmed', $qb->expr()->literal(true)),
                $qb->expr()->eq('c.public', $qb->expr()->literal(true))
            ))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param bool $publicOnly
     *
     * @return Club100[]
     */
    public function getAllActive(bool $publicOnly = false): array
    {
        $qb = $this->createQueryBuilder('c');
        $query =  $qb
            ->where($qb->expr()->andX(
                $qb->expr()->isNull('c.unsubscribedAt'),
                $qb->expr()->eq('c.confirmed', $qb->expr()->literal(true))
            ));

        if ($publicOnly) {
            $query->andWhere($qb->expr()->eq('c.public', $qb->expr()->literal($publicOnly)));
        }

        return $query->getQuery()->getResult();
    }
}
