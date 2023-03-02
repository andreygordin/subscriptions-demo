<?php

declare(strict_types=1);

namespace Test\Integration\Messenger\Handler;

use Acme\Messenger\Message\Message;
use Codeception\Attribute\DataProvider;
use Codeception\Module\Symfony;
use Codeception\Test\Unit;
use Test\Support\IntegrationTester;

abstract class AbstractHandlerTest extends Unit
{
	protected IntegrationTester $tester;
	protected Symfony $symfony;

	protected function _before()
	{
		$this->symfony = $this->getModule('Symfony');
	}

	#[DataProvider('handleProvider')]
	public function testHandle(
		string $messageOperation,
		array $messageData,
		?string $exceptionToExpect,
		?array $dbCriteria = null
	): void
	{
		/** @var Message $message */
		$message = (new ($this->getMessageClassName())())
			->setOperation($messageOperation)
			->setData($messageData);

		/** @var callable $handler */
		$handler = $this->symfony->grabService($this->getHandlerClassName());

		if (!is_null($exceptionToExpect)) {
			$this->expectException($exceptionToExpect);
		}

		$handler($message);

		$this->tester->seeInDatabase($this->getTableName(), $dbCriteria);
	}

	abstract protected function getMessageClassName(): string;

	abstract protected function getHandlerClassName(): string;

	abstract protected function getTableName(): string;

	abstract protected function handleProvider(): array;
}
