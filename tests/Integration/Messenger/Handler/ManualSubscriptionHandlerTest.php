<?php

declare(strict_types=1);

namespace Test\Integration\Messenger\Handler;

use App\Entity\Enum\ManualSubscriptionStatus;
use App\Messenger\Handler\ManualSubscriptionHandler;
use Acme\Messenger\Message\ManualSubscription;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class ManualSubscriptionHandlerTest extends AbstractHandlerTest
{
	protected function getMessageClassName(): string
	{
		return ManualSubscription::class;
	}

	protected function getHandlerClassName(): string
	{
		return ManualSubscriptionHandler::class;
	}

	protected function getTableName(): string
	{
		return 'manual_subscription';
	}

	protected function handleProvider(): array
	{
		return [
			'create a manual subscription' => [
				'messageOperation' => 'add',
				'messageData' => [
					'author_id' => 1,
					'channel_id' => 1,
					'offer_id' => 2,
					'is_active' => true,
					'status' => ManualSubscriptionStatus::SUBSCRIBED,
				],
				'exceptionToExpect' => null,
				'dbCriteria' => [
					'author_id' => 1,
					'channel_id' => 1,
					'offer_id' => 2,
					'is_active' => 1,
					'status' => ManualSubscriptionStatus::SUBSCRIBED,
				],
			],
			'create a manual subscription that already exists' => [
				'messageOperation' => 'add',
				'messageData' => [
					'author_id' => 1,
					'channel_id' => 1,
					'offer_id' => 1,
					'is_active' => true,
					'status' => ManualSubscriptionStatus::SUBSCRIBED,
				],
				'exceptionToExpect' => UniqueConstraintViolationException::class,
			],
			'edit a manual subscription' => [
				'messageOperation' => 'edit',
				'messageData' => [
					'author_id' => 1,
					'channel_id' => 1,
					'offer_id' => 1,
					'is_active' => false,
					'status' => ManualSubscriptionStatus::UNSUBSCRIBED,
				],
				'exceptionToExpect' => null,
				'dbCriteria' => [
					'author_id' => 1,
					'channel_id' => 1,
					'offer_id' => 1,
					'is_active' => 0,
					'status' => ManualSubscriptionStatus::UNSUBSCRIBED,
				],
			],
		];
	}
}
