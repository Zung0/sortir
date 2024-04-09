<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\User;
use App\Helpers\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function countParticipants($id)
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(p.id) as participantCount')
            ->leftJoin('s.participants', 'p')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function finSearch(SearchData $search, User $user)
    {

        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 's')
            ->join('p.site', 's');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }
        if (!empty($search->site)) {
            $query = $query
                ->andWhere('s.id IN (:s) ')
                ->setParameter('s', $search->site);

        }
        if (!empty($search->dateMin)) {
            $query = $query
                ->andWhere('p.dateHeureDebut >= :d')
                ->setParameter('d', $search->dateMin);
        }
        if (!empty($search->dateMax)) {
            $query = $query
                ->andWhere('p.dateHeureDebut <= :m')
                ->setParameter('m', $search->dateMax);
        }
        if (!empty($search->sortiesPassees)) {
            $query = $query
                ->andWhere('p.dateHeureDebut <= :dateNow')
                ->setParameter('dateNow', new \DateTime());
        }
        if (!empty($search->isOrganisateur)) {
            $query = $query
                ->andWhere('p.organisateur = :user')
                ->setParameter('user', $user->getId());;
        }
        if (!empty($search->isInscrit)) {
            $query = $query
                ->andWhere(':user MEMBER OF p.participants')
                ->setParameter('user', $user->getId());
        }
        if (!empty($search->isNotInscrit)) {
            $query = $query
                ->andWhere(':user NOT MEMBER OF p.participants')
                ->setParameter('user', $user->getId());
        }
        if (!empty($search->orderBy)) {
            $query = $query
                ->orderBy('p.dateHeureDebut', $search->orderBy);
        }
        return $query->getQuery()->getResult();
    }

}
