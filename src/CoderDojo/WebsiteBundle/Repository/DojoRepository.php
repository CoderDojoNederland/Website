<?php

namespace CoderDojo\WebsiteBundle\Repository;

use CoderDojo\WebsiteBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class DojoRepository extends EntityRepository
{
    /**
     * Fetch all dojos ordered by city
     *
     * @return User[]
     */
    public function getSortedByCity()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns an array of known zen ids for dojos
     *
     * @return array
     */
    public function getZenIds()
    {
        $list = $this->createQueryBuilder('d')
            ->select('d.zenId')
            ->where('d.zenId IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        $return = [];

        foreach ($list as $item) {
            $return[] = $item['zenId'];
        }

        return $return;
    }
}
