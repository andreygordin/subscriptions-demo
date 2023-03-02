<?php

declare(strict_types=1);

namespace App\Command\CreateManualSubscription;

use App\Entity\Channel;
use App\Entity\Enum\ManualSubscriptionStatus;
use App\Entity\Enum\UserRole;
use App\Entity\ManualSubscription;
use App\Entity\Offer;
use App\Entity\User;
use App\Validator\Constraint;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[Constraint\Unique(
	entityClass: ManualSubscription::class,
	fields: [
		'channelId' => 'channel',
		'offerId' => 'offer'
	]
)]
#[OA\Schema(schema: 'CreateManualSubscriptionCommand', required: ['authorId', 'channelId', 'offerId', 'status'])]
final readonly class Command
{
	public function __construct(
		#[Assert\NotBlank]
		#[Assert\Type('int')]
		#[Constraint\Exists(
			entityClass: User::class,
			criteria: ['role' => UserRole::WEBMASTER],
			message: 'Webmaster doesnʼt exist.'
		)]
		#[OA\Property(type: 'integer')]
		public mixed $authorId = null,

		#[Assert\NotBlank]
		#[Assert\Type('int')]
		#[Constraint\Exists(entityClass: Channel::class, message: 'Channel doesnʼt exist.')]
		#[OA\Property(type: 'integer')]
		public mixed $channelId = null,

		#[Assert\NotBlank]
		#[Assert\Type('int')]
		#[Constraint\Exists(entityClass: Offer::class, message: 'Offer doesnʼt exist.')]
		#[OA\Property(type: 'integer')]
		public mixed $offerId = null,

		#[Assert\NotBlank]
		#[Assert\Type('bool')]
		#[OA\Property(type: 'boolean')]
		public mixed $isActive = null,

		#[Assert\NotBlank]
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
