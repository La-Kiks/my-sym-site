<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;


/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginatorInterface,
    )
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param int $page
     * @param Category|null $category
     * @param Tag|null $tag
     * @return PaginationInterface
     */
    public function findPublished(
        int $page,
        ?Category $category = null,
        ?Tag $tag = null
    ): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.state LIKE :state')
            ->setParameter('state', '%STATE_PUBLISHED%')
            ->orderBy('p.createdAt', 'DESC');

        if(isset($category)){
            $data = $data
                ->join('p.categories', 'c')
                ->andWhere(':category IN (c)')
                ->setParameter('category', $category);
        }

        if(isset($tag)){
            $data = $data
                ->join('p.tags', 't')
                ->andWhere(':tag IN (t)')
                ->setParameter('tag', $tag);
        }

        $data->getQuery()
            ->getResult();

        return $this->paginatorInterface->paginate($data, $page, 9);
    }

    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.state LIKE :state')
            ->setParameter('state', '%STATE_PUBLISHED');

        if(!empty($searchData->q)){
            $data = $data
                ->leftJoin('p.tags', 't')
                ->andWhere('p.title LIKE :q  OR t.name LIKE :q OR p.content LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        if(!empty($searchData->categories)){
            $data = $data
                ->leftJoin('p.categories', 'c')
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $searchData->categories);
        }

        $data = $data
            ->getQuery()
            ->getResult();

        return $this->paginatorInterface->paginate($data, $searchData->page, 9);
    }

    public function findPostByUser(
        int $page,
        User $user
    ): PaginationInterface
    {
        $userUuid = $user->getId();

        $qb = $this->createQueryBuilder('p')
            ->where('p.user = :userUuid')
            ->setParameter('userUuid', $userUuid, UuidType::NAME)
            ->getQuery()
            ->getResult();

            return $this->paginatorInterface->paginate($qb, $page, 15);
    }

}
