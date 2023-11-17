/*
 Navicat MySQL Data Transfer

 Source Server         : hk103.19.190.65
 Source Server Type    : MySQL
 Source Server Version : 50738 (5.7.38-log)
 Source Host           : 103.19.190.65:3306
 Source Schema         : tgbot

 Target Server Type    : MySQL
 Target Server Version : 50738 (5.7.38-log)
 File Encoding         : 65001

 Date: 06/09/2023 16:14:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_extension_histories
-- ----------------------------
DROP TABLE IF EXISTS `admin_extension_histories`;
CREATE TABLE `admin_extension_histories`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 1,
  `version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_extension_histories_name_index`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_extension_histories
-- ----------------------------
INSERT INTO `admin_extension_histories` VALUES (1, 'dcat-admin.operation-log', 2, '1.0.0', 'create_opration_log_table.php', '2023-07-20 00:00:16', '2023-07-20 00:00:16');
INSERT INTO `admin_extension_histories` VALUES (2, 'dcat-admin.operation-log', 1, '1.0.0', 'Initialize extension.', '2023-07-22 23:35:18', '2023-07-22 23:35:18');
INSERT INTO `admin_extension_histories` VALUES (3, 'dcat-admin.form-step', 1, '1.0.0', 'Initialize extension.', '2023-08-03 13:43:00', '2023-08-03 13:43:00');

-- ----------------------------
-- Table structure for admin_extensions
-- ----------------------------
DROP TABLE IF EXISTS `admin_extensions`;
CREATE TABLE `admin_extensions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_extensions_name_unique`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_extensions
-- ----------------------------
INSERT INTO `admin_extensions` VALUES (1, 'dcat-admin.operation-log', '1.0.0', 1, NULL, '2023-07-22 23:35:18', '2023-07-22 23:35:21');
INSERT INTO `admin_extensions` VALUES (2, 'dcat-admin.form-step', '1.0.0', 0, NULL, '2023-08-03 13:43:00', '2023-08-03 13:43:00');

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `uri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `extension` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `show` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES (1, 0, 1, 'Index', 'feather icon-bar-chart-2', '/', '', 1, '2023-04-27 01:59:46', '2023-08-03 05:49:40');
INSERT INTO `admin_menu` VALUES (2, 0, 2, 'Admin', 'feather icon-settings', '', '', 1, '2023-04-27 01:59:46', '2023-08-03 05:49:51');
INSERT INTO `admin_menu` VALUES (3, 2, 3, 'Users', '', 'auth/users', '', 1, '2023-04-27 01:59:46', '2023-08-03 05:50:02');
INSERT INTO `admin_menu` VALUES (4, 2, 4, 'Roles', '', 'auth/roles', '', 1, '2023-04-27 01:59:46', NULL);
INSERT INTO `admin_menu` VALUES (5, 2, 5, 'Permission', '', 'auth/permissions', '', 1, '2023-04-27 01:59:46', '2023-08-03 05:50:43');
INSERT INTO `admin_menu` VALUES (6, 2, 6, 'Menu', '', 'auth/menu', '', 1, '2023-04-27 01:59:46', '2023-08-03 05:50:53');
INSERT INTO `admin_menu` VALUES (7, 2, 7, 'Extensions', '', 'auth/extensions', '', 1, '2023-04-27 01:59:46', '2023-08-03 05:50:26');
INSERT INTO `admin_menu` VALUES (9, 0, 8, 'Operation Log', '', 'auth/operation-logs', 'dcat-admin.operation-log', 1, '2023-07-22 23:35:18', '2023-07-22 23:35:18');
INSERT INTO `admin_menu` VALUES (12, 0, 9, '红包雷管理', 'fa-delicious', NULL, '', 1, '2023-08-04 11:34:58', '2023-08-04 11:34:58');
INSERT INTO `admin_menu` VALUES (17, 12, 14, '充值记录', 'fa-diamond', '/rechargerecord', '', 1, '2023-08-22 14:42:02', '2023-08-22 14:42:34');
INSERT INTO `admin_menu` VALUES (14, 12, 11, '群组管理', 'fa-group', 'groups', '', 1, '2023-08-04 11:43:23', '2023-08-04 11:43:32');
INSERT INTO `admin_menu` VALUES (15, 12, 12, '用户管理', 'fa-user', 'tgusers', '', 1, '2023-08-04 11:44:02', '2023-08-04 11:44:02');
INSERT INTO `admin_menu` VALUES (16, 12, 13, '红包管理', 'fa-shopping-bag', 'luckmoney', '', 1, '2023-08-04 11:44:38', '2023-08-04 11:44:38');
INSERT INTO `admin_menu` VALUES (18, 12, 15, '平台抽成记录', NULL, '/commissionrecord', '', 1, '2023-08-22 15:14:04', '2023-08-22 15:14:04');
INSERT INTO `admin_menu` VALUES (19, 12, 16, '中奖记录', 'fa-money', 'rewardrecord', '', 1, '2023-08-22 16:50:12', '2023-08-22 16:50:48');
INSERT INTO `admin_menu` VALUES (20, 12, 17, '提现记录', 'fa-caret-square-o-up', 'withdrawrecord', '', 1, '2023-08-24 11:29:20', '2023-08-24 11:29:20');
INSERT INTO `admin_menu` VALUES (21, 12, 18, '报表', NULL, 'user-reports', '', 1, '2023-09-02 14:31:03', '2023-09-02 14:31:03');

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_operation_log_user_id_index`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9676 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------

-- ----------------------------
-- Table structure for admin_permission_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_permission_menu`;
CREATE TABLE `admin_permission_menu`  (
  `permission_id` bigint(20) NOT NULL,
  `menu_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE INDEX `admin_permission_menu_permission_id_menu_id_unique`(`permission_id`, `menu_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of admin_permission_menu
-- ----------------------------
INSERT INTO `admin_permission_menu` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (2, 2, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (3, 3, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (3, 2, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (4, 4, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (4, 2, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (5, 5, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (5, 2, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (6, 6, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (6, 2, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (7, 7, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (7, 2, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (9, 9, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (12, 12, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (52, 15, '2023-09-05 15:47:52', '2023-09-05 15:47:52');
INSERT INTO `admin_permission_menu` VALUES (13, 12, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (14, 14, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (14, 12, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (15, 15, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (15, 12, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (16, 16, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (16, 12, NULL, NULL);
INSERT INTO `admin_permission_menu` VALUES (51, 15, '2023-09-05 15:47:14', '2023-09-05 15:47:14');
INSERT INTO `admin_permission_menu` VALUES (50, 21, '2023-09-02 14:31:59', '2023-09-02 14:31:59');
INSERT INTO `admin_permission_menu` VALUES (49, 20, '2023-08-24 11:31:43', '2023-08-24 11:31:43');
INSERT INTO `admin_permission_menu` VALUES (48, 15, '2023-08-24 11:31:02', '2023-08-24 11:31:02');
INSERT INTO `admin_permission_menu` VALUES (47, 19, '2023-08-22 16:50:12', '2023-08-22 16:50:12');
INSERT INTO `admin_permission_menu` VALUES (46, 18, '2023-08-22 15:14:04', '2023-08-22 15:14:04');
INSERT INTO `admin_permission_menu` VALUES (45, 17, '2023-08-22 14:44:45', '2023-08-22 14:44:45');
INSERT INTO `admin_permission_menu` VALUES (24, 14, '2023-08-11 10:16:55', '2023-08-11 10:16:55');
INSERT INTO `admin_permission_menu` VALUES (25, 14, '2023-08-11 10:20:46', '2023-08-11 10:20:46');
INSERT INTO `admin_permission_menu` VALUES (26, 14, '2023-08-11 10:23:06', '2023-08-11 10:23:06');
INSERT INTO `admin_permission_menu` VALUES (27, 14, '2023-08-11 10:24:31', '2023-08-11 10:24:31');
INSERT INTO `admin_permission_menu` VALUES (28, 14, '2023-08-11 10:29:42', '2023-08-11 10:29:42');
INSERT INTO `admin_permission_menu` VALUES (29, 14, '2023-08-11 10:40:12', '2023-08-11 10:40:12');
INSERT INTO `admin_permission_menu` VALUES (30, 14, '2023-08-11 10:40:54', '2023-08-11 10:40:54');
INSERT INTO `admin_permission_menu` VALUES (31, 15, '2023-08-11 10:16:55', '2023-08-11 10:16:55');
INSERT INTO `admin_permission_menu` VALUES (32, 15, '2023-08-11 10:20:46', '2023-08-11 10:20:46');
INSERT INTO `admin_permission_menu` VALUES (33, 15, '2023-08-11 10:23:06', '2023-08-11 10:23:06');
INSERT INTO `admin_permission_menu` VALUES (34, 15, '2023-08-11 10:24:31', '2023-08-11 10:24:31');
INSERT INTO `admin_permission_menu` VALUES (35, 15, '2023-08-11 10:29:42', '2023-08-11 10:29:42');
INSERT INTO `admin_permission_menu` VALUES (36, 15, '2023-08-11 10:40:12', '2023-08-11 10:40:12');
INSERT INTO `admin_permission_menu` VALUES (37, 15, '2023-08-11 10:40:54', '2023-08-11 10:40:54');
INSERT INTO `admin_permission_menu` VALUES (38, 16, '2023-08-11 10:16:55', '2023-08-11 10:16:55');
INSERT INTO `admin_permission_menu` VALUES (39, 16, '2023-08-11 10:20:46', '2023-08-11 10:20:46');
INSERT INTO `admin_permission_menu` VALUES (40, 16, '2023-08-11 10:23:06', '2023-08-11 10:23:06');
INSERT INTO `admin_permission_menu` VALUES (41, 15, '2023-08-11 10:16:55', '2023-08-11 10:16:55');
INSERT INTO `admin_permission_menu` VALUES (42, 15, '2023-08-11 10:20:46', '2023-08-11 10:20:46');
INSERT INTO `admin_permission_menu` VALUES (43, 15, '2023-08-11 10:23:06', '2023-08-11 10:23:06');
INSERT INTO `admin_permission_menu` VALUES (44, 15, '2023-08-22 14:26:34', '2023-08-22 14:26:34');

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `http_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_permissions_slug_unique`(`slug`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 53 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES (1, 'Index', '4d1a157e-a014-4da5-97fb-2f04307f23b4', NULL, '', 1, 0, '2023-04-27 01:59:46', '2023-08-03 05:49:40');
INSERT INTO `admin_permissions` VALUES (2, 'Admin', '66acb96e-485a-431d-8990-df0825701161', NULL, '', 2, 0, '2023-04-27 01:59:46', '2023-08-03 05:49:51');
INSERT INTO `admin_permissions` VALUES (3, 'Users', 'b742fd78-812e-45f5-92e5-5f219eb95230', NULL, '/auth/users*', 3, 2, '2023-04-27 01:59:46', '2023-08-03 05:50:02');
INSERT INTO `admin_permissions` VALUES (4, 'Roles', '14bcf127-6df8-46a6-bc19-4cec0b0a3393', NULL, '/auth/roles*', 4, 2, '2023-04-27 01:59:46', NULL);
INSERT INTO `admin_permissions` VALUES (5, 'Permission', '437bad33-169e-489e-881f-6ec4d1ec2f32', NULL, '/auth/permissions*', 5, 2, '2023-04-27 01:59:46', '2023-08-03 05:50:43');
INSERT INTO `admin_permissions` VALUES (6, 'Menu', 'f5318063-4ffd-44f5-bea7-8030d9c6904f', NULL, '/auth/menu*', 6, 2, '2023-04-27 01:59:46', '2023-08-03 05:50:53');
INSERT INTO `admin_permissions` VALUES (7, 'Extensions', '7c70a841-d19d-446d-a056-176cef7c8f16', NULL, '/auth/extensions*', 7, 2, '2023-04-27 01:59:46', '2023-08-03 05:50:26');
INSERT INTO `admin_permissions` VALUES (9, 'Operation Log', '1e7c7ba3-b068-48ca-b0b9-c2cf65e17d31', NULL, '/auth/operation-logs*', 8, 0, '2023-07-22 23:35:18', '2023-07-22 23:35:18');
INSERT INTO `admin_permissions` VALUES (12, '红包雷管理', '79c9c3e3-69a0-4898-a282-a8372e78bd4d', NULL, '', 9, 0, '2023-08-04 11:34:58', '2023-08-04 11:34:58');
INSERT INTO `admin_permissions` VALUES (13, '红包配置', 'd6b98c5a-5c9e-4adc-be1a-218a0f35cfaf', '', '', 10, 12, '2023-08-04 11:42:53', '2023-08-11 10:21:23');
INSERT INTO `admin_permissions` VALUES (14, '群组管理', '1c60081e-6e8e-4a27-9fcb-a8348a6e7475', '', '', 11, 12, '2023-08-04 11:43:23', '2023-08-11 10:50:16');
INSERT INTO `admin_permissions` VALUES (15, '用户管理', 'a977fe81-a2a6-40a3-a0b2-a2dce0cf6deb', NULL, '', 12, 12, '2023-08-04 11:44:02', '2023-08-04 11:44:02');
INSERT INTO `admin_permissions` VALUES (16, '红包管理', '3d304cb3-e378-4d05-8d80-c264bbd93cd4', NULL, '', 13, 12, '2023-08-04 11:44:38', '2023-08-04 11:44:38');
INSERT INTO `admin_permissions` VALUES (17, '新增配置', 'config_add', 'GET', '/configs/create', 14, 13, '2023-08-11 10:16:55', '2023-08-11 10:17:08');
INSERT INTO `admin_permissions` VALUES (18, '配置列表', 'config_list', 'GET', '/configs', 15, 13, '2023-08-11 10:20:45', '2023-08-11 10:23:33');
INSERT INTO `admin_permissions` VALUES (19, '编辑配置', 'config_edit', 'GET', '/configs/*/edit', 16, 13, '2023-08-11 10:23:06', '2023-08-11 10:23:19');
INSERT INTO `admin_permissions` VALUES (20, '删除配置', 'config_del', 'DELETE', '/configs/*', 17, 13, '2023-08-11 10:24:31', '2023-08-11 10:24:31');
INSERT INTO `admin_permissions` VALUES (21, '配置详情', 'config_show', 'GET', '/configs/*', 18, 13, '2023-08-11 10:29:42', '2023-08-11 10:29:56');
INSERT INTO `admin_permissions` VALUES (22, '配置保存', 'config_save', 'POST', '/configs', 19, 13, '2023-08-11 10:40:11', '2023-08-11 10:40:11');
INSERT INTO `admin_permissions` VALUES (23, '配置更新', 'config_update', 'PUT,PATCH', '/configs/*', 20, 13, '2023-08-11 10:40:53', '2023-08-11 10:41:21');
INSERT INTO `admin_permissions` VALUES (24, '新增群组', 'group_add', 'GET', '/groups/create', 14, 14, '2023-08-11 10:16:55', '2023-08-11 10:17:08');
INSERT INTO `admin_permissions` VALUES (25, '群组列表', 'group_list', 'GET', '/groups', 15, 14, '2023-08-11 10:20:45', '2023-08-11 10:23:33');
INSERT INTO `admin_permissions` VALUES (26, '编辑群组', 'group_edit', 'GET', '/groups/*/edit', 16, 14, '2023-08-11 10:23:06', '2023-08-11 10:23:19');
INSERT INTO `admin_permissions` VALUES (27, '删除群组', 'group_del', 'DELETE', '/groups/*', 17, 14, '2023-08-11 10:24:31', '2023-08-11 10:24:31');
INSERT INTO `admin_permissions` VALUES (28, '群组详情', 'group_show', 'GET', '/groups/*', 18, 14, '2023-08-11 10:29:42', '2023-08-11 10:29:56');
INSERT INTO `admin_permissions` VALUES (29, '群组保存', 'group_save', 'POST', '/groups', 19, 14, '2023-08-11 10:40:11', '2023-08-11 10:40:11');
INSERT INTO `admin_permissions` VALUES (30, '群组更新', 'group_update', 'PUT,PATCH', '/groups/*', 20, 14, '2023-08-11 10:40:53', '2023-08-11 10:41:21');
INSERT INTO `admin_permissions` VALUES (31, '新增用户', 'tguser_add', 'GET', '/tgusers/create', 15, 15, '2023-08-11 10:16:55', '2023-08-11 10:17:08');
INSERT INTO `admin_permissions` VALUES (32, '用户列表', 'tguser_list', 'GET', '/tgusers', 15, 15, '2023-08-11 10:20:45', '2023-08-11 10:23:33');
INSERT INTO `admin_permissions` VALUES (33, '编辑用户', 'tguser_edit', 'GET', '/tgusers/*/edit', 16, 15, '2023-08-11 10:23:06', '2023-08-11 10:23:19');
INSERT INTO `admin_permissions` VALUES (34, '删除用户', 'tguser_del', 'DELETE', '/tgusers/*', 17, 15, '2023-08-11 10:24:31', '2023-08-11 10:24:31');
INSERT INTO `admin_permissions` VALUES (35, '用户详情', 'tguser_show', 'GET', '/tgusers/*', 18, 15, '2023-08-11 10:29:42', '2023-08-11 10:29:56');
INSERT INTO `admin_permissions` VALUES (36, '用户保存', 'tguser_save', 'POST', '/tgusers', 19, 15, '2023-08-11 10:40:11', '2023-08-11 10:40:11');
INSERT INTO `admin_permissions` VALUES (37, '用户更新', 'tguser_update', 'PUT,PATCH', '/tgusers/*', 20, 15, '2023-08-11 10:40:53', '2023-08-11 10:41:21');
INSERT INTO `admin_permissions` VALUES (38, '红包列表', 'luckmoney_list', 'GET', '/luckmoney', 15, 16, '2023-08-11 10:20:45', '2023-08-11 10:23:33');
INSERT INTO `admin_permissions` VALUES (39, '红包详情', 'luckmoney_show', 'GET', '/luckmoney/*', 18, 16, '2023-08-11 10:29:42', '2023-08-11 10:29:56');
INSERT INTO `admin_permissions` VALUES (40, '红包领取记录', 'luckhistory_list', 'GET', '/luckhistory', 19, 16, '2023-08-11 10:29:42', '2023-08-11 10:29:56');
INSERT INTO `admin_permissions` VALUES (41, '邀请奖励记录', 'inviterecord', 'GET', '/inviterecord', 20, 15, '2023-08-11 10:40:53', '2023-08-11 10:41:21');
INSERT INTO `admin_permissions` VALUES (42, '中奖记录', 'rewardrecord', 'GET', '/rewardrecord', 20, 15, '2023-08-11 10:40:53', '2023-08-11 10:41:21');
INSERT INTO `admin_permissions` VALUES (43, '代理抽成记录', 'sharerecord', 'GET', '/sharerecord', 20, 15, '2023-08-11 10:40:53', '2023-08-11 10:41:21');
INSERT INTO `admin_permissions` VALUES (44, '充值', 'user_recharge', '', '', 21, 15, '2023-08-22 14:26:34', '2023-08-22 14:26:34');
INSERT INTO `admin_permissions` VALUES (45, '充值记录', 'recharge_record', 'GET', '/rechargerecord', 22, 12, '2023-08-22 14:44:45', '2023-08-22 14:45:04');
INSERT INTO `admin_permissions` VALUES (46, '平台抽成记录', 'commission_record', '', '/commissionrecord', 23, 12, '2023-08-22 15:13:30', '2023-08-22 15:13:30');
INSERT INTO `admin_permissions` VALUES (47, '中奖记录', 'reward_record', 'GET', '/rewardrecord', 24, 12, '2023-08-22 16:49:49', '2023-08-22 16:49:49');
INSERT INTO `admin_permissions` VALUES (48, '提现', 'user_withdraw', '', '', 25, 15, '2023-08-24 11:31:02', '2023-08-24 11:31:02');
INSERT INTO `admin_permissions` VALUES (49, '提现记录', 'withdraw_record', 'GET', '/withdrawrecord', 26, 12, '2023-08-24 11:31:43', '2023-08-24 11:31:43');
INSERT INTO `admin_permissions` VALUES (50, '报表', 'userreport', 'GET', '/user-reports', 27, 12, '2023-09-02 14:31:59', '2023-09-02 14:31:59');
INSERT INTO `admin_permissions` VALUES (51, '个人报表', 'tguserreport', 'GET', '/tgusers/report', 28, 15, '2023-09-05 15:47:14', '2023-09-05 15:47:14');
INSERT INTO `admin_permissions` VALUES (52, '资金明细', 'moneylog', 'GET', '/moneylog', 29, 15, '2023-09-05 15:47:52', '2023-09-05 16:40:17');

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu`  (
  `role_id` bigint(20) NOT NULL,
  `menu_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE INDEX `admin_role_menu_role_id_menu_id_unique`(`role_id`, `menu_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------
INSERT INTO `admin_role_menu` VALUES (2, 1, '2023-07-25 15:07:30', '2023-07-25 15:07:30');
INSERT INTO `admin_role_menu` VALUES (2, 12, '2023-08-04 11:45:08', '2023-08-04 11:45:08');
INSERT INTO `admin_role_menu` VALUES (2, 17, '2023-08-24 11:32:27', '2023-08-24 11:32:27');
INSERT INTO `admin_role_menu` VALUES (2, 14, '2023-08-04 13:40:46', '2023-08-04 13:40:46');
INSERT INTO `admin_role_menu` VALUES (2, 15, '2023-08-04 13:40:46', '2023-08-04 13:40:46');
INSERT INTO `admin_role_menu` VALUES (2, 16, '2023-08-04 13:40:46', '2023-08-04 13:40:46');
INSERT INTO `admin_role_menu` VALUES (2, 18, '2023-08-24 11:32:27', '2023-08-24 11:32:27');
INSERT INTO `admin_role_menu` VALUES (2, 19, '2023-08-24 11:32:28', '2023-08-24 11:32:28');
INSERT INTO `admin_role_menu` VALUES (2, 20, '2023-08-24 11:32:28', '2023-08-24 11:32:28');
INSERT INTO `admin_role_menu` VALUES (2, 21, '2023-09-05 16:39:58', '2023-09-05 16:39:58');

-- ----------------------------
-- Table structure for admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions`  (
  `role_id` bigint(20) NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE INDEX `admin_role_permissions_role_id_permission_id_unique`(`role_id`, `permission_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of admin_role_permissions
-- ----------------------------
INSERT INTO `admin_role_permissions` VALUES (2, 18, '2023-08-11 10:21:46', '2023-08-11 10:21:46');
INSERT INTO `admin_role_permissions` VALUES (2, 9, '2023-08-04 11:45:08', '2023-08-04 11:45:08');
INSERT INTO `admin_role_permissions` VALUES (2, 41, '2023-08-11 14:46:46', '2023-08-11 14:46:46');
INSERT INTO `admin_role_permissions` VALUES (2, 32, '2023-08-11 13:53:56', '2023-08-11 13:53:56');
INSERT INTO `admin_role_permissions` VALUES (2, 23, '2023-08-11 10:53:30', '2023-08-11 10:53:30');
INSERT INTO `admin_role_permissions` VALUES (2, 21, '2023-08-11 10:53:30', '2023-08-11 10:53:30');
INSERT INTO `admin_role_permissions` VALUES (2, 19, '2023-08-11 10:26:03', '2023-08-11 10:26:03');
INSERT INTO `admin_role_permissions` VALUES (2, 24, '2023-08-11 10:53:31', '2023-08-11 10:53:31');
INSERT INTO `admin_role_permissions` VALUES (2, 25, '2023-08-11 10:53:31', '2023-08-11 10:53:31');
INSERT INTO `admin_role_permissions` VALUES (2, 28, '2023-08-11 10:53:31', '2023-08-11 10:53:31');
INSERT INTO `admin_role_permissions` VALUES (2, 29, '2023-08-11 10:53:31', '2023-08-11 10:53:31');
INSERT INTO `admin_role_permissions` VALUES (2, 51, '2023-09-05 16:39:35', '2023-09-05 16:39:35');
INSERT INTO `admin_role_permissions` VALUES (2, 30, '2023-08-11 14:46:46', '2023-08-11 14:46:46');
INSERT INTO `admin_role_permissions` VALUES (2, 35, '2023-08-11 13:53:56', '2023-08-11 13:53:56');
INSERT INTO `admin_role_permissions` VALUES (2, 26, '2023-08-11 14:46:46', '2023-08-11 14:46:46');
INSERT INTO `admin_role_permissions` VALUES (2, 50, '2023-09-05 16:39:35', '2023-09-05 16:39:35');
INSERT INTO `admin_role_permissions` VALUES (2, 38, '2023-08-11 13:53:57', '2023-08-11 13:53:57');
INSERT INTO `admin_role_permissions` VALUES (2, 39, '2023-08-11 13:53:57', '2023-08-11 13:53:57');
INSERT INTO `admin_role_permissions` VALUES (2, 40, '2023-08-11 13:53:57', '2023-08-11 13:53:57');
INSERT INTO `admin_role_permissions` VALUES (2, 42, '2023-08-11 14:46:47', '2023-08-11 14:46:47');
INSERT INTO `admin_role_permissions` VALUES (2, 43, '2023-08-11 14:46:47', '2023-08-11 14:46:47');
INSERT INTO `admin_role_permissions` VALUES (2, 44, '2023-08-22 14:38:09', '2023-08-22 14:38:09');
INSERT INTO `admin_role_permissions` VALUES (2, 45, '2023-08-24 11:32:26', '2023-08-24 11:32:26');
INSERT INTO `admin_role_permissions` VALUES (2, 46, '2023-08-24 11:32:26', '2023-08-24 11:32:26');
INSERT INTO `admin_role_permissions` VALUES (2, 47, '2023-08-24 11:32:26', '2023-08-24 11:32:26');
INSERT INTO `admin_role_permissions` VALUES (2, 48, '2023-08-24 11:32:27', '2023-08-24 11:32:27');
INSERT INTO `admin_role_permissions` VALUES (2, 49, '2023-08-24 11:32:27', '2023-08-24 11:32:27');
INSERT INTO `admin_role_permissions` VALUES (2, 52, '2023-09-05 16:39:57', '2023-09-05 16:39:57');

-- ----------------------------
-- Table structure for admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users`  (
  `role_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE INDEX `admin_role_users_role_id_user_id_unique`(`role_id`, `user_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of admin_role_users
-- ----------------------------
INSERT INTO `admin_role_users` VALUES (1, 1, '2023-04-27 01:59:46', '2023-04-27 01:59:46');
INSERT INTO `admin_role_users` VALUES (2, 2, '2023-08-04 11:48:58', '2023-08-04 11:48:58');

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_roles_slug_unique`(`slug`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
INSERT INTO `admin_roles` VALUES (1, 'Administrator', 'administrator', '2023-04-27 01:59:46', '2023-04-27 01:59:46');
INSERT INTO `admin_roles` VALUES (2, '测试', 'test', '2023-07-25 15:07:30', '2023-07-25 15:07:30');

-- ----------------------------
-- Table structure for admin_settings
-- ----------------------------
DROP TABLE IF EXISTS `admin_settings`;
CREATE TABLE `admin_settings`  (
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`slug`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_settings
-- ----------------------------

-- ----------------------------
-- Table structure for admin_test
-- ----------------------------
DROP TABLE IF EXISTS `admin_test`;
CREATE TABLE `admin_test`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `test` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_test
-- ----------------------------

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_users_username_unique`(`username`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES (1, 'admin', '$2y$10$jLcTuZ2MkjULVkNn36i9kOYslLg0lO33AFQ79xlvojg8HNAG4ku7q', 'Administrator', NULL, NULL, '2023-04-27 01:59:46', '2023-08-28 13:48:55');
INSERT INTO `admin_users` VALUES (2, 'test', '$2y$10$68AAg3By5At04fafiTKqM.0ReiJiURggQK/LgNiikim5T7tVMayVO', '测试账号', NULL, 'h0kb3MNXSelFKQr9hYXIiP5OVdRlbYF8Z8LzwvbCLBemELHiYgc9uetrhx39', '2023-07-17 16:53:53', '2023-08-29 11:23:56');

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '群组id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `status` tinyint(2) NULL DEFAULT 0 COMMENT '状态',
  `service_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '客服链接',
  `recharge_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '充值链接',
  `channel_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '频道链接',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `photo_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '图片id',
  `admin_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status_group`(`status`, `group_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for commission_record
-- ----------------------------
DROP TABLE IF EXISTS `commission_record`;
CREATE TABLE `commission_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lucky_id` int(11) NULL DEFAULT NULL COMMENT '红包id',
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) NULL DEFAULT NULL COMMENT '用户id',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `sender_id` bigint(16) NULL DEFAULT NULL COMMENT '发包者id',
  `profit_amount` decimal(10, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 769 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '配置key',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '配置值',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '群组id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx`(`group_id`, `name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 269 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for invite_link
-- ----------------------------
DROP TABLE IF EXISTS `invite_link`;
CREATE TABLE `invite_link`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tg_id` bigint(16) NULL DEFAULT NULL,
  `invite_link` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `group_id` bigint(16) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx`(`group_id`, `invite_link`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for invite_record
-- ----------------------------
DROP TABLE IF EXISTS `invite_record`;
CREATE TABLE `invite_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) NULL DEFAULT NULL COMMENT '用户id',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `invite_user_id` bigint(16) NULL DEFAULT NULL COMMENT '分享者id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for lucky_history
-- ----------------------------
DROP TABLE IF EXISTS `lucky_history`;
CREATE TABLE `lucky_history`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` bigint(16) NULL DEFAULT NULL COMMENT '领取用户id',
  `lucky_id` int(10) NULL DEFAULT NULL COMMENT '红包id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `is_thunder` tinyint(2) NULL DEFAULT NULL COMMENT '是否中雷',
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '领取金额',
  `lose_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '损失金额',
  `first_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `lucky_id_idx`(`lucky_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1481 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '领取红包记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for lucky_money
-- ----------------------------
DROP TABLE IF EXISTS `lucky_money`;
CREATE TABLE `lucky_money`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(16) NULL DEFAULT NULL COMMENT '发送用户id',
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '红包金额',
  `received` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '被领取金额',
  `number` int(3) NULL DEFAULT NULL COMMENT '红包个数',
  `lucky` tinyint(2) NULL DEFAULT NULL COMMENT '是否随机',
  `thunder` int(11) NULL DEFAULT NULL COMMENT '雷',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `chat_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '群组id',
  `red_list` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '红包数组',
  `sender_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '发送者名称',
  `lose_rate` decimal(10, 2) NULL DEFAULT NULL COMMENT '红包倍数',
  `status` tinyint(2) NULL DEFAULT 1 COMMENT '状态:1=正常;2=已领完;3=已过期',
  `message_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '消息id',
  `type` tinyint(1) NULL DEFAULT 1 COMMENT '类型:1=雷包;2=福利红包',
  `received_num` int(10) NULL DEFAULT 0 COMMENT '领取数量',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `chat_id`(`chat_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 654 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '红包表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (3, '2016_01_04_173148_create_admin_tables', 1);
INSERT INTO `migrations` VALUES (4, '2019_08_19_000000_create_failed_jobs_table', 1);
INSERT INTO `migrations` VALUES (5, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` VALUES (6, '2020_09_07_090635_create_admin_settings_table', 1);
INSERT INTO `migrations` VALUES (7, '2020_09_22_015815_create_admin_extensions_table', 1);
INSERT INTO `migrations` VALUES (8, '2020_11_01_083237_update_admin_menu_table', 1);
INSERT INTO `migrations` VALUES (9, '2023_08_28_104735_create_jobs_table', 2);

-- ----------------------------
-- Table structure for money_log
-- ----------------------------
DROP TABLE IF EXISTS `money_log`;
CREATE TABLE `money_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) NULL DEFAULT NULL COMMENT '用户id',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型',
  `lucky_id` int(11) NULL DEFAULT NULL COMMENT '红包id',
  `balance` decimal(10, 2) NULL DEFAULT 0 COMMENT '当时余额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 294 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for pay
-- ----------------------------
DROP TABLE IF EXISTS `pay`;
CREATE TABLE `pay`  (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `telegramid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `name` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `amount` double(255, 3) NULL DEFAULT NULL,
  `topup_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `way` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `applytime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `applytimestamp` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `tixiantime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
  `changetime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `replyMessageid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `isfuyingli` int(11) NULL DEFAULT 0,
  `username` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay
-- ----------------------------

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token`) USING BTREE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type`, `tokenable_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for recharge_record
-- ----------------------------
DROP TABLE IF EXISTS `recharge_record`;
CREATE TABLE `recharge_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tg_id` bigint(16) NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '充值金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态:0=进行中;1=充值成功',
  `type` tinyint(1) NULL DEFAULT NULL COMMENT '类型:1=后台充值;2=自动充值',
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `admin_id` int(11) NULL DEFAULT NULL,
  `group_id` bigint(16) NULL DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `trx_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tail` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for reward_record
-- ----------------------------
DROP TABLE IF EXISTS `reward_record`;
CREATE TABLE `reward_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lucky_id` int(11) NULL DEFAULT NULL COMMENT '红包id',
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) NULL DEFAULT NULL COMMENT '中奖用户id',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `sender_id` bigint(16) NULL DEFAULT NULL COMMENT '发包者id',
  `reward_num` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '中奖数字',
  `type` tinyint(1) NULL DEFAULT NULL COMMENT '中奖类型:1=豹子;2=顺子',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 96 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
-- ----------------------------
-- Table structure for share_record
-- ----------------------------
DROP TABLE IF EXISTS `share_record`;
CREATE TABLE `share_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lucky_id` int(11) NULL DEFAULT NULL COMMENT '红包id',
  `amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) NULL DEFAULT NULL COMMENT '用户id',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `share_user_id` bigint(16) NULL DEFAULT NULL COMMENT '分享者id',
  `sender_id` bigint(16) NULL DEFAULT NULL COMMENT '发包者id',
  `profit_amount` decimal(10, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 134 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tg_users
-- ----------------------------
DROP TABLE IF EXISTS `tg_users`;
CREATE TABLE `tg_users`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `first_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '用户名',
  `tg_id` bigint(16) NULL DEFAULT NULL COMMENT 'tgId',
  `balance` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '余额',
  `status` tinyint(2) NULL DEFAULT 1 COMMENT '状态:1=正常;0=离开',
  `invite_user` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '邀请人id',
  `group_id` bigint(16) NULL DEFAULT NULL COMMENT '组id',
  `has_thunder` tinyint(1) NULL DEFAULT 0 COMMENT '发包必有雷',
  `pass_mine` tinyint(1) NULL DEFAULT 0 COMMENT '抢包不中雷',
  `auto_get` tinyint(1) NULL DEFAULT 0 COMMENT '自动领取红包:1=自动领取',
  `withdraw_addr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `no_thunder` tinyint(1) NULL DEFAULT 0 COMMENT '发包无雷',
  `get_mine` tinyint(1) NULL DEFAULT 0 COMMENT '抢包必中雷',
  `online` tinyint(1) NULL DEFAULT 0 COMMENT '',
  `send_chance` int(3) DEFAULT NULL COMMENT '发包雷的概率',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `tg_id_idx`(`tg_id`) USING BTREE,
  INDEX `tg_id_group_idx`(`tg_id`, `group_id`) USING BTREE,
  INDEX `auto_get`(`auto_get`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 427 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'tg用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------

-- ----------------------------
-- Table structure for withdraw_record
-- ----------------------------
DROP TABLE IF EXISTS `withdraw_record`;
CREATE TABLE `withdraw_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tg_id` bigint(16) NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `amount` decimal(10, 2) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '状态:0=申请;1=提现成功',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '提现地址',
  `addr_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '地址类型',
  `group_id` bigint(16) NULL DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `admin_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;



CREATE TABLE `jackpot_pool` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`group_id` bigint(16) DEFAULT NULL,
`balance` decimal(10,2) DEFAULT '0.00',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `jackpot_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lucky_id` int(11) DEFAULT NULL COMMENT '红包id',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) DEFAULT NULL COMMENT '用户id',
  `group_id` bigint(16) DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `sender_id` bigint(16) DEFAULT NULL COMMENT '发包者id',
  `profit_amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1350 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

CREATE TABLE `jackpot_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lucky_id` int(11) DEFAULT NULL COMMENT '红包id',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `tg_id` bigint(16) DEFAULT NULL COMMENT '中奖用户id',
  `group_id` bigint(16) DEFAULT NULL COMMENT '组id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `sender_id` bigint(16) DEFAULT NULL COMMENT '发包者id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;


SET FOREIGN_KEY_CHECKS = 1;
