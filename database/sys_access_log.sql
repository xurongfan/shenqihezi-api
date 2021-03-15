-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-03-15 14:52:25
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
-- 表的结构 `sys_access_log`
--

CREATE TABLE `sys_access_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `ip` varchar(191) DEFAULT NULL COMMENT '请求ip',
  `method` varchar(191) DEFAULT NULL COMMENT '请求方法',
  `path` varchar(191) DEFAULT NULL COMMENT '请求路径',
  `status` bigint(20) DEFAULT NULL COMMENT '请求状态',
  `latency` float(20,3) DEFAULT NULL COMMENT '延迟（用时）',
  `agent` varchar(191) DEFAULT NULL COMMENT '代理',
  `error_message` text COMMENT '错误信息',
  `body` longtext COMMENT '请求Body',
  `resp` longtext COMMENT '响应Body',
  `user_id` varchar(20) DEFAULT NULL COMMENT '用户id',
  `user_name` varchar(40) DEFAULT NULL COMMENT '用户姓名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `sys_access_log`
--

INSERT INTO `sys_access_log` (`id`, `created_at`, `updated_at`, `deleted_at`, `ip`, `method`, `path`, `status`, `latency`, `agent`, `error_message`, `body`, `resp`, `user_id`, `user_name`) VALUES
(4927, '2021-03-04 16:09:49', '2021-03-04 16:09:49', NULL, '183.253.28.190', 'POST', '/api/user/login', 200, 0.029, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_0_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.192 Safari/537.36', '登录成功', '{\"username\":\"admin\",\"password\":\"123456\"}', '{\"success\":true,\"code\":200,\"msg\":\"\\u767b\\u5f55\\u6210\\u529f\",\"data\":{\"id\":1,\"created_at\":\"2020-10-09T13:46:11.000000Z\",\"updated_at\":\"2020-10-09T13:46:11.000000Z\",\"deleted_at\":null,\"uuid\":\"81e98dea-3289-47c8-8d90-9dbc48310d8b\",\"username\":\"admin\",\"nick_name\":\"\\u8d85\\u7ea7\\u7ba1\\u7406\\u5458\",\"header_img\":\"https:\\/\\/thirdwx.qlogo.cn\\/mmopen\\/vi_32\\/Q0j4TwGTfTL3useHGQXxrM5qSJdyTB5OFhDBPbIdLts0hYFbyVmmzkG38ibpeDZ2icayib9eN9sd6r8bo5iaocrxeg\\/132\",\"authority_id\":\"888\",\"token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hZG1pbi1mbi5mdW50b3VjaHBhbC5jb21cL2FwcFwvYXBpXC91c2VyXC9sb2dpbiIsImlhdCI6MTYxNDg0NTM4OSwiZXhwIjoxNjE1MDYxMzg5LCJuYmYiOjE2MTQ4NDUzODksImp0aSI6IjA2OUxXZXV4bkV0TkhUbkEiLCJzdWIiOjEsInBydiI6IjYyNGVkYTliMGViMjFjN2FkMTQ4MDhhZjMzZjNkM2M0M2IwYWZiNGIifQ.bbAXMYjSutCdxOoWn-Uh8FNWGMUPYBtjejfD8EKtwiY\"}}', '1', 'admin');

--
-- 转储表的索引
--

--
-- 表的索引 `sys_access_log`
--
ALTER TABLE `sys_access_log`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_sys_operation_records_deleted_at` (`deleted_at`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `sys_access_log`
--
ALTER TABLE `sys_access_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9197;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
