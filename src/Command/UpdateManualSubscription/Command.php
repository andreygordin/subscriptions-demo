<?php

declare(strict_types=1);

namespace App\Command\UpdateManualSubscription;

use App\Entity\Enum\ManualSubscriptionStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'UpdateManualSubscriptionCommand', required: ['status'])]
final readonly class Command
{
	public function __construct(
		public mixed $id = null,

		#[Assert\Type('bool')]
		#[OA\Property(type: 'boolean')]
		public mixed $isActive = null,

		#[Assert\Choice(choices: ManualSubscriptionStatus::VALUES)]
		#[OA\Property(
			type: 'integer',
			description: ManualSubscriptionStatus::UNSUBSCRIBED . ' - unsubscribed, '
			. ManualSubscriptionStatus::SUBSCRIBED . ' - subscribed.',
			enum: ManualSubscriptionStatus::VALUES,
		)]
		public mixed $status = null,
	) {
	}
}
