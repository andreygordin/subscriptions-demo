<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(columns: ['user_id'], name: 'IDX_user_id')]
class Channel
{
	public function __construct(
		#[ORM\Id]
		#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
		private int $id,

		#[ORM\ManyToOne(targetEntity: User::class)]
		#[ORM\JoinColumn(nullable: false)]
		private User $user,

		#[ORM\Column(type: Types::BOOLEAN, nullable: false)]
		private bool $isActive,
	) {
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUser(): User
	{
		return $this->user;
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
}
