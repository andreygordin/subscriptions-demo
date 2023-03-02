<?php

declare(strict_types=1);

namespace App\Messenger\Handler;

use App\Command\CreateManualSubscription;
use App\Command\UpdateManualSubscription;
use Acme\Messenger\Message\ManualSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class ManualSubscriptionHandler
{
	private const OPERATION_ADD = 'add';
	private const OPERATION_EDIT = 'edit';

	public function __construct(
		private CreateManualSubscription\Handler $createManualSubscriptionHandler,
		private UpdateManualSubscription\Handler $updateManualSubscriptionHandler,
		private DenormalizerInterface $denormalizer,
		private EntityManagerInterface $entityManager,
	) {
	}

	public function __invoke(ManualSubscription $message): void
	{
		Assert::oneOf($message->getOperation(), [self::OPERATION_ADD, self::OPERATION_EDIT]);

		$this->entityManager->clear();

		$command = $this->denormalizer->denormalize(
			$message->getData(),
			$message->getOperation() === self::OPERATION_ADD
				? CreateManualSubscription\Command::class
				: UpdateManualSubscription\Command::class
		);

		$handler = $message->getOperation() === self::OPERATION_ADD
			? $this->createManualSubscriptionHandler
			: $this->updateManualSubscriptionHandler;

		$handler->handle($command);
	}
}
