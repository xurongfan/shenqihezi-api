-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-03-15 14:51:48
-- 服务器版本： 10.0.38-MariaDB
-- PHP 版本： 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `fun-touch`
--

-- --------------------------------------------------------

--
-- 表的结构 `sys_authorities`
--

CREATE TABLE `sys_authorities` (
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `authority_id` varchar(90) NOT NULL COMMENT '角色ID',
  `authority_name` varchar(191) DEFAULT NULL COMMENT '角色名',
  `parent_id` varchar(191) DEFAULT NULL COMMENT '父角色ID',
  `menu_ids` varchar(191) DEFAULT NULL COMMENT '菜单IDS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `sys_authorities`
--

INSERT INTO `sys_authorities` (`created_at`, `updated_at`, `deleted_at`, `authority_id`, `authority_name`, `parent_id`, `menu_ids`) VALUES
('2020-10-09 21:46:11', '2021-03-15 10:50:41', NULL, '888', '普通用户', '', '[1,30,45,46,31,32,47,33,34,35,43,44,36,37,38,39,40,41,42,3,4,7,23,5,6,24,8,17,18,19,20,9,10,11,12,13,14,15,25,16]'),
('2020-10-09 21:46:11', '2021-02-24 02:19:20', NULL, '8881', '普通用户子角色', '888', '[30,31,26,1,21,3,4,7,23,5,6,22,24,8,17,18,19,20,9,10,11,12,13,14,15,25,16,27]'),
('2021-03-09 18:11:08', '2021-03-09 18:11:29', NULL, '8888', '运营', '', '[1,30,31,32,38,39,8]'),
('2020-10-09 21:46:11', '2020-11-07 07:53:32', NULL, '9528', '测试角色', NULL, '[26,1,21,8,9,10,11,12,13,14,15,25,16,27,2]');

--
-- 转储表的索引
--

--
-- 表的索引 `sys_authorities`
--
ALTER TABLE `sys_authorities`
  ADD PRIMARY KEY (`authority_id`) USING BTREE,
  ADD UNIQUE KEY `authority_id` (`authority_id`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
