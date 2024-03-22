<?php

namespace App\Entity;

use App\Repository\NewspaperRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewspaperRepository::class)]
class Newspaper
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'newspapers')]
    private Collection $articles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $thumbnailLetter = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $ciColor = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        // sort articles by date
        $iterator = $this->articles->getIterator();
        $iterator->uasort(function (Article $a, Article $b) {
            return $a->getDate() <=> $b->getDate();
        });
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addNewspaper($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeNewspaper($this);
        }

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getThumbnailLetter(): ?string
    {
        return $this->thumbnailLetter;
    }

    public function setThumbnailLetter(?string $thumbnailLetter): self
    {
        $this->thumbnailLetter = $thumbnailLetter;

        return $this;
    }

    public function getCiColor(): ?string
    {
        return $this->ciColor;
    }

    public function setCiColor(?string $ciColor): self
    {
        $this->ciColor = $ciColor;

        return $this;
    }
}
