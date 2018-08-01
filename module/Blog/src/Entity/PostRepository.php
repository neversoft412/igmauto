<?php

namespace Blog\Entity;

use Doctrine\ORM\EntityRepository;
use Tag\Entity\Tag;

/**
 * PostRe
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    public function getArrayTags($postId)
    {
        // $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        // $queryBuilder->select('Tag.id', 'Tag.name')
        //     ->from(Tag::class, 'Tag')
        //     ->join('post_tag', 'PostTag')
        //     ->where('PostTag.post_id = :post_id')
        //     ->setParameter('post_id', $postId);
        $sql = 'SELECT Tag.id, Tag.name FROM ' . Tag::class . ' AS Tag
            JOIN post_tag PostTag ON Tag.id = PostTag.tag_id 
            WHERE PostTag.post_id = :postId';

        $queryBuilder = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('post_id', $postId);

        return $queryBuilder->getScalarResult();
    }
}