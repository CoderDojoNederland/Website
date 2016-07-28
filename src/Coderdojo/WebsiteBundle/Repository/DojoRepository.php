<?php

namespace Coderdojo\WebsiteBundle\Repository;

use Coderdojo\WebsiteBundle\Entity\Dojo;
use Doctrine\ORM\EntityRepository;

class DojoRepository extends EntityRepository
{
    /**
     * Fetch all dojos ordered by city
     *
     * @return Dojo[]
     */
    public function getSortedByCity()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
