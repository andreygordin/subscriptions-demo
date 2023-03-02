<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\CreateManualSubscription;
use App\Command\DeleteManualSubscription;
use App\Command\UpdateManualSubscription;
use App\Service\OpenApi\Attribute;
use App\Query\GetManualSubscriptions;
use App\Validator\Validator;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[OA\Tag(name: 'ManualSubscriptions')]
final class ManualSubscriptionController
{
	public function __construct(
		private DenormalizerInterface $denormalizer,
		private UrlGeneratorInterface $urlGenerator,
		private Validator $validator,
	) {
	}

	#[Route('/manualSubscriptions', name: 'manual_subscriptions_get_list', methods: ['GET', 'HEAD'])]
	#[OA\Get(path: '/manualSubscriptions', description: 'Returns the manual subscriptions list', tags: ['ManualSubscriptions'])]
	#[Attribute\Parameters(GetManualSubscriptions\Query::class)]
	#[OA\Response(
		response: Response::HTTP_OK,
		description: 'OK',
		content: new OA\JsonContent(
			type: 'array',
			items: new OA\Items(type: GetManualSubscriptions\ManualSubscription::class),
		),
	)]
	public function getList(Request $request, GetManualSubscriptions\Fetcher $fetcher): JsonResponse
	{
		/** @var GetManualSubscriptions\Query $query */
		$query = $this->denormalizer->denormalize(
			$request->query->all(),
			GetManualSubscriptions\Query::class
		);

		$manualSubscriptions = $fetcher->fetch($query);

		return new JsonResponse(
			['manualSubscription' => $manualSubscriptions]
		);
	}

	#[Route('/manualSubscriptions/{id}', name: 'manual_subscriptions_get_one', methods: ['GET', 'HEAD'])]
	#[OA\Get(
		path: '/manualSubscriptions/{id}',
		description: 'Returns the manual subscription by ID',
		tags: ['ManualSubscriptions'],
		parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
	)]
	#[OA\Response(
		response: Response::HTTP_OK,
		description: 'OK',
		content: new OA\JsonContent(ref: '#/components/schemas/GetManualSubscriptionsResult'),
	)]
	#[OA\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
	public function getOne(int $id, Request $request, GetManualSubscriptions\Fetcher $fetcher): JsonResponse
	{
		/** @var GetManualSubscriptions\Query $query */
		$query = $this->denormalizer->denormalize(
			array_merge($request->query->all(), ['ids' => [$id]]),
			GetManualSubscriptions\Query::class
		);

		$manualSubscription = $fetcher->fetch($query)[0] ?? null;

		if (is_null($manualSubscription)) {
			throw new NotFoundHttpException('Manual subscription not found.');
		}

		return new JsonResponse(
			['manualSubscription' => $manualSubscription]
		);
	}

	#[Route('/manualSubscriptions', name: 'manual_subscriptions_post', methods: ['POST'])]
	#[OA\Post(path: '/manualSubscriptions', description: 'Creates a new manual subscription', tags: ['ManualSubscriptions'])]
	#[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/CreateManualSubscriptionCommand'))]
	#[OA\Response(ref: '#/components/responses/Created', response: Response::HTTP_CREATED)]
	#[OA\Response(ref: '#/components/responses/ValidationErrors', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
	public function post(Request $request, CreateManualSubscription\Handler $handler): Response
	{
		/** @var CreateManualSubscription\Command $command */
		$command = $this->denormalizer->denormalize(
			json_decode($request->getContent(), true),
			CreateManualSubscription\Command::class
		);

		$this->validator->validate($command);

		$id = $handler->handle($command);

		return new Response(
			null,
			Response::HTTP_CREATED,
			['Location' => $this->urlGenerator->generate('manual_subscriptions_get_one', ['id' => $id])]
		);
	}

	#[Route('/manualSubscriptions/{id}', name: 'manual_subscriptions_patch', methods: ['PATCH'])]
	#[OA\Patch(
		path: '/manualSubscriptions/{id}',
		description: 'Updates an existing manual subscription',
		tags: ['ManualSubscriptions'],
		parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
	)]
	#[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/UpdateManualSubscriptionCommand'))]
	#[OA\Response(
		response: Response::HTTP_NO_CONTENT,
		description: 'Updated',
	)]
	#[OA\Response(ref: '#/components/responses/ValidationErrors', response: Response::HTTP_UNPROCESSABLE_ENTITY)]
	#[OA\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
	public function patch(Request $request, UpdateManualSubscription\Handler $handler): Response
	{
		/** @var UpdateManualSubscription\Command $command */
		$command = $this->denormalizer->denormalize(
			array_merge(json_decode($request->getContent(), true), $request->attributes->all()),
			UpdateManualSubscription\Command::class
		);

		$this->validator->validate($command);

		try {
			$handler->handle($command);
		} catch (UpdateManualSubscription\ManualSubscriptionNotFoundException) {
			throw new NotFoundHttpException('Manual subscription not found.');
		}

		return new Response(null, Response::HTTP_NO_CONTENT);
	}

	#[Route('/manualSubscriptions/{id}', name: 'manual_subscriptions_delete', methods: ['DELETE'])]
	#[OA\Delete(
		path: '/manualSubscriptions/{id}',
		description: 'Deletes an existing manual subscription',
		tags: ['ManualSubscriptions'],
		parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
	)]
	#[OA\Response(ref: '#/components/responses/Deleted', response: Response::HTTP_NO_CONTENT)]
	#[OA\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
	public function delete(Request $request, DeleteManualSubscription\Handler $handler): Response
	{
		/** @var DeleteManualSubscription\Command $command */
		$command = $this->denormalizer->denormalize(
			$request->attributes->all(),
			DeleteManualSubscription\Command::class
		);

		try {
			$handler->handle($command);
		} catch (DeleteManualSubscription\ManualSubscriptionNotFoundException) {
			throw new NotFoundHttpException('Manual subscription not found.');
		}

		return new Response(null, Response::HTTP_NO_CONTENT);
	}
}
