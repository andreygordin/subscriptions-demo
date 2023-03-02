<?php

declare(strict_types=1);

namespace App\Command\UpdateManualSubscription;

use App\Entity\ManualSubscription;
use Doctrine\ORM\EntityManagerInterface;

final class Handler
{
	public function __construct(
		private EntityManagerInterface $em,
	) {
	}

	public function handle(Command $command): void
	{
		/** @var ManualSubscription|null $manualSubscription */
		$manualSubscription = $this->em->getRepository(ManualSubscription::class)->find((int)$command->id);
		if (is_null($manualSubscription)) {
			throw new ManualSubscriptionNotFoundException();
		}

		if (!is_null($command->status)) {
			$manualSubscription->setStatus((int)$command->status);
		}

		if (!is_null($command->isActive)) {
			filter_var($command->isActive, FILTER_VALIDATE_BOOLEAN)
				? $manualSubscription->activate()
				: $manualSubscription->deactivate();
		}

		$this->em->persist($manualSubscription);
		$this->em->flush();
	}
}
