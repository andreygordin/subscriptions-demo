<?php

declare(strict_types=1);

namespace App\Query\GetManualSubscriptions;

use App\Entity\Enum\ManualSubscriptionStatus;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'GetManualSubscriptionsResult')]
final readonly class ManualSubscription
{
	public function __construct(
		#[OA\Property]
		public int $id,
		#[OA\Property]
		public int $authorId,
		#[OA\Property]
		public int $channelId,
		#[OA\Property]
		public int $offerId,
		#[OA\Property]
		public bool $isActive,
		#[OA\Property(enum: ManualSubscriptionStatus::VALUES)]
		public int $status,
	) {
	}
}
