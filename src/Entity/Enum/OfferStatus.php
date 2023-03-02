<?php

declare(strict_types=1);

namespace App\Entity\Enum;

class OfferStatus
{
	public const STOPPED = 0;
	public const WORKING = 1;
	public const TESTING = 2;
	public const STOPPING = 3;

	public const VALUES = [
		self::STOPPED,
		self::WORKING,
		self::TESTING,
		self::STOPPING,
	];
}
