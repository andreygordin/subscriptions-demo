<?php

declare(strict_types=1);

namespace App\Entity\Enum;

class ManualSubscriptionStatus
{
	public const UNSUBSCRIBED = 0;
	public const SUBSCRIBED = 1;

	public const VALUES = [
		self::UNSUBSCRIBED,
		self::SUBSCRIBED,
	];
}
