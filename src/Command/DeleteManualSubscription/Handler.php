<?php

declare(strict_types=1);

namespace App\Command\DeleteManualSubscription;

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

		$this->em->remove($manualSubscription);
		$this->em->flush();
	}
}
