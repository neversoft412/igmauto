<?php

namespace Blog\Model;

interface PostCommandInterface
{
    /**
     * Persist a new write in the system.
     *
     * @param Post $post The write to insert; may or may not have an identifier.
     * @return Post The inserted write, with identifier.
     */
    public function insertPost(Post $post);

    /**
     * Update an existing write in the system.
     *
     * @param Post $post The write to update; must have an identifier.
     * @return Post The updated write.
     */
    public function updatePost(Post $post);

    /**
     * Delete a write from the system.
     *
     * @param Post $post The write to delete.
     * @return bool
     */
    public function deletePost(Post $post);
}
