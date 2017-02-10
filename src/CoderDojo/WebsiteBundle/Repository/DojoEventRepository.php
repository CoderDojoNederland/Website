<?php

namespace CoderDojo\WebsiteBundle\Repository;

use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use Doctrine\ORM\EntityRepository;

class DojoEventRepository extends EntityRepository
{
    /**
     * Fetch all upcoming events
     *
     * @param null $max
     * @return DojoEvent[]
     */
    public function getAllUpcomingEvents($max = null)
    {
        $return = $this->createQueryBuilder('e')
            ->join('e.dojo', 'd')
            ->having('e.date >= :today')
            ->setParameter('today', date('Y-m-d'))
            ->orderBy('e.date', 'ASC')
            ->addOrderBy('d.city', 'ASC')
            ->getQuery();

        if ($max) {
            $return->setMaxResults($max);
        }

        return $return->getResult();
    }
}