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

    public function getCodeWeek2019Events()
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :start_date')
            ->andWhere('e.date <= :end_date')
            ->orderBy('e.date', 'ASC')
            ->setParameter('start_date', new \DateTime('2018-10-05'))
            ->setParameter('end_date', new \DateTime('2018-10-20'))
            ->getQuery()
            ->getResult();
    }
}
