-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Дек 11 2012 г., 21:32
-- Версия сервера: 5.1.62
-- Версия PHP: 5.4.6--pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Структура таблицы `area`
--

CREATE TABLE IF NOT EXISTS `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Структура таблицы `box`
--

CREATE TABLE IF NOT EXISTS `box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `box_type_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57 ;

-- --------------------------------------------------------

--
-- Структура таблицы `box_type`
--

CREATE TABLE IF NOT EXISTS `box_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unit` (`name`,`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cable`
--

CREATE TABLE IF NOT EXISTS `cable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pq_1` int(11) DEFAULT NULL,
  `pq_2` int(11) DEFAULT NULL,
  `cable_type` int(11) DEFAULT NULL,
  `fib2` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pq_1_pq_2_fib` (`pq_1`,`pq_2`,`fib2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=556 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cable_type`
--

CREATE TABLE IF NOT EXISTS `cable_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `fib` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_fib` (`name`,`fib`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cruz_conn`
--

CREATE TABLE IF NOT EXISTS `cruz_conn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pq_id` int(11) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `fiber_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pq_id_port_fiber_id` (`pq_id`,`port`,`fiber_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9323 ;

-- --------------------------------------------------------

--
-- Структура таблицы `desc`
--

CREATE TABLE IF NOT EXISTS `desc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text,
  `node_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fiber`
--

CREATE TABLE IF NOT EXISTS `fiber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cable_id` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cable_id_num` (`cable_id`,`num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4893 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fiber_conn`
--

CREATE TABLE IF NOT EXISTS `fiber_conn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiber_id_1` int(11) DEFAULT NULL,
  `fiber_id_2` int(11) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fib_1_fib_2_node` (`fiber_id_1`,`fiber_id_2`,`node_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2076 ;

-- --------------------------------------------------------

--
-- Структура таблицы `keys`
--

CREATE TABLE IF NOT EXISTS `keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num` varchar(255) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `num` (`num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=813 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lift`
--

CREATE TABLE IF NOT EXISTS `lift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `lift_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `node_id_lift_id` (`node_id`,`lift_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `lift_type`
--

CREATE TABLE IF NOT EXISTS `lift_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Структура таблицы `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location` (`location`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mc`
--

CREATE TABLE IF NOT EXISTS `mc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `mc_type_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mc_type`
--

CREATE TABLE IF NOT EXISTS `mc_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `power` float DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_power` (`name`,`power`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) DEFAULT NULL,
  `incorrect` tinyint(1) DEFAULT NULL,
  `street_id` int(11) DEFAULT NULL,
  `street_num_id` int(11) DEFAULT NULL,
  `num_ent` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `st_id_st_num_id_num_ent_loc_id_room_id` (`street_id`,`street_num_id`,`num_ent`,`location_id`,`room_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=682 ;

-- --------------------------------------------------------

--
-- Структура таблицы `other`
--

CREATE TABLE IF NOT EXISTS `other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `other_type_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Структура таблицы `other_type`
--

CREATE TABLE IF NOT EXISTS `other_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unit` (`name`,`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pq`
--

CREATE TABLE IF NOT EXISTS `pq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `pq_type_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `node_num_pq_type_id` (`node`,`num`,`pq_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=679 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pq_type`
--

CREATE TABLE IF NOT EXISTS `pq_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `ports_num` int(11) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_type_ports_num_unit` (`name`,`type`,`ports_num`,`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Структура таблицы `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room` (`room`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sn`
--

CREATE TABLE IF NOT EXISTS `sn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(50) DEFAULT NULL,
  `eq` int(11) DEFAULT NULL,
  `eq_type` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn_eq_type` (`sn`,`eq_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Структура таблицы `street_name`
--

CREATE TABLE IF NOT EXISTS `street_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `small_name` varchar(255) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_area_id` (`name`,`area_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=97 ;

-- --------------------------------------------------------

--
-- Структура таблицы `street_num`
--

CREATE TABLE IF NOT EXISTS `street_num` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `street_name_id` int(11) DEFAULT NULL,
  `num` varchar(255) DEFAULT NULL,
  `desc` varchar(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `street_name_num` (`street_name_id`,`num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=549 ;

-- --------------------------------------------------------

--
-- Структура таблицы `switches`
--

CREATE TABLE IF NOT EXISTS `switches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `switch_type_id` int(11) DEFAULT NULL,
  `used_ports` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Структура таблицы `switch_type`
--

CREATE TABLE IF NOT EXISTS `switch_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ports_num` int(11) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `power` float DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_ports_num_unit_power` (`name`,`ports_num`,`unit`,`power`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ups`
--

CREATE TABLE IF NOT EXISTS `ups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `ups_type_id` int(11) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `ups_type`
--

CREATE TABLE IF NOT EXISTS `ups_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `power` float DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unit_power` (`name`,`unit`,`power`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `node_add` tinyint(1) DEFAULT NULL,
  `node_edit` tinyint(1) DEFAULT NULL,
  `node_del` tinyint(1) DEFAULT NULL,
  `pq_add` tinyint(1) DEFAULT NULL,
  `pq_edit` tinyint(1) DEFAULT NULL,
  `pq_del` tinyint(1) DEFAULT NULL,
  `cable_add` tinyint(1) DEFAULT NULL,
  `cable_edit` tinyint(1) DEFAULT NULL,
  `cable_move` tinyint(1) DEFAULT NULL,
  `cable_del` tinyint(1) DEFAULT NULL,
  `cable_del_all` tinyint(1) DEFAULT NULL,
  `fiber_add` tinyint(1) DEFAULT NULL,
  `fiber_del` tinyint(1) DEFAULT NULL,
  `fiber_find` tinyint(1) DEFAULT NULL,
  `port_add` tinyint(1) DEFAULT NULL,
  `port_edit` tinyint(1) DEFAULT NULL,
  `port_del` tinyint(1) DEFAULT NULL,
  `port_edit_desc` tinyint(1) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
