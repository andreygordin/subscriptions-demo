<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class Exists extends Constraint
{
	/**
	 * @param class-string<object> $entityClass
	 * @param array<string,mixed> $criteria
	 */
	#[HasNamedArguments]
	public function __construct(
		public string $entityClass,
		public string $field = 'id',
		public array $criteria = [],
		public string $message = 'Entry doesnʼt exist.',
		array $groups = null,
		mixed $payload = null
	) {
		parent::__construct($groups, $payload);
	}
}
