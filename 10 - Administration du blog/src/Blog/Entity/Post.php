<?php

namespace App\Blog\Entity;

class Post
{
    public int $id;
    public string $name;
    public string $slug;
    public string $content;
    public \DateTime $created_at;
    public ?\DateTime $updated_at;
    public int $category_id;

    public function __construct(
        int $id,
        string $name,
        string $slug,
        string $content,
        string $created_at,
        ?string $updated_at,
        int $category_id
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->content = $content;
        $this->created_at = new \DateTime($created_at);
        $this->updated_at = $updated_at ? new \DateTime($updated_at) : null;
        $this->category_id = $category_id;
    }
}
