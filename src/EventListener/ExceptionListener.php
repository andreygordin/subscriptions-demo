<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Validator\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ExceptionListener
{
	public function onKernelException(ExceptionEvent $event): void
	{
		$exception = $event->getThrowable();

		if ($exception instanceof HttpExceptionInterface) {
			$event->setResponse(
				new JsonResponse(
					['error' => $exception->getMessage()],
					$exception->getStatusCode()
				)
			);
		}

		if ($exception instanceof ValidationException) {
			$errors = [];
			foreach ($exception->getViolations() as $violation) {
				$errors[$violation->getPropertyPath()] = $violation->getMessage();
			}
			$event->setResponse(
				new JsonResponse(
					['errors' => $errors],
					Response::HTTP_UNPROCESSABLE_ENTITY
				)
			);
		}
	}
}
