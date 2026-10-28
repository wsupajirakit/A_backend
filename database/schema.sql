-- A Backend schema (structure only, no data).
-- Import: mysql -u <user> -p aradio < database/schema.sql

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `aradio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aradio`;

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `profile_image` varchar(100) NOT NULL,
  `user_level` tinyint(1) NOT NULL DEFAULT '1',
  `active_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `artist` (
  `artist_id` int(11) NOT NULL AUTO_INCREMENT,
  `artist_name` varchar(255) NOT NULL,
  `artist_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`artist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_title` text NOT NULL,
  `post_content` text NOT NULL,
  `post_cover` varchar(100) NOT NULL,
  `post_slug` varchar(100) NOT NULL,
  `publish_date` datetime NOT NULL,
  `publish_status` tinyint(1) NOT NULL,
  `pin_status` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `public_relations` (
  `pr_id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_type` tinyint(1) NOT NULL COMMENT '1=cover,2=sidebar sponsor,3=marquee,4=app popup',
  `pr_title` varchar(255) DEFAULT NULL,
  `pr_message` text,
  `pr_image` varchar(100) DEFAULT NULL,
  `pr_image_app` varchar(100) DEFAULT NULL,
  `pr_url` text NOT NULL,
  `pr_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`pr_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `public_relations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `radio_presenter` (
  `rp_id` int(11) NOT NULL AUTO_INCREMENT,
  `rp_name` varchar(300) NOT NULL,
  `rp_image` varchar(100) NOT NULL,
  `rp_birthdate` date NOT NULL,
  `rp_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `radio_program` (
  `rdo_program_id` int(11) NOT NULL AUTO_INCREMENT,
  `rdo_program_start` date NOT NULL,
  `rdo_program_end` date NOT NULL,
  `rdo_program_time_range` varchar(5) NOT NULL,
  `rdo_program_hour_gap` varchar(2) NOT NULL,
  `radio_id` int(11) NOT NULL COMMENT 'station id from radio_list in site config',
  PRIMARY KEY (`rdo_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `radio_program_details` (
  `rdo_program_details_id` int(11) NOT NULL AUTO_INCREMENT,
  `rdo_program_id` int(11) NOT NULL,
  `rdo_program_details_day` tinyint(1) NOT NULL,
  `rdo_program_details_time_start` time NOT NULL,
  `rdo_program_details_time_end` time NOT NULL,
  PRIMARY KEY (`rdo_program_details_id`),
  KEY `rdo_program_id` (`rdo_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `radio_program_details_radio_presenter` (
  `rp_id` int(11) NOT NULL,
  `rdo_program_details_id` int(11) NOT NULL,
  PRIMARY KEY (`rp_id`,`rdo_program_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `song` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT,
  `song_name` varchar(255) NOT NULL,
  `artist_name_map` varchar(2550) NOT NULL,
  PRIMARY KEY (`song_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `song_artist` (
  `song_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `is_lead_artist` tinyint(1) NOT NULL,
  PRIMARY KEY (`song_id`,`artist_id`),
  KEY `artist_id` (`artist_id`),
  CONSTRAINT `song_artist_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`artist_id`) ON UPDATE CASCADE,
  CONSTRAINT `song_artist_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `song` (`song_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `top_chart` (
  `top_chart_id` int(11) NOT NULL AUTO_INCREMENT,
  `top_chart_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`top_chart_id`),
  KEY `user_id` (`user_id`),
  KEY `top_chart_date` (`top_chart_date`),
  CONSTRAINT `top_chart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `top_chart_song` (
  `top_chart_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `song_order` int(11) NOT NULL,
  `last_week_order` int(11) DEFAULT NULL,
  `on_chart_week_number` varchar(90) DEFAULT NULL,
  PRIMARY KEY (`top_chart_id`,`song_id`),
  KEY `song_id` (`song_id`),
  CONSTRAINT `top_chart_song_ibfk_1` FOREIGN KEY (`top_chart_id`) REFERENCES `top_chart` (`top_chart_id`) ON UPDATE CASCADE,
  CONSTRAINT `top_chart_song_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `song` (`song_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
