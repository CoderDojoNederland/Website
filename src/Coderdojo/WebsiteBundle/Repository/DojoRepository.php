<?php

namespace Coderdojo\WebsiteBundle\Repository;

use Coderdojo\WebsiteBundle\Entity\User;
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
        return $this->createQueryBuilder('u')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
