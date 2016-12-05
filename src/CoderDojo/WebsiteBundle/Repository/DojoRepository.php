<?php

namespace CoderDojo\WebsiteBundle\Repository;

use CoderDojo\WebsiteBundle\Entity\Dojo;
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

    /**
     * @param $city
     * @param $email
     * @param $twitter
     * @return Dojo|null
     */
    public function getForExternalWithoutZenId($city, $email, $twitter)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb
            ->where($qb->expr()->andX(
                $qb->expr()->eq('d.city', ':city'),
                $qb->expr()->eq('d.email', ':email')
            ))
            ->orWhere($qb->expr()->andX(
                $qb->expr()->eq('d.city', ':city'),
                $qb->expr()->eq('d.twitter', ':twitter')
            ))
            ->andWhere('d.zenId IS NULL')
            ->setParameter('city', $city)
            ->setParameter('email', $email)
            ->setParameter('twitter', $twitter)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
