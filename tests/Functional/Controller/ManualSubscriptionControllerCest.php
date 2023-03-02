<?php

declare(strict_types=1);

namespace Test\Functional\Controller;

use App\Entity\Enum\ManualSubscriptionStatus;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Symfony\Component\HttpFoundation\Response;
use Test\Support\FunctionalTester;

final class ManualSubscriptionControllerCest
{
	#[DataProvider('getListProvider')]
	public function getList(FunctionalTester $I, Example $example): void
	{
		$I->sendGet('/manualSubscriptions', $example['params']);
		$I->seeResponseCodeIsSuccessful();
		$I->seeResponseIsJson();
		$I->seeResponseJsonXpathEvaluatesTo($example['xPath'], true);

		if ($I->grabDataFromResponseByJsonPath('manualSubscription[0]') === []) {
			return;
		}

		$I->seeResponseMatchesJsonType(
			[
				'manualSubscription' => [
					[
						'id' => 'integer',
						'authorId' => 'integer',
						'channelId' => 'integer',
						'offerId' => 'integer',
						'isActive' => 'boolean',
						'status' => 'integer',
					],
				],
			]
		);
	}

	public function getOneSuccessfully(FunctionalTester $I): void
	{
		$I->sendGet('/manualSubscriptions/1');
		$I->seeResponseCodeIsSuccessful();
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType(
			[
				'manualSubscription' => [
					'id' => 'integer',
					'authorId' => 'integer',
					'channelId' => 'integer',
					'offerId' => 'integer',
					'isActive' => 'boolean',
					'status' => 'integer',
				],
			]
		);
		$I->seeResponseJsonXpathEvaluatesTo('/manualSubscription/id = 1', true);
	}

	public function getOneNonExistent(FunctionalTester $I): void
	{
		$I->sendGet('/manualSubscriptions/1000');
		$I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
	}

	public function postSuccessfully(FunctionalTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPost(
			'/manualSubscriptions',
			[
				'authorId' => 1,
				'channelId' => 1,
				'offerId' => 2,
				'isActive' => true,
				'status' => ManualSubscriptionStatus::UNSUBSCRIBED,
			]
		);
		$I->seeResponseCodeIs(Response::HTTP_CREATED);
		$I->seeResponseEquals('');
		$I->seeHttpHeader('Location');
		$I->seeInDatabase(
			'manual_subscription',
			[
				'author_id' => 1,
				'channel_id' => 1,
				'offer_id' => 2,
				'is_active' => true,
				'status' => ManualSubscriptionStatus::UNSUBSCRIBED,
			]
		);
	}

	#[DataProvider('postWithValidationErrorsProvider')]
	public function postWithValidationErrors(FunctionalTester $I, Example $example): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPost('/manualSubscriptions', $example['params']);
		$I->seeResponseCodeIs(Response::HTTP_UNPROCESSABLE_ENTITY);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType(
			[
				'errors' => [
					$example['errorPath'] => 'string',
				],
			]
		);
	}

	#[DataProvider('patchSuccessfullyProvider')]
	public function patchSuccessfully(FunctionalTester $I, Example $example): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPatch('/manualSubscriptions/1', $example['params']);
		$I->seeResponseCodeIs(Response::HTTP_NO_CONTENT);
		$I->seeResponseEquals('');
		$I->seeInDatabase(
			'manual_subscription',
			array_merge(['id' => 1], $example['dbCriteria']),
		);
	}

	#[DataProvider('patchWithValidationErrorsProvider')]
	public function patchWithValidationErrors(FunctionalTester $I, Example $example): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPatch('/manualSubscriptions/1', $example['params']);
		$I->seeResponseCodeIs(Response::HTTP_UNPROCESSABLE_ENTITY);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType(
			[
				'errors' => [
					$example['errorPath'] => 'string',
				],
			]
		);
	}

	public function patchNonExistent(FunctionalTester $I): void
	{
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->sendPatch(
			'/manualSubscriptions/1000',
			['status' => ManualSubscriptionStatus::SUBSCRIBED]
		);
		$I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
	}

	public function delete(FunctionalTester $I): void
	{
		$I->sendDelete('/manualSubscriptions/1');
		$I->seeResponseCodeIs(Response::HTTP_NO_CONTENT);
		$I->seeResponseEquals('');
		$I->dontSeeInDatabase(
			'manual_subscription',
			['id' => 1]
		);
	}

	public function deleteNonExistent(FunctionalTester $I): void
	{
		$I->sendDelete('/manualSubscriptions/1000', []);
		$I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
	}

	private function getListProvider(): array
	{
		return [
			'No filters' => [
				'params' => [],
				'xPath' => 'count(/manualSubscription/*) > 0',
			],
			'Filter by an existent id' => [
				'params' => ['ids' => [1]],
				'xPath' => '/manualSubscription/id = 1',
			],
			'Filter by a non-existent id' => [
				'params' => ['ids' => [1000]],
				'xPath' => 'count(/manualSubscription/*) = 0',
			],
			'Filter by an existent author' => [
				'params' => ['authorIds' => [1]],
				'xPath' => '/manualSubscription/authorId = 1',
			],
			'Filter by a non-existent author' => [
				'params' => ['authorIds' => [1000]],
				'xPath' => 'count(/manualSubscription/*) = 0',
			],
			'Filter by an existent channel' => [
				'params' => ['channelIds' => [1]],
				'xPath' => '/manualSubscription/channelId = 1',
			],
			'Filter by a non-existent channel' => [
				'params' => ['channelIds' => [1000]],
				'xPath' => 'count(/manualSubscription/*) = 0',
			],
			'Filter by an existent offer' => [
				'params' => ['offerIds' => [1]],
				'xPath' => '/manualSubscription/offerId = 1',
			],
			'Filter by a non-existent offer' => [
				'params' => ['offerIds' => [1000]],
				'xPath' => 'count(/manualSubscription/*) = 0',
			],
			'Filter by activity' => [
				'params' => ['isActive' => true],
				'xPath' => '/manualSubscription/isActive = true()',
			],
			'Filter by an existent status' => [
				'params' => ['statuses' => [ManualSubscriptionStatus::SUBSCRIBED]],
				'xPath' => '/manualSubscription/status = ' . ManualSubscriptionStatus::SUBSCRIBED,
			],
			'Filter by a non-existent status' => [
				'params' => ['statuses' => [1000]],
				'xPath' => 'count(/manualSubscription/*) = 0',
			],
		];
	}

	private function postWithValidationErrorsProvider(): array
	{
		$validParams = [
			'authorId' => 1,
			'channelId' => 1,
			'offerId' => 2,
			'isActive' => true,
			'status' => ManualSubscriptionStatus::UNSUBSCRIBED,
		];

		return [
			'Author is not specified' => [
				'params' => array_merge(
					$validParams,
					['authorId' => null]
				),
				'errorPath' => 'authorId',
			],
			'Author doesnʼt exist' => [
				'params' => array_merge(
					$validParams,
					['authorId' => 1000]
				),
				'errorPath' => 'authorId',
			],
			'Author is not a webmaster' => [
				'params' => array_merge(
					$validParams,
					['authorId' => 2]
				),
				'errorPath' => 'authorId',
			],
			'Channel is not specified' => [
				'params' => array_merge(
					$validParams,
					['channelId' => null]
				),
				'errorPath' => 'channelId',
			],
			'Channel doesnʼt exist' => [
				'params' => array_merge(
					$validParams,
					['channelId' => 1000]
				),
				'errorPath' => 'channelId',
			],
			'Offer is not specified' => [
				'params' => array_merge(
					$validParams,
					['offerId' => null]
				),
				'errorPath' => 'offerId',
			],
			'Offer doesnʼt exist' => [
				'params' => array_merge(
					$validParams,
					['offerId' => 1000]
				),
				'errorPath' => 'offerId',
			],
			'Activity is not specified' => [
				'params' => array_merge(
					$validParams,
					['isActive' => null]
				),
				'errorPath' => 'isActive',
			],
			'Activity is invalid' => [
				'params' => array_merge(
					$validParams,
					['isActive' => 'invalid_value']
				),
				'errorPath' => 'isActive',
			],
			'Status is not specified' => [
				'params' => array_merge(
					$validParams,
					['status' => null]
				),
				'errorPath' => 'status',
			],
			'Status doesnʼt exist' => [
				'params' => array_merge(
					$validParams,
					['status' => 1000]
				),
				'errorPath' => 'status',
			],
			'Subscription already exists' => [
				'params' => array_merge(
					$validParams,
					['channelId' => 1, 'offerId' => 1]
				),
				'errorPath' => '',
			],
		];
	}

	private function patchSuccessfullyProvider(): array
	{
		return [
			'Patch activity' => [
				'params' => ['isActive' => false],
				'dbCriteria' => ['is_active' => 0],
			],
			'Patch status' => [
				'params' => ['status' => ManualSubscriptionStatus::UNSUBSCRIBED],
				'dbCriteria' => ['status' => ManualSubscriptionStatus::UNSUBSCRIBED],
			],
		];
	}

	private function patchWithValidationErrorsProvider(): array
	{
		return [
			'Activity is invalid' => [
				'params' => ['isActive' => 'invalid_value'],
				'errorPath' => 'isActive',
			],
			'Status doesnʼt exist' => [
				'params' => ['status' => 1000],
				'errorPath' => 'status',
			],
		];
	}
}
