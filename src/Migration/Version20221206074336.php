<?php

declare(strict_types=1);

namespace Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221206074336 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		$this->addSql(
			'
				CREATE TABLE channel
				(
					id        INT UNSIGNED NOT NULL,
					user_id   INT UNSIGNED NOT NULL,
					is_active TINYINT(1) NOT NULL,
					INDEX     IDX_user_id (user_id),
					PRIMARY KEY (id)
				) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
			'
		);

		$this->addSql(
			'
				CREATE TABLE manual_subscription
					(
					id         INT UNSIGNED AUTO_INCREMENT NOT NULL,
					author_id  INT UNSIGNED NOT NULL,
					channel_id INT UNSIGNED NOT NULL,
					offer_id   INT UNSIGNED NOT NULL,
					is_active  TINYINT(1) NOT NULL,
					status     SMALLINT NOT NULL,
					INDEX      IDX_author_id (author_id),
					INDEX      IDX_channel_id (channel_id),
					INDEX      IDX_offer_id (offer_id),
					UNIQUE INDEX UQ_manual_subscription_channel_id_offer_id (channel_id, offer_id),
					PRIMARY KEY (id)
				) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
			'
		);

		$this->addSql(
			'
				CREATE TABLE offer
					(
					id            INT UNSIGNED NOT NULL,
					advertiser_id INT UNSIGNED NOT NULL,
					trust_level   SMALLINT DEFAULT NULL,
					status        SMALLINT NOT NULL,
					INDEX         IDX_advertiser_id (advertiser_id),
					PRIMARY KEY (id)
				) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
			'
		);

		$this->addSql(
			'
				CREATE TABLE user
					(
					id          INT UNSIGNED NOT NULL,
					is_active   TINYINT(1) NOT NULL,
					role        VARCHAR(2) NOT NULL,
					trust_level SMALLINT   NOT NULL,
					PRIMARY KEY (id)
				) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
			'
		);

		$this->addSql(
			'
				ALTER TABLE channel
					ADD CONSTRAINT FK_channel_user_id FOREIGN KEY (user_id) REFERENCES user (id);
			'
		);

		$this->addSql(
			'
				ALTER TABLE manual_subscription
					ADD CONSTRAINT FK_manual_subscription_author_id FOREIGN KEY (author_id) REFERENCES user (id);
			'
		);

		$this->addSql(
			'
				ALTER TABLE manual_subscription
					ADD CONSTRAINT FK_manual_subscription_channel_id FOREIGN KEY (channel_id) REFERENCES channel (id);
			'
		);

		$this->addSql(
			'
				ALTER TABLE manual_subscription
					ADD CONSTRAINT FK_manual_subscription_offer_id FOREIGN KEY (offer_id) REFERENCES offer (id);
			'
		);

		$this->addSql(
			'
				ALTER TABLE offer
					ADD CONSTRAINT FK_offer_advertiser_id FOREIGN KEY (advertiser_id) REFERENCES user (id);
			'
		);
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE channel DROP FOREIGN KEY FK_channel_user_id');
		$this->addSql('ALTER TABLE manual_subscription DROP FOREIGN KEY FK_manual_subscription_author_id');
		$this->addSql('ALTER TABLE manual_subscription DROP FOREIGN KEY FK_manual_subscription_channel_id');
		$this->addSql('ALTER TABLE manual_subscription DROP FOREIGN KEY FK_manual_subscription_offer_id');
		$this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_offer_advertiser_id');
		$this->addSql('DROP TABLE channel');
		$this->addSql('DROP TABLE manual_subscription');
		$this->addSql('DROP TABLE offer');
		$this->addSql('DROP TABLE user');
	}
}
