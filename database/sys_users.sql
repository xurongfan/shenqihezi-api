-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-03-15 14:51:13
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
-- 表的结构 `sys_users`
--

CREATE TABLE `sys_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `uuid` varchar(191) DEFAULT NULL COMMENT '用户UUID',
  `username` varchar(191) DEFAULT NULL COMMENT '用户登录名',
  `password` varchar(191) DEFAULT NULL COMMENT '用户登录密码',
  `nick_name` varchar(191) DEFAULT '系统用户' COMMENT '用户昵称',
  `header_img` varchar(191) DEFAULT 'http://qmplusimg.henrongyi.top/head.png' COMMENT '用户头像',
  `authority_id` varchar(90) DEFAULT '888' COMMENT '用户角色ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `sys_users`
--

INSERT INTO `sys_users` (`id`, `created_at`, `updated_at`, `deleted_at`, `uuid`, `username`, `password`, `nick_name`, `header_img`, `authority_id`) VALUES
(1, '2020-10-09 21:46:11', '2021-03-04 16:10:35', NULL, '81e98dea-3289-47c8-8d90-9dbc48310d8b', 'admin', 'a4fdeae7fcc2426e0710a493924ead00', '超级管理员', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTL3useHGQXxrM5qSJdyTB5OFhDBPbIdLts0hYFbyVmmzkG38ibpeDZ2icayib9eN9sd6r8bo5iaocrxeg/132', '888'),
(2, '2020-10-09 21:46:11', '2020-11-08 12:17:25', NULL, '9715b6bf-041a-47cd-9481-d5e3a7b2abc9', 'a303176530', '3ec063004a6f31642261936a379fde3d', 'QMPlusUser', 'http://qmplusimg.henrongyi.top/1572075907logo.png', '8881'),
(17, '2020-11-08 13:16:59', '2021-02-22 02:46:46', NULL, 'aec95b54-21c4-11eb-8b20-1c3947102d9d', 'wangjie', 'e10adc3949ba59abbe56e057f20f883e', '系统用户', 'http://qmplusimg.henrongyi.top/gvalogo.png', '8881'),
(18, '2021-02-22 03:29:12', '2021-02-22 03:29:12', NULL, '21e8f97c-74be-11eb-92bd-0242ac120005', 'acer123', 'e10adc3949ba59abbe56e057f20f883e', '系统用户', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTL3useHGQXxrM5qSJdyTB5OFhDBPbIdLts0hYFbyVmmzkG38ibpeDZ2icayib9eN9sd6r8bo5iaocrxeg/132', '888'),
(19, '2021-03-09 18:11:58', '2021-03-09 18:11:58', NULL, 'e1d86d0a-80bf-11eb-b73e-5254009ac618', 'yunying', 'b35ce399132d51dd110949238a9722c1', '系统用户', 'http://qmplusimg.henrongyi.top/1576554439myAvatar.png', '8888');

--
-- 转储表的索引
--

--
-- 表的索引 `sys_users`
--
ALTER TABLE `sys_users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `idx_username` (`username`) USING BTREE,
  ADD KEY `idx_sys_users_deleted_at` (`deleted_at`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `sys_users`
--
ALTER TABLE `sys_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
