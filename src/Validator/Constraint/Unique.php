<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class Unique extends Constraint
{
	/**
	 * @param class-string<object> $entityClass
	 * @param array<string,string> $fields
	 */
	#[HasNamedArguments]
	public function __construct(
		public string $entityClass,
		public array $fields,
		public ?string $atPath = null,
		public string $message = 'Entry already exists.',
		array $groups = null,
		mixed $payload = null
	) {
		parent::__construct($groups, $payload);
	}

	public function getTargets(): string
	{
		return parent::CLASS_CONSTRAINT;
	}
}
