<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\TrustLevel;
use App\Entity\Enum\UserRole;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
class User
{
	public function __construct(
		#[ORM\Id]
		#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
		private int $id,

		#[ORM\Column(type: Types::BOOLEAN, nullable: false)]
		private bool $isActive,

		#[ORM\Column(type: Types::STRING, length: 2, nullable: false)]
		private string $role,

		#[ORM\Column(type: Types::SMALLINT, nullable: true)]
		private ?int $trustLevel,
	) {
		Assert::oneOf($role, UserRole::VALUES);
		self::assertTrustLevel($trustLevel, $role);
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function isActive(): bool
	{
		return $this->isActive;
	}

	public function getRole(): string
	{
		return $this->role;
	}

	public function getTrustLevel(): ?int
	{
		return $this->trustLevel;
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

	public function setRole(string $role): self
	{
		Assert::oneOf($role, UserRole::VALUES);
		self::assertTrustLevel($this->trustLevel, $role);
		$this->role = $role;
		return $this;
	}

	public function setTrustLevel(?int $trustLevel): self
	{
		self::assertTrustLevel($trustLevel, $this->role);
		$this->trustLevel = $trustLevel;
		return $this;
	}

	private static function assertTrustLevel(?int $trustLevel, string $role): void
	{
		if ($role === UserRole::WEBMASTER) {
			Assert::oneOf($trustLevel, TrustLevel::VALUES);
		} else {
			Assert::null($trustLevel);
		}
	}
}
