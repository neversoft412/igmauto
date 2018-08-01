<?php

namespace Blog\Model;

interface PostRepositoryInterface
{
    /**
     * Return a set of all blog posts that we can iterate over.
     *
     * Each entry should be a Post instance.
     *
     * @return Post[]
     */
    public function findAllPosts();

    /**
     * Return a single blog write.
     *
     * @param  int $id Identifier of the write to return.
     * @return Post
     */
    public function findPost($id);
}
