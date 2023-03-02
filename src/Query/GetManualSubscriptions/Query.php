<?php

declare(strict_types=1);

namespace App\Query\GetManualSubscriptions;

use App\Entity\Enum\ManualSubscriptionStatus;
use App\Service\OpenApi\Attribute;
use OpenApi\Attributes as OA;

#[Attribute\ParametersSchema]
final readonly class Query
{
	/**
	 * @param string[] $ids
	 * @param string[] $authorIds
	 * @param string[] $channelIds
	 * @param string[] $offerIds
	 * @param string[] $statuses
	 */
	public function __construct(
		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
		public mixed $ids = null,

		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
		public mixed $authorIds = null,

		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
		public mixed $channelIds = null,

		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')))]
		public mixed $offerIds = null,

		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'boolean'))]
		public mixed $isActive = null,

		#[OA\Parameter(
			description: ManualSubscriptionStatus::UNSUBSCRIBED . ' - unsubscribed, '
			. ManualSubscriptionStatus::SUBSCRIBED . ' - subscribed.',
			in: 'query',
			schema: new OA\Schema(
				type:  'array',
				items: new OA\Items(enum: ManualSubscriptionStatus::VALUES)
			)
		)]
		public mixed $statuses = null,

		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'integer'))]
		public mixed $page = null,

		#[OA\Parameter(in: 'query', schema: new OA\Schema(type: 'integer'))]
		public mixed $perPage = null,
	) {
	}
}
