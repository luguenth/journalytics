<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // date should be daten and time
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: Journalist::class, inversedBy: 'articles')]
    private Collection $journalists;

    #[ORM\ManyToMany(targetEntity: Newspaper::class, inversedBy: 'articles')]
    private Collection $newspapers;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->journalists = new ArrayCollection();
        $this->newspapers = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Journalist>
     */
    public function getJournalists(): Collection
    {
        return $this->journalists;
    }

    public function addJournalist(Journalist $journalist): self
    {
        if (!$this->journalists->contains($journalist)) {
            $this->journalists->add($journalist);
        }

        return $this;
    }

    public function removeJournalist(Journalist $journalist): self
    {
        $this->journalists->removeElement($journalist);

        return $this;
    }

    /**
     * @return Collection<int, Newspaper>
     */
    public function getNewspapers(): Collection
    {
        return $this->newspapers;
    }

    public function addNewspaper(Newspaper $newspaper): self
    {
        if (!$this->newspapers->contains($newspaper)) {
            $this->newspapers->add($newspaper);
        }

        return $this;
    }

    public function removeNewspaper(Newspaper $newspaper): self
    {
        $this->newspapers->removeElement($newspaper);

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

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
}
