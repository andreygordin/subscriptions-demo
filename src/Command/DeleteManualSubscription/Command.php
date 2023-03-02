<?php

declare(strict_types=1);

namespace App\Command\DeleteManualSubscription;

final readonly class Command
{
	public function __construct(
		public mixed $id = null,
	) {
	}
}
