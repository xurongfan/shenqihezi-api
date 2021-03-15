-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-03-15 14:51:22
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
-- 表的结构 `sys_files`
--

CREATE TABLE `sys_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL COMMENT '文件名',
  `url` text COMMENT '文件地址',
  `tag` varchar(191) DEFAULT NULL COMMENT '文件标签',
  `key` varchar(191) DEFAULT NULL COMMENT '编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `sys_files`
--

INSERT INTO `sys_files` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `url`, `tag`, `key`) VALUES
(1, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, '10.png', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTL3useHGQXxrM5qSJdyTB5OFhDBPbIdLts0hYFbyVmmzkG38ibpeDZ2icayib9eN9sd6r8bo5iaocrxeg/132', 'png', '158787308910.png'),
(2, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 'logo.png', 'http://qmplusimg.henrongyi.top/1576554439myAvatar.png', 'png', '1587973709logo.png');

--
-- 转储表的索引
--

--
-- 表的索引 `sys_files`
--
ALTER TABLE `sys_files`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_exa_file_upload_and_downloads_deleted_at` (`deleted_at`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `sys_files`
--
ALTER TABLE `sys_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
