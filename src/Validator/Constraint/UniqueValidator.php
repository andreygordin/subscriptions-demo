<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueValidator extends ConstraintValidator
{
	public function __construct(
		private EntityManagerInterface $em,
		private PropertyAccessorInterface $propertyAccessor,
	) {
	}

	/**
	 * @param Unique $constraint
	 */
	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof Unique) {
			throw new UnexpectedTypeException($constraint, Unique::class);
		}

		if (is_null($value) || $value === '') {
			return;
		}

		$criteria = [];
		foreach ($constraint->fields as $from => $to) {
			$criteria[$to] = $this->propertyAccessor->getValue($value, $from);
		}

		$repository = $this->em->getRepository($constraint->entityClass);
		if ($repository->count($criteria) !== 0) {
			$violation = $this->context->buildViolation($constraint->message);
			if (!is_null($constraint->atPath)) {
				$violation->atPath($constraint->atPath);
			}
			$violation->addViolation();
		}
	}
}
