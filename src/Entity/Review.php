<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?int $rate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTime $posted_date = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'review', cascade: ['persist', 'remove'])]
    private ?ReviewResponse $reviewResponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getposted_date(): ?DateTime
    {
        return $this->posted_date;
    }

    public function setPostedDate(DateTime $posted_date): static
    {
        $this->posted_date = $posted_date;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getReviewResponse(): ?ReviewResponse
    {
        return $this->reviewResponse;
    }

    public function setReviewResponse(ReviewResponse $reviewResponse): static
    {
        // set the owning side of the relation if necessary
        if ($reviewResponse->getReview() !== $this) {
            $reviewResponse->setReview($this);
        }

        $this->reviewResponse = $reviewResponse;

        return $this;
    }
}
