<?php

namespace AppBundle\Repository;

/**
 * ProduitRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProduitRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByCodeBarre($codebarre)
    {
        // automatically knows to select Products
        // the "p" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.codeBarre = :code_barre')
            ->setParameter(':code_barre', $codebarre)
            ->getQuery();

        return $qb->execute();

        // to get just one result:
        $product = $qb->setMaxResults(1)->getOneOrNullResult();
    }

    public function findTheLast()
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.dateDerniereVue', 'DESC')
            ->setMaxResults('8')
            ->getQuery();

        return $qb->execute();
    }
    public function findAVG($produitId)
    {
        $qb = $this->getEntityManager()->getConnection();
        $req = 'SELECT AVG(note) as note FROM evaluation e
			WHERE e.produit_id = :produit_id';
        $stmt = $qb->prepare($req);
        $stmt->execute(['produit_id' => $produitId]);

        return $stmt->fetch();
    }
    public function findTheBest()
    {
        $qb = $this->getEntityManager()->getConnection();
        $req = 'SELECT produit_id, AVG(note) as note FROM evaluation e
			GROUP BY produit_id
			ORDER BY note DESC LIMIT 8;';
        $stmt = $qb->prepare($req);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
