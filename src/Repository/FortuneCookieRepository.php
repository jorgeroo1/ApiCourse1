<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\FortuneCookie;
use App\Model\CategoryFortuneStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FortuneCookie>
 *
 * @method FortuneCookie|null find($id, $lockMode = null, $lockVersion = null)
 * @method FortuneCookie|null findOneBy(array $criteria, array $orderBy = null)
 * @method FortuneCookie[]    findAll()
 * @method FortuneCookie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FortuneCookie::class);
    }

    public function save(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countFortuneCookie(Category $category):int{
        //al poner el select cuando se ejecute el getResult solo me devolvera el contenido del select
        //como el resultado del select va a ser un numero, en vez de devolver el getResult que devuelve un objeto
        //de tipo Category devolvemos el contenido del select que en este caso es un int eso hace ele getSingleScalar
        //me está devolviendo el valor que le metamos en el select de la categoria seleccionada
        return $this->createQueryBuilder('fortuneCookie')->select('sum(fortuneCookie.numberPrinted)')->andWhere('fortuneCookie.category= :category')
            ->setParameter('category',$category)->getQuery()->getSingleScalarResult();
    }
    //como en este caso queremos devolver mas de un dato

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countFortuneCookieAvg(Category $category):array{
        return $this->createQueryBuilder('fortuneCookie')
            ->select('AVG(fortuneCookie.numberPrinted) fortuneAverage','SUM(fortuneCookie.numberPrinted) fortunePrinted')
//            ->addSelect('category.name AS categoryName')
//            ->groupBy('category.name')
            ->innerJoin('fortuneCookie.category', 'category')
            ->andWhere('fortuneCookie.category = :category')
            ->setParameter('category', $category)->getQuery()->getSingleResult();
        //la diferencia principal es que getResult me devuelve un array y por ejemplo en showCategory.twig deberiamos
        //acceder a {{ result[0].fortunePrinted}} para acceder a la suma y si pones getSingleResult te dan los valores planos
        // y entonces puedes a fortunePrinted como {{ result.fortunePrinted }}
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countFortuneCookieModelAvg(Category $category){
        return $this->createQueryBuilder('fortuneCookie')
            ->select(sprintf('NEW %s(SUM(fortuneCookie.numberPrinted), AVG(fortuneCookie.numberPrinted))', CategoryFortuneStats::class))
            ->innerJoin('fortuneCookie.category', 'category')
            ->andWhere('fortuneCookie.category = :category')
            ->setParameter('category', $category)->getQuery()->getSingleResult();
    }
    public static function createFortuneCookiesStillInProductionCriteria(): Criteria
    {
        //La clase Criteria es similar al QueryBuilder, el create es como el createQueryBuilder
        //metemos una condicion where y el expr nos permite crear la clausula Where con metodos como
        //contains, in o eq="igual a", esto quiere decir que el campo discontinued sea igual a true
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('discontinued', false));
    }

//    /**
//     * @return FortuneCookie[] Returns an array of FortuneCookie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FortuneCookie
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
