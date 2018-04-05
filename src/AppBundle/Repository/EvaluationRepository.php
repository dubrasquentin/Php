<?php

namespace AppBundle\Repository;

/**
 * EvaluationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EvaluationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByIdProduit($produitId)
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.produit = :produitId')
            ->setParameter(':produitId', $produitId)
            ->getQuery();

        return $qb->execute();


    }
}
