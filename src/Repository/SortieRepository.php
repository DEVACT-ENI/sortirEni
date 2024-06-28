<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
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


        $qb->AndWhere('s.etat != :etat')
            ->setParameter('etat', '7');
        return $qb->getQuery()->getResult();
    }

    /**
     * Cette méthode est utilisée pour gérer l'inscription ou la désinscription d'un participant à une sortie.
     * Elle prend trois paramètres : l'id de la sortie, l'objet participant et un indicateur.
     *
     * @param int $idSortie L'id de la sortie à laquelle le participant veut s'inscrire ou se désinscrire.
     * @param Participant $user Le participant qui veut s'inscrire ou se désinscrire à la sortie.
     * @param string $flag Un indicateur pour indiquer si le participant veut s'inscrire ("-i") ou se désinscrire ("-d"). La valeur par défaut est "-i".
     *
     * @return void Cette méthode ne retourne rien. Elle modifie directement la sortie dans la base de données.
     */

    public function inscription(int $idSortie, Participant $user, string $flag = "-i") : void
    {
        $sortie = $this->find($idSortie);
        $flag === "-i" ? $sortie->addListInscrit($user) : null;
        $flag === "-d" ? $sortie->removeListInscrit($user) : null;
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();
    }

    public function save(Sortie $sortie)
    {
        $this->entityManager->persist($sortie);
        $this->entityManager->flush();
    }
}
