<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $term
     * @return Category[]
     */
    public function search(string $term):array{
            //creamos un creador de consultas con el alias que queramos y añadimos una condicion where que filtrara por nombre
        //el nombre sera lo que me pasen por parametro pero en bindParam para evitar inyeccionesSQL
//        return $this ->createQueryBuilder('category')->andWhere('category.name LIKE :searchTerm')->setParameter('searchTerm', '%'.$term.'%')
//            ->getQuery()->getResult();
        $termList = explode(' ', $term);
        $qb = $this->addOrderByCategoryName();
        return $this->addGroupByCategory($qb)
            ->andWhere('category.name LIKE :searchTerm OR category.name IN (:termList) OR category.iconKey LIKE :searchTerm OR fortuneCookie.fortune LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$term.'%')
            ->setParameter('termList', $termList)
            ->getQuery()
            ->getResult();
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findAllOrdered(): array{
        //creamos un creador de consultas con un alias que puede ser el que quieras, criteria es una clase especial de doctrine para ordenar
        //esto me genera la consulta, la consulta seria SELECT CATEGORY from App\Entity\Category as category order by category.name
        $qb = $this->addGroupByCategory()
            ->addOrderBy('category.name', Criteria::DESC);

        //el App\Entity\Category  lo sabe porque esta clase hereda de ServiceEntityRepository y en el @extends le hemos dicho que el
        //ServiceEntityRepository es de tipo Category, tambien en el constructor le hemos dicho que estamos inicializando la entidad Category
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findWithFortunesJoin(int $id): ?Category{
        return $this->addFortuneCookieJoinAndSelect()
            ->andWhere('category.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult();
    }
    private function addFortuneCookieJoinAndSelect(QueryBuilder $qb=null): QueryBuilder{
        return ($qb ?? $this->createQueryBuilder('category'))->addSelect('fortuneCookie')
            ->leftJoin('category.fortuneCookies', 'fortuneCookie');
    }
    private function addOrderByCategoryName(QueryBuilder $qb = null): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('category'))
            ->addOrderBy('category.name', Criteria::DESC);
    }
    private function addGroupByCategory(QueryBuilder $qb = null): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('category'))
            ->addSelect('COUNT(fortuneCookie.id) AS fortuneCookiesTotal')
            ->leftJoin('category.fortuneCookies', 'fortuneCookie')
            ->addGroupBy('category.id');
    }


//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
