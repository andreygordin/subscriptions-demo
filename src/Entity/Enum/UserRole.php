<?php

declare(strict_types=1);

namespace App\Entity\Enum;

class UserRole
{
	public const MANAGER = 'M';
	public const ADVERTISER = 'A';
	public const WEBMASTER = 'W';
	public const EDITOR = 'E';
	public const AGENT = 'B';
	public const ADVMADNET = 'N';
	public const STATVIEWER = 'S';

	public const VALUES = [
		self::MANAGER,
		self::ADVERTISER,
		self::WEBMASTER,
		self::EDITOR,
		self::AGENT,
		self::ADVMADNET,
		self::STATVIEWER,
	];
}
