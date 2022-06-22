<?php

namespace App\Repository;

use App\Entity\Recurso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recurso>
 *
 * @method Recurso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recurso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recurso[]    findAll()
 * @method Recurso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recurso::class);
    }

    public function add(Recurso $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recurso $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Recurso[] Returns an array of Recurso objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recurso
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByBusqueda(string $busquedaQuery)
    {
        return $this->createQueryBuilder('r')
            ->where('r.nombre LIKE :param')
            ->setParameter('param', '%' . $busquedaQuery . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFicheros()
    {
        return $this->createQueryBuilder('r')
            ->where('r.fichero = true')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUsuario($usuario)
    {
        return $this->createQueryBuilder('r')
            ->where('r.propietario = :usuario')
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByFicherosAndUsuario($usuario)
    {
        return $this->createQueryBuilder('r')
            ->where('r.propietario = :usuario and r.fichero = true')
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByFichero(mixed $recurso)
    {
        return $this->createQueryBuilder('r')
            ->where('r.id = :recurso and r.fichero = true')
            ->setParameter('recurso', $recurso)
            ->getQuery()
            ->getResult()
        ;
    }
}
