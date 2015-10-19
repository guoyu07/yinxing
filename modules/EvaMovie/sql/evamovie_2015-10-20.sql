# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.27)
# Database: yinxing
# Generation Time: 2015-10-19 17:08:56 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table eva_movie_makers
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_makers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '制作商名称',
  `summary` text COMMENT '制作商简介',
  `logo` varchar(255) DEFAULT NULL COMMENT '制作商Logo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table eva_movie_movies
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_movies` (
  `id` bigint(14) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '名称',
  `banngo` varchar(50) NOT NULL DEFAULT '' COMMENT '番号',
  `subBanngo` varchar(50) DEFAULT NULL COMMENT '番号别名',
  `originalTitle` varchar(255) DEFAULT NULL COMMENT '原名',
  `aka` varchar(500) DEFAULT NULL COMMENT 'Array 又名',
  `alt` varchar(255) DEFAULT NULL COMMENT '信息URL',
  `ratingsCount` int(11) NOT NULL DEFAULT '0' COMMENT '评分人数',
  `wishCount` int(11) NOT NULL DEFAULT '0' COMMENT '想看人数',
  `collectCount` int(11) NOT NULL DEFAULT '0' COMMENT '看过人数',
  `doCount` int(11) NOT NULL DEFAULT '0' COMMENT '在看人数',
  `subtype` varchar(10) NOT NULL DEFAULT 'movie' COMMENT '条目分类 movie | tv',
  `website` varchar(255) DEFAULT NULL COMMENT '官方网站',
  `pubdate` varchar(10) DEFAULT NULL COMMENT '上映日期',
  `year` int(4) DEFAULT NULL COMMENT '年代',
  `languages` varchar(20) DEFAULT NULL COMMENT '语言',
  `genres` varchar(100) DEFAULT NULL COMMENT 'Array 影片类型',
  `durations` varchar(20) DEFAULT NULL COMMENT '片长',
  `countries` varchar(100) DEFAULT NULL COMMENT 'Array 制作国家',
  `summary` text COMMENT '简介',
  `seasonsCount` int(3) NOT NULL DEFAULT '0' COMMENT '总季数',
  `currentSeason` int(3) NOT NULL DEFAULT '0' COMMENT '当前季数',
  `tags` varchar(255) DEFAULT NULL COMMENT 'Array 标签',
  `episodesCount` int(3) NOT NULL DEFAULT '0' COMMENT '当前季的集数',
  `makerId` int(10) NOT NULL DEFAULT '0' COMMENT '制作厂商ID',
  `seriesId` int(10) NOT NULL DEFAULT '0' COMMENT '系列ID',
  `images` varchar(1000) DEFAULT NULL COMMENT 'Array 封面图片，尺寸从小到大',
  `previews` varchar(1000) DEFAULT NULL COMMENT 'Array 预览图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table eva_movie_movies_casts
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_movies_casts` (
  `id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `movieId` bigint(14) NOT NULL,
  `staffId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table eva_movie_movies_directors
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_movies_directors` (
  `id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `movieId` bigint(14) NOT NULL,
  `staffId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table eva_movie_movies_writers
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_movies_writers` (
  `id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `movieId` bigint(14) NOT NULL,
  `staffId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table eva_movie_series
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '系列名称',
  `summary` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table eva_movie_staffs
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eva_movie_staffs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '影人姓名',
  `nameRuby` varchar(100) DEFAULT NULL COMMENT '姓名标注',
  `nameEn` varchar(100) DEFAULT NULL COMMENT '英文名',
  `alt` varchar(255) DEFAULT NULL COMMENT 'URL',
  `avatars` varchar(1000) DEFAULT NULL COMMENT 'Array 头像',
  `summary` text COMMENT '简介',
  `aka` varchar(255) DEFAULT NULL COMMENT 'Array 别名',
  `akaRuby` varchar(255) DEFAULT NULL COMMENT 'Array 别名标注',
  `akaEn` varchar(255) DEFAULT NULL COMMENT 'Array 别名英文名',
  `website` varchar(255) DEFAULT NULL COMMENT '官方网站',
  `gender` varchar(10) DEFAULT NULL COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `bornPlace` varchar(50) DEFAULT NULL COMMENT '出生地',
  `professions` varchar(100) DEFAULT NULL COMMENT '职业',
  `constellation` varchar(50) DEFAULT NULL COMMENT '星座',
  `threeSizes` varchar(20) DEFAULT NULL COMMENT '三围',
  `cup` varchar(1) DEFAULT NULL COMMENT '罩杯',
  `hobby` varchar(50) DEFAULT NULL COMMENT '爱好',
  `isDirector` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导演身份',
  `isWriter` tinyint(1) NOT NULL DEFAULT '0' COMMENT '编剧身份',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
