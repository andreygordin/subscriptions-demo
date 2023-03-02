<?php

declare(strict_types=1);

namespace App\Entity\Enum;

class TrustLevel
{
	public const BANNED = -1;
	public const BASE = 1;
	public const ADVANCED = 2;
	public const VIP = 3;
	public const PLATINUM = 4;
	public const LIMITED = 5;
	public const EXCLUSIVE = 6;
	public const HIDDEN = 7;
	public const TEST = 8;

	public const VALUES = [
		self::BANNED,
		self::BASE,
		self::ADVANCED,
		self::VIP,
		self::PLATINUM,
		self::LIMITED,
		self::EXCLUSIVE,
		self::HIDDEN,
		self::TEST,
	];
}
