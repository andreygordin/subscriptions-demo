<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ExistsValidator extends ConstraintValidator
{
	public function __construct(
		private EntityManagerInterface $em,
	) {
	}

	/**
	 * @param Exists $constraint
	 */
	public function validate(mixed $value, Constraint $constraint): void
	{
		if (!$constraint instanceof Exists) {
			throw new UnexpectedTypeException($constraint, Exists::class);
		}

		if (is_null($value) || $value === '') {
			return;
		}

		$repository = $this->em->getRepository($constraint->entityClass);

		$criteria = array_merge(
			$constraint->criteria,
			[$constraint->field => $value],
		);

		if ($repository->count($criteria) === 0) {
			$this->context
				->buildViolation($constraint->message)
				->addViolation();
		}
	}
}
