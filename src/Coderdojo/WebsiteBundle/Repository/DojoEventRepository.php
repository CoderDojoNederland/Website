<?php

namespace Coderdojo\WebsiteBundle\Repository;

use Coderdojo\WebsiteBundle\Entity\DojoEvent;
use Doctrine\ORM\EntityRepository;

class DojoEventRepository extends EntityRepository
{
    /**
     * Fetch all upcoming events
     *
     * @return DojoEvent[]
     */
    public function getAllUpcomingEvents()
    {
        return $this->createQueryBuilder('d')
            ->having('d.date >= :today')
            ->setParameter('today', date('Y-m-d'))
            ->orderBy('d.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}