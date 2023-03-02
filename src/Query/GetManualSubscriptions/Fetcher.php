<?php

declare(strict_types=1);

namespace App\Query\GetManualSubscriptions;

use App\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

final class Fetcher
{
	/**
	 * @param EntityRepository<Entity\ManualSubscription> $manualSubscriptionRepository
	 */
	public function __construct(
		private EntityRepository $manualSubscriptionRepository,
	) {
	}

	/**
	 * @return ManualSubscription[]
	 */
	public function fetch(Query $query): array
	{
		$criteria = new Criteria();

		if (!empty($query->ids)) {
			$criteria->andWhere(Criteria::expr()->in('id', (array)$query->ids));
		}
		if (!empty($query->authorIds)) {
			$criteria->andWhere(Criteria::expr()->in('author', (array)$query->authorIds));
		}
		if (!empty($query->channelIds)) {
			$criteria->andWhere(Criteria::expr()->in('channel', (array)$query->channelIds));
		}
		if (!empty($query->offerIds)) {
			$criteria->andWhere(Criteria::expr()->in('offer', (array)$query->offerIds));
		}
		if (!empty($query->isActive)) {
			$isActive = filter_var($query->isActive, FILTER_VALIDATE_BOOLEAN);
			$criteria->andWhere(Criteria::expr()->eq('isActive', $isActive));
		}
		if (!empty($query->statuses)) {
			$criteria->andWhere(Criteria::expr()->in('status', (array)$query->statuses));
		}
		if (!empty($query->perPage)) {
			$page = max((int)$query->page, 1);
			$firstResult = (int)$query->perPage * ($page - 1);
			$criteria->setFirstResult($firstResult);
			$criteria->setMaxResults((int)$query->perPage);
		}

		$manualSubscriptions = $this->manualSubscriptionRepository->matching($criteria);

		$result = [];
		foreach ($manualSubscriptions as $manualSubscription) {
			/** @var Entity\ManualSubscription $manualSubscription */
			$result[] = new ManualSubscription(
				id:        $manualSubscription->getId(),
				authorId:  $manualSubscription->getAuthor()->getId(),
				channelId: $manualSubscription->getChannel()->getId(),
				offerId:   $manualSubscription->getOffer()->getId(),
				isActive:  $manualSubscription->isActive(),
				status:    $manualSubscription->getStatus(),
			);
		}

		return $result;
	}
}
