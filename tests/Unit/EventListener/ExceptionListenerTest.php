<?php

declare(strict_types=1);

namespace Test\Unit\EventListener;

use App\EventListener\ExceptionListener;
use App\Validator\ValidationException;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Error;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Test\Support\UnitTester;
use Throwable;

class ExceptionListenerTest extends Unit
{
	protected UnitTester $tester;

	#[DataProvider('responsesProvider')]
	public function testResponses(Throwable $exception, int $code, string $content): void
	{
		$exceptionListener = new ExceptionListener();
		$defaultResponse = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);

		$exceptionEvent = new ExceptionEvent(
			$this->makeEmpty(HttpKernelInterface::class),
			$this->makeEmpty(Request::class),
			HttpKernelInterface::MAIN_REQUEST,
			$exception
		);
		$exceptionEvent->setResponse($defaultResponse);
		$exceptionListener->onKernelException($exceptionEvent);

		$response = $exceptionEvent->getResponse();
		$this->assertEquals($response->getStatusCode(), $code);
		$this->assertEquals($response->getContent(), $content);
	}

	private function responsesProvider(): array
	{
		return [
			[
				new RuntimeException('Runtime error.'),
				Response::HTTP_INTERNAL_SERVER_ERROR,
				''
			],
			[
				new Error('Error.'),
				Response::HTTP_INTERNAL_SERVER_ERROR,
				''
			],
			[
				new NotFoundHttpException('Not found.'),
				Response::HTTP_NOT_FOUND,
				json_encode(['error' => 'Not found.'])
			],
			[
				new BadRequestHttpException('Bad request.'),
				Response::HTTP_BAD_REQUEST,
				json_encode(['error' => 'Bad request.'])
			],
			[
				new ValidationException(
					new ConstraintViolationList(
						[
							new ConstraintViolation(
								message: 'Name cannot be blank.',
								messageTemplate: null,
								parameters: [],
								root: '',
								propertyPath: 'name',
								invalidValue: '',
							),
						]
					)
				),
				Response::HTTP_UNPROCESSABLE_ENTITY,
				json_encode(['errors' => ['name' => 'Name cannot be blank.']])
			],
		];
	}
}
