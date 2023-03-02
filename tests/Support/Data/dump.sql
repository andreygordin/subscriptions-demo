CREATE TABLE channel
(
	id        INT UNSIGNED NOT NULL,
	user_id   INT UNSIGNED NOT NULL,
	is_active TINYINT(1) NOT NULL,
	INDEX     IDX_user_id (user_id),
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

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

CREATE TABLE offer
(
	id            INT UNSIGNED NOT NULL,
	advertiser_id INT UNSIGNED NOT NULL,
	trust_level   SMALLINT NOT NULL,
	status        SMALLINT NOT NULL,
	INDEX         IDX_advertiser_id (advertiser_id),
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE user
(
	id          INT UNSIGNED NOT NULL,
	is_active   TINYINT(1) NOT NULL,
	role        VARCHAR(2) NOT NULL,
	trust_level SMALLINT   NULL,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE channel
	ADD CONSTRAINT FK_channel_user_id FOREIGN KEY (user_id) REFERENCES user (id);

ALTER TABLE manual_subscription
	ADD CONSTRAINT FK_manual_subscription_author_id FOREIGN KEY (author_id) REFERENCES user (id);

ALTER TABLE manual_subscription
	ADD CONSTRAINT FK_manual_subscription_channel_id FOREIGN KEY (channel_id) REFERENCES channel (id);

ALTER TABLE manual_subscription
	ADD CONSTRAINT FK_manual_subscription_offer_id FOREIGN KEY (offer_id) REFERENCES offer (id);

ALTER TABLE offer
	ADD CONSTRAINT FK_offer_advertiser_id FOREIGN KEY (advertiser_id) REFERENCES user (id);

INSERT INTO user (id, is_active, role, trust_level)
VALUES (1, 1, 'W', 1),
	   (2, 1, 'A', null);

INSERT INTO channel (id, user_id, is_active)
VALUES (1, 1, 1);

INSERT INTO offer (id, advertiser_id, trust_level, status)
VALUES (1, 2, 1, 1),
	   (2, 2, 1, 0);

INSERT INTO manual_subscription (id, author_id, channel_id, offer_id, is_active, status)
VALUES (1, 1, 1, 1, TRUE, 1);
