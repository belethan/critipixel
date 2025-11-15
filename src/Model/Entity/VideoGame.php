<?php

namespace App\Model\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class VideoGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100)]
    private string $title;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "date_immutable")]
    private DateTimeImmutable $releaseDate;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $test = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $averageRating = null;

    #[ORM\Embedded(class: NumberOfRatingPerValue::class, columnPrefix: false)]
    private NumberOfRatingPerValue $numberOfRatingsPerValue;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'video_game_tags')]
    private Collection $tags;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'videoGame', cascade: ['persist', 'remove'])]
    private Collection $reviews;

    public function __construct()
    {
        $this->numberOfRatingsPerValue = new NumberOfRatingPerValue();
        $this->tags = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->updatedAt = new DateTimeImmutable();
    }

    /* ============================================================
       GETTERS / SETTERS
       ============================================================ */

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getImageName(): ?string { return $this->imageName; }
    public function setImageName(?string $imageName): self { $this->imageName = $imageName; return $this; }

    public function getImageSize(): ?int { return $this->imageSize; }
    public function setImageSize(?int $imageSize): self { $this->imageSize = $imageSize; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getReleaseDate(): DateTimeImmutable { return $this->releaseDate; }
    public function setReleaseDate(DateTimeImmutable $date): self { $this->releaseDate = $date; return $this; }

    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?DateTimeImmutable $date): self { $this->updatedAt = $date; return $this; }

    public function getTest(): ?string { return $this->test; }
    public function setTest(?string $test): self { $this->test = $test; return $this; }

    public function getRating(): ?int { return $this->rating; }
    public function setRating(?int $rating): self { $this->rating = $rating; return $this; }

    public function getAverageRating(): float
    {
        if ($this->reviews->isEmpty()) {
            return 0.0;
        }

        $sum = 0;

        foreach ($this->reviews as $review) {
            $sum += $review->getRating();
        }

        return round($sum / count($this->reviews), 1);
    }
    public function setAverageRating(?int $avg): self { $this->averageRating = $avg; return $this; }

    public function getNumberOfRatingsPerValue(): NumberOfRatingPerValue
    {
        return $this->numberOfRatingsPerValue;
    }

    /* ============================================================
       TAGS (ManyToMany)
       ============================================================ */

    public function getTags(): Collection { return $this->tags; }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    /* ============================================================
       REVIEWS (OneToMany)
       ============================================================ */

    public function getReviews(): Collection { return $this->reviews; }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {

            // Ajout à la collection de reviews
            $this->reviews->add($review);

            // Liaison inverse : Review → VideoGame
            $review->setVideoGame($this);

            // Mise à jour du nombre de votes par valeur
            $rating = $review->getRating();
            if ($rating !== null) {
                $this->numberOfRatingsPerValue->increment($rating);
            }
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getVideoGame() === $this) {
                $review->setVideoGame(null);
            }
        }
        return $this;
    }
}
