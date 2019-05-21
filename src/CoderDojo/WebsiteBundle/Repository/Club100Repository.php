<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Repository;

use CoderDojo\WebsiteBundle\Entity\Club100;
use Doctrine\ORM\EntityRepository;

class Club100Repository extends EntityRepository
{
    /**
     * @return Club100[]
     */
    public function getAllWithImage(): array
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->where($qb->expr()->andX(
                $qb->expr()->isNotNull('c.avatar'),
                $qb->expr()->neq('c.avatar', $qb->expr()->literal(''))
            ))
            ->getQuery()
            ->getResult()
        ;
    }
}
