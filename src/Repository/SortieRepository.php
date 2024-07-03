<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\Modeles\FormFilterMainModele;
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

    public function searchSorties(?FormFilterMainModele $formFilter, ?UserInterface $user, string $flag = "-a") : ?array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.etat', 'e')
            ->innerJoin('s.organisateur', 'o')
            ->innerJoin('s.campus', 'c')
            ->innerJoin('s.lieu', 'l')
            ->innerJoin('l.ville', 'v')
            ->leftJoin('s.listInscrit', 'sp');

        $qb->andWhere('e.code != :code')
            ->setParameter('code', 'HIS');

        if ($flag === "-a")
            return $qb->getQuery()->getResult();

        if ($flag !== "-p")
            return null;

        if ($formFilter->getCampus())
            $qb->andWhere('c.id = :campus')->setParameter('campus', $formFilter->getCampus());

        if ($formFilter->getKeyword())
            $qb->andWhere('s.nom LIKE :keyword')->setParameter('keyword', '%' . $formFilter->getKeyword() . '%');

        if ($formFilter->getDateDebut())
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')->setParameter('dateDebut', $formFilter->getDateDebut());

        if ($formFilter->getDateFin())
            $qb->andWhere('s.dateHeureDebut <= :dateFin')->setParameter('dateFin', $formFilter->getDateFin());

        if ($formFilter->getChoiceValue() == "organisateur" && $user)
            $qb->andWhere('o = :user')->setParameter('user', $user);

        if ($formFilter->getChoiceValue() == "inscrit" && $user)
            $qb->andWhere(':user MEMBER OF s.listInscrit')->setParameter('user', $user);

        if ($formFilter->getChoiceValue() == "nonInscrit" && $user)
            $qb->andWhere(':user NOT MEMBER OF s.listInscrit')->setParameter('user', $user);

        if ($formFilter->getSortiesPassees())
            $qb->andWhere('e.code = :codePasse')->setParameter('codePasse', 'PAS');

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

    public function inscriptionOrInvert(int $idSortie, Participant $user, string $flag = "-i") : void
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
