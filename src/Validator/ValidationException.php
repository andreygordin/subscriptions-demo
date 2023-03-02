<?php

declare(strict_types=1);

namespace App\Validator;

use LogicException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ValidationException extends LogicException
{
	public function __construct(
		private ConstraintViolationListInterface $violations,
		string $message = '',
		int $code = 0,
		Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}

	public function getViolations(): ConstraintViolationListInterface
	{
		return $this->violations;
	}
}
