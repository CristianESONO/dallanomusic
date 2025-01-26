<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


 /**
     * Récupérer les produits avec des filtres de tri.
     */
    public function findByFiltersAndSort(?string $category, ?string $sortBy, ?float $minPrice, ?float $maxPrice): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
    
        // Filtrage par catégorie
        if ($category) {
            $queryBuilder->andWhere('p.category = :category')
                         ->setParameter('category', $category);
        }
    
        // Filtrage par prix (min et max)
        if ($minPrice !== null) {
            $queryBuilder->andWhere('p.price >= :minPrice')
                         ->setParameter('minPrice', $minPrice);
        }
    
        if ($maxPrice !== null) {
            $queryBuilder->andWhere('p.price <= :maxPrice')
                         ->setParameter('maxPrice', $maxPrice);
        }
    
        // Critères de tri
        switch ($sortBy) {
            case 'price_asc':
                $queryBuilder->orderBy('p.price', 'ASC');
                break;
            case 'price_desc':
                $queryBuilder->orderBy('p.price', 'DESC');
                break;
            case 'name':
            default:
                $queryBuilder->orderBy('p.name', 'ASC'); // Tri par défaut par nom
                break;
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
    
}
