<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\ManualSubscriptionStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Index(columns: ['author_id'], name: 'IDX_author_id')]
#[ORM\Index(columns: ['channel_id'], name: 'IDX_channel_id')]
#[ORM\Index(columns: ['offer_id'], name: 'IDX_offer_id')]
#[ORM\UniqueConstraint(name: 'UQ_manual_subscription_channel_id_offer_id', columns: ['channel_id', 'offer_id'])]
class ManualSubscription
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
	private ?int $id = null;

	public function __construct(
		#[ORM\ManyToOne(targetEntity: User::class)]
		#[ORM\JoinColumn(nullable: false)]
		private User $author,

		#[ORM\ManyToOne(targetEntity: Channel::class)]
		#[ORM\JoinColumn(nullable: false)]
		private Channel $channel,

		#[ORM\ManyToOne(targetEntity: Offer::class)]
		#[ORM\JoinColumn(nullable: false)]
		private Offer $offer,

		#[ORM\Column(type: Types::BOOLEAN, nullable: false)]
		private bool $isActive,

		#[ORM\Column(type: Types::SMALLINT, nullable: false)]
		private int $status,
	) {
		Assert::oneOf($status, ManualSubscriptionStatus::VALUES);
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getAuthor(): User
	{
		return $this->author;
	}

	public function getChannel(): Channel
	{
		return $this->channel;
	}

	public function getOffer(): Offer
	{
		return $this->offer;
	}

	public function isActive(): bool
	{
		return $this->isActive;
	}

	public function activate(): self
	{
		$this->isActive = true;
		return $this;
	}

	public function deactivate(): self
	{
		$this->isActive = false;
		return $this;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function setStatus(int $status): self
	{
		Assert::oneOf($status, ManualSubscriptionStatus::VALUES);
		$this->status = $status;
		return $this;
	}
}
