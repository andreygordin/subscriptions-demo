<?php

declare(strict_types=1);

namespace App\Command\CreateManualSubscription;

use App\Entity\Channel;
use App\Entity\ManualSubscription;
use App\Entity\Offer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class Handler
{
	public function __construct(
		private EntityManagerInterface $em,
	) {
	}

	public function handle(Command $command): int
	{
		/** @var User|null $author */
		$author = $this->em->getRepository(User::class)->find((int)$command->authorId);

		/** @var Channel|null $channel */
		$channel = $this->em->getRepository(Channel::class)->find((int)$command->channelId);

		/** @var Offer|null $offer */
		$offer = $this->em->getRepository(Offer::class)->find((int)$command->offerId);

		$manualSubscription = new ManualSubscription(
			author:   $author,
			channel:  $channel,
			offer:    $offer,
			status:   (int)$command->status,
			isActive: (bool)$command->isActive,
		);

		$this->em->persist($manualSubscription);
		$this->em->flush();

		return $manualSubscription->getId();
	}
}
