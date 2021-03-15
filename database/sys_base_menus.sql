-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-03-15 14:51:39
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
-- 表的结构 `sys_base_menus`
--

CREATE TABLE `sys_base_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `menu_level` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` varchar(191) DEFAULT NULL COMMENT '父菜单ID',
  `path` varchar(191) DEFAULT NULL COMMENT '路由path',
  `name` varchar(191) DEFAULT NULL COMMENT '路由name',
  `meta` varchar(255) NOT NULL COMMENT 'meta',
  `icon` varchar(191) DEFAULT NULL COMMENT '附加属性',
  `hidden` tinyint(1) DEFAULT NULL COMMENT '是否在列表隐藏',
  `component` varchar(191) DEFAULT NULL COMMENT '对应前端文件路径',
  `sort` bigint(20) DEFAULT NULL COMMENT '排序标记'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `sys_base_menus`
--

INSERT INTO `sys_base_menus` (`id`, `created_at`, `updated_at`, `deleted_at`, `menu_level`, `parent_id`, `path`, `name`, `meta`, `icon`, `hidden`, `component`, `sort`) VALUES
(1, '2020-10-09 21:46:11', '2021-02-20 09:35:50', NULL, 0, '0', 'dashboard', 'dashboard', '{\"title\":\"仪表盘\",\"icon\":\"setting\",\"defaultMenu\":false,\"keepAlive\":false}', 'setting', 0, 'view/dashboard/index.vue', 1),
(3, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '0', 'admin', 'superAdmin', '{\"title\":\"超级管理员\",\"icon\":\"user-solid\",\"defaultMenu\":false,\"keepAlive\":false}', 'user-solid', 0, 'view/superAdmin/index.vue', 3),
(4, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '3', 'authority', 'authority', '{\"title\":\"角色管理\",\"icon\":\"s-custom\",\"defaultMenu\":false,\"keepAlive\":false}', 's-custom', 0, 'view/superAdmin/authority/authority.vue', 1),
(5, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '3', 'menu', 'menu', '{\"title\":\"菜单管理\",\"icon\":\"s-order\",\"defaultMenu\":false,\"keepAlive\":false}', 's-order', 0, 'view/superAdmin/menu/menu.vue', 2),
(6, '2020-10-09 05:46:11', '2020-11-07 21:01:22', NULL, 0, '3', 'api', 'api', '{\"title\":\"api管理\",\"icon\":\"s-platform\",\"defaultMenu\":false,\"keepAlive\":false}', 's-platform', 1, 'view/superAdmin/api/api.vue', 3),
(7, '2020-10-09 21:46:11', '2020-11-08 13:02:51', NULL, 0, '3', 'user', 'user', '{\"title\":\"用户管理\",\"icon\":\"coordinate\",\"defaultMenu\":false,\"keepAlive\":false}', 'coordinate', 0, 'view/superAdmin/user/user.vue', 1),
(8, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '0', 'person', 'person', '{\"title\":\"个人信息\",\"icon\":\"message-solid\",\"defaultMenu\":false,\"keepAlive\":false}', 'message-solid', 1, 'view/person/person.vue', 4),
(9, '2020-10-09 21:46:11', '2021-03-02 10:00:42', NULL, 0, '0', 'example', 'example', '{\"title\":\"示例文件\",\"icon\":\"s-management\",\"defaultMenu\":false,\"keepAlive\":false}', 's-management', 1, 'view/example/index.vue', 6),
(10, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'table', 'table', '{\"title\":\"表格示例\",\"icon\":\"s-order\",\"defaultMenu\":false,\"keepAlive\":false}', 's-order', 0, 'view/example/table/table.vue', 1),
(11, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'form', 'form', '{\"title\":\"表单示例\",\"icon\":\"document\",\"defaultMenu\":false,\"keepAlive\":false}', 'document', 0, 'view/example/form/form.vue', 2),
(12, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'rte', 'rte', '{\"title\":\"富文本编辑器\",\"icon\":\"reading\",\"defaultMenu\":false,\"keepAlive\":false}', 'reading', 0, 'view/example/rte/rte.vue', 3),
(13, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'excel', 'excel', '{\"title\":\"excel导入导出\",\"icon\":\"s-marketing\",\"defaultMenu\":false,\"keepAlive\":false}', 's-marketing', 0, 'view/example/excel/excel.vue', 4),
(14, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'upload', 'upload', '{\"title\":\"上传下载\",\"icon\":\"upload\",\"defaultMenu\":false,\"keepAlive\":false}', 'upload', 0, 'view/example/upload/upload.vue', 5),
(15, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'breakpoint', 'breakpoint', '{\"title\":\"断点续传\",\"icon\":\"upload\",\"defaultMenu\":false,\"keepAlive\":false}', 'upload', 0, 'view/example/breakpoint/breakpoint.vue', 6),
(16, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'customer', 'customer', '{\"title\":\"客户列表（资源示例）\",\"icon\":\"s-custom\",\"defaultMenu\":false,\"keepAlive\":false}', 's-custom', 0, 'view/example/customer/customer.vue', 7),
(17, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '0', 'systemTools', 'systemTools', '{\"title\":\"系统工具\",\"icon\":\"s-cooperation\",\"defaultMenu\":false,\"keepAlive\":false}', 's-cooperation', 0, 'view/systemTools/index.vue', 5),
(18, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '17', 'autoCode', 'autoCode', '{\"title\":\"代码生成器\",\"icon\":\"cpu\",\"defaultMenu\":false,\"keepAlive\":false}', 'cpu', 0, 'view/systemTools/autoCode/index.vue', 1),
(19, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '17', 'formCreate', 'formCreate', '{\"title\":\"表单生成器\",\"icon\":\"magic-stick\",\"defaultMenu\":false,\"keepAlive\":false}', 'magic-stick', 0, 'view/systemTools/formCreate/index.vue', 2),
(20, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '17', 'system', 'system', '{\"title\":\"系统配置\",\"icon\":\"s-operation\",\"defaultMenu\":false,\"keepAlive\":false}', 's-operation', 0, 'view/systemTools/system/system.vue', 3),
(23, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '3', 'dictionaryDetail/:id', 'dictionaryDetail', '{\"title\":\"字典详情\",\"icon\":\"s-order\",\"defaultMenu\":false,\"keepAlive\":false}', 's-order', 1, 'view/superAdmin/dictionary/sysDictionaryDetail.vue', 1),
(24, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '3', 'operation', 'operation', '{\"title\":\"操作历史\",\"icon\":\"time\",\"defaultMenu\":false,\"keepAlive\":false}', 'time', 0, 'view/superAdmin/operation/sysOperationRecord.vue', 6),
(25, '2020-10-09 21:46:11', '2020-10-09 21:46:11', NULL, 0, '9', 'simpleUploader', 'simpleUploader', '{\"title\":\"断点续传（插件版）\",\"icon\":\"upload\",\"defaultMenu\":false,\"keepAlive\":false}', 'upload', 0, 'view/example/simpleUploader/simpleUploader', 6),
(29, '2020-12-01 12:21:37', '2020-12-05 08:55:36', NULL, NULL, '28', 'area', 'area', '{\"title\":\"\\u5168\\u56fd\\u5730\\u5740\",\"icon\":\"s-flag\",\"defaultMenu\":false,\"keepAlive\":null}', NULL, NULL, 'view/base/baseArea/index.vue', 0),
(30, '2021-02-20 02:20:30', '2021-02-23 18:24:16', NULL, NULL, '0', 'gameIndex', 'gameIndex', '{\"title\":\"\\u6e38\\u620f\\u7ba1\\u7406\",\"icon\":\"star-on\",\"defaultMenu\":false,\"keepAlive\":null}', NULL, 0, 'view/game/index.vue', 2),
(31, '2021-02-24 02:12:38', '2021-02-24 05:38:09', NULL, NULL, '30', 'game', 'game', '{\"title\":\"\\u6e38\\u620f\\u5305\",\"icon\":\"goods\",\"defaultMenu\":false,\"keepAlive\":\"\"}', NULL, 0, 'view/game/game/index.vue', 1),
(32, '2021-02-24 03:47:41', '2021-02-24 05:38:40', NULL, NULL, '30', 'gameTag', 'gameTag', '{\"title\":\"\\u6e38\\u620f\\u6807\\u7b7e\",\"icon\":\"tickets\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/game/gameTag/index.vue', 2),
(33, '2021-02-24 06:05:05', '2021-02-24 06:10:11', NULL, NULL, '0', 'systemConfig', 'systemConfig', '{\"title\":\"App\\u7cfb\\u7edf\\u914d\\u7f6e\",\"icon\":\"setting\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/system/index.vue', 2),
(34, '2021-02-24 00:03:55', '2021-02-24 17:43:49', NULL, NULL, '0', 'merUser', 'merUser', '{\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"icon\":\"user\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/merUser/index.vue', 2),
(35, '2021-02-24 08:06:28', '2021-02-25 01:44:13', NULL, NULL, '34', 'userList', 'userList', '{\"title\":\"\\u7528\\u6237\\u7ec4\",\"icon\":\"s-check\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/merUser/user/index.vue', NULL),
(36, '2021-02-25 01:16:18', '2021-02-25 01:16:18', NULL, NULL, '0', 'topic', 'topic', '{\"title\":\"\\u8bdd\\u9898\\u7ba1\\u7406\",\"icon\":\"s-home\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/topic/index.vue', 2),
(37, '2021-02-25 09:17:43', '2021-02-25 09:18:13', NULL, NULL, '36', 'topicContent', 'topicContent', '{\"title\":\"\\u8bdd\\u9898\\u5185\\u5bb9\",\"icon\":\"tickets\",\"defaultMenu\":false,\"keepAlive\":\"\"}', NULL, 0, 'view/topic/topicContent/index.vue', NULL),
(38, '2021-03-02 00:42:23', '2021-03-02 00:44:19', NULL, NULL, '0', 'google', 'google', '{\"title\":\"\\u8c37\\u6b4c\\u5e02\\u573a\\u914d\\u7f6e\",\"icon\":\"s-finance\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/google/index.vue', 2),
(39, '2021-03-02 08:43:10', '2021-03-02 08:44:30', NULL, NULL, '38', 'channel', 'channel', '{\"title\":\"\\u8c37\\u6b4c\\u6e20\\u9053\",\"icon\":\"s-help\",\"defaultMenu\":false,\"keepAlive\":\"\"}', NULL, 0, 'view/google/channel/index.vue', NULL),
(40, '2021-03-02 01:19:03', '2021-03-02 01:19:03', NULL, NULL, '0', 'pay', 'pay', '{\"title\":\"\\u652f\\u4ed8\\u7ba1\\u7406\",\"icon\":\"coin\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/pay/index.vue', 2),
(41, '2021-03-02 09:19:56', '2021-03-02 09:19:56', NULL, NULL, '40', 'payProject', 'payProject', '{\"title\":\"\\u652f\\u4ed8\\u9879\\u76ee\",\"icon\":\"tickets\",\"defaultMenu\":false,\"keepAlive\":\"\"}', NULL, 0, 'view/pay/project/index.vue', NULL),
(42, '2021-03-02 22:10:52', '2021-03-02 22:10:52', NULL, NULL, '40', 'payOrder', 'payOrder', '{\"title\":\"\\u652f\\u4ed8\\u8ba2\\u5355\",\"icon\":\"sunny\",\"defaultMenu\":false,\"keepAlive\":\"\"}', NULL, 0, 'view/pay/order/index.vue', NULL),
(43, '2021-03-09 19:57:52', '2021-03-09 19:57:52', NULL, NULL, '34', 'staticsRemain', 'staticsRemain', '{\"title\":\"\\u7528\\u6237\\u7559\\u5b58\",\"icon\":\"chat-line-round\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/merUser/remain/index.vue', NULL),
(44, '2021-03-10 11:00:58', '2021-03-10 11:00:58', NULL, NULL, '34', 'staticsCountry', 'staticsCountry', '{\"title\":\"\\u56fd\\u5bb6\\u5206\\u5e03\",\"icon\":\"discount\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/merUser/country/index.vue', NULL),
(45, '2021-03-10 17:36:45', '2021-03-11 13:33:22', NULL, NULL, '30', 'staticsGame', 'staticsGame', '{\"title\":\"\\u6e38\\u620f\\u70ed\\u5ea6\",\"icon\":\"s-data\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/game/game/statics.vue', NULL),
(46, '2021-03-11 13:45:42', '2021-03-11 13:45:42', NULL, NULL, '30', 'staticsGameCountry', 'staticsGameCountry', '{\"title\":\"\\u6e38\\u620f\\u56fd\\u5bb6\\u70ed\\u5ea6\",\"icon\":\"stopwatch\",\"defaultMenu\":false,\"keepAlive\":false}', NULL, 0, 'view/game/game/country.vue', NULL),
(47, '2021-03-15 10:11:14', '2021-03-15 10:11:14', NULL, NULL, '30', 'gameType', 'gameType', '{\"title\":\"\\u6e38\\u620f\\u7c7b\\u522b\",\"icon\":\"eleme\",\"defaultMenu\":false,\"keepAlive\":\"\"}', NULL, 0, 'view/game/gameType/index.vue', 3);

--
-- 转储表的索引
--

--
-- 表的索引 `sys_base_menus`
--
ALTER TABLE `sys_base_menus`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `idx_sys_base_menus_deleted_at` (`deleted_at`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `sys_base_menus`
--
ALTER TABLE `sys_base_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
