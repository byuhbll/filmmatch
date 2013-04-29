
-- ---------------------------------------------------------------- --
-- Create filmmatch tables
-- ---------------------------------------------------------------- --

-- Record

CREATE TABLE IF NOT EXISTS `Record` (
  `catId` VARCHAR(64) COLLATE utf8_bin NOT NULL,
  `title` TEXT NOT NULL DEFAULT '',
  `notes` TEXT DEFAULT NULL,
  `queue` INT(11) NOT NULL DEFAULT '0',
  `status` ENUM('yes','no') DEFAULT NULL,
  `modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`catId`),
  KEY `queue` (`queue`)
) ENGINE MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


-- History

CREATE TABLE IF NOT EXISTS `History` (
  `catId` VARCHAR(64) COLLATE utf8_bin NOT NULL,
  `tmdbId` VARCHAR(16) COLLATE utf8_bin DEFAULT NULL,
  `imdbId` VARCHAR(16) COLLATE utf8_bin DEFAULT NULL,
  `status` VARCHAR(16) NOT NULL,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `catId` (`catId`)
) ENGINE MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


-- Result

CREATE TABLE IF NOT EXISTS `Result` (
  `catId` VARCHAR(64) COLLATE utf8_bin NOT NULL,
  `tmdbId` VARCHAR(16) COLLATE utf8_bin NOT NULL,
  `imdbId` VARCHAR(16) COLLATE utf8_bin NOT NULL,
  `status` enum('yes','no') NOT NULL,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`catId`,`tmdbId`,`imdbId`)
) ENGINE MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

