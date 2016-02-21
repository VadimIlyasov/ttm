ALTER TABLE `tweets` ADD `satisfaction` TINYINT NOT NULL DEFAULT '0' AFTER `tweet_id`;



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `raw_tweets` (
  `id` int(11) NOT NULL,
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;

ALTER TABLE `raw_tweets`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `raw_tweets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



CREATE TABLE IF NOT EXISTS `satisfactions` (
  `id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  `tweet_id` bigint(20) unsigned NOT NULL,
  `satisfaction` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `satisfactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keyword_id` (`keyword_id`),
  ADD KEY `tweet_id` (`tweet_id`);

ALTER TABLE `satisfactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `satisfactions`
  ADD CONSTRAINT `satisfactions_ibfk_1` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE;

