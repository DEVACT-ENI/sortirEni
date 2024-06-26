<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function searchSorties($campus, $keyword, $dateDebut, $dateFin, $organisateur, $inscrit, $nonInscrit, $sortiesPassees)
    {
        $qb = $this->createQueryBuilder('s');

        if ($campus) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }

        if ($keyword) {
            $qb->andWhere('s.nom LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        if ($dateDebut && $dateFin) {
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dateDebut)
                ->setParameter('dateFin', $dateFin);
        }

        if ($organisateur) {
            $qb->join('s.organisateur', 'o')
                ->andWhere('o.username = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }

        if ($inscrit) {
            $qb->join('s.listInscrit', 'p')
                ->andWhere('p = :inscrit')
                ->setParameter('inscrit', $inscrit);
        }


        if ($nonInscrit) {
            $qb->leftJoin('s.listInscrit', 'p')
                ->andWhere(':nonInscrit NOT MEMBER OF s.listInscrit')
                ->setParameter('nonInscrit', $nonInscrit);
        }



        if ($sortiesPassees) {
            $qb->andWhere('s.dateHeureDebut < :now')
                ->setParameter('now', new \DateTime());
        }



        return $qb->getQuery()->getResult();
    }
}
