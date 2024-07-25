<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    'slug',
    message: 'Ce slug existe déjà'
)]
class Category
{
    use CategoryTagTrait;
    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'categories')]
    #[ORM\JoinTable(name: 'categories_posts')]
    private Collection $posts;

    public function __toString()
    {
        return $this->name;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)){
            $this->posts[] = $post;
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }

}
