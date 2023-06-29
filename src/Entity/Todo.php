<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Controller\TodosController;
use App\Repository\TodoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(operations: [
    new Delete(uriTemplate: '/api/todos/delete/done', controller: TodosController::class, name: 'delete_done')
])]
#[ORM\Entity(repositoryClass: TodoRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['todo:read']],
    order: ['id' => 'DESC'],
)]
class Todo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['todo:read', 'tag:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['todo:read', 'tag:read'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('todo:read')]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['todo:read'])]
    private ?bool $done = false;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'todos', cascade: ["persist"])]
    #[Groups(['todo:read'])]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'todos')]
    #[Groups(['todo:read'])]
    private ?User $creator = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(?bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addTodo($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeTodo($this);
        }

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }
}
