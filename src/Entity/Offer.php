<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\OfferStatus;
use App\Entity\Enum\TrustLevel;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Index(columns: ['advertiser_id'], name: 'IDX_advertiser_id')]
class Offer
{
	public function __construct(
		#[ORM\Id]
		#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
		private int $id,

		#[ORM\ManyToOne(targetEntity: User::class)]
		#[ORM\JoinColumn(nullable: false)]
		private User $advertiser,

		#[ORM\Column(type: Types::SMALLINT, nullable: false)]
		private int $trustLevel,

		#[ORM\Column(type: Types::SMALLINT, nullable: false)]
		private int $status,
	) {
		Assert::oneOf($trustLevel, TrustLevel::VALUES);
		Assert::oneOf($status, OfferStatus::VALUES);
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getAdvertiser(): User
	{
		return $this->advertiser;
	}

	public function getTrustLevel(): int
	{
		return $this->trustLevel;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function setAdvertiser(User $advertiser): self
	{
		$this->advertiser = $advertiser;
		return $this;
	}

	public function setTrustLevel(int $trustLevel): self
	{
		Assert::oneOf($trustLevel, TrustLevel::VALUES);
		$this->trustLevel = $trustLevel;
		return $this;
	}

	public function setStatus(int $status): self
	{
		Assert::oneOf($status, OfferStatus::VALUES);
		$this->status = $status;
		return $this;
	}
}
