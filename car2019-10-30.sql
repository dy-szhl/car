/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : car

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-10-30 18:21:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `car_goods`
-- ----------------------------
DROP TABLE IF EXISTS `car_goods`;
CREATE TABLE `car_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT '0' COMMENT '一级商品分类',
  `ct_id` int(11) DEFAULT '0' COMMENT '二级分类id',
  `name` varchar(150) DEFAULT NULL,
  `og_price` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '售价',
  `price0` decimal(10,2) DEFAULT '0.00' COMMENT '会员价',
  `price1` decimal(10,2) DEFAULT '0.00' COMMENT '会员价',
  `price2` decimal(10,2) DEFAULT '0.00' COMMENT '会员价',
  `img` text CHARACTER SET utf8mb4 COMMENT '封面图',
  `spu` text CHARACTER SET utf8mb4,
  `stock` int(10) unsigned DEFAULT '0',
  `intro` varchar(255) DEFAULT NULL,
  `content` text COMMENT '详细资料',
  `sold_num` int(11) DEFAULT '0' COMMENT '销量',
  `is_hot` tinyint(4) DEFAULT '0' COMMENT '是否热门',
  `is_new` tinyint(4) DEFAULT '0' COMMENT '新品',
  `status` tinyint(4) DEFAULT NULL,
  `sort` tinyint(4) DEFAULT '100',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `update_time` (`update_time`),
  KEY `sort` (`sort`),
  KEY `status` (`status`),
  KEY `price` (`price`),
  KEY `og_price` (`og_price`),
  KEY `price0` (`price0`),
  KEY `price1` (`price1`),
  KEY `price2` (`price2`),
  KEY `sold_num` (`sold_num`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of car_goods
-- ----------------------------
INSERT INTO `car_goods` VALUES ('1', '1', '0', '商品名', '0.00', '0.00', '999.99', '999.99', '999.99', '/uploads/article/20191025/1U0d88d54bef0518b277aa4f4cc69fa769.jpg', '[]', '999', '', null, '0', '1', '1', '1', '100', '1571973274', '1572425848', null);
INSERT INTO `car_goods` VALUES ('2', '1', '0', '润滑油2', '0.00', '0.00', '1.00', '2.00', '3.00', '/uploads/article/20191025/1U9382981b60ac87fda52bdd53c7296ae1.png', '[{\"name\":\"品牌\",\"value\":\"\"},{\"name\":\"材质\",\"value\":\"\"},{\"name\":\"尺寸\",\"value\":\"\"}]', '999', '', '<p>111123</p>', '0', '0', '0', '1', '100', '1571973433', '1572259307', null);

-- ----------------------------
-- Table structure for `car_goods_cate`
-- ----------------------------
DROP TABLE IF EXISTS `car_goods_cate`;
CREATE TABLE `car_goods_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `icon` text,
  `font_img` text,
  `img` text,
  `sort` tinyint(4) DEFAULT '100',
  `status` tinyint(4) DEFAULT '1',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='商品品牌';

-- ----------------------------
-- Records of car_goods_cate
-- ----------------------------
INSERT INTO `car_goods_cate` VALUES ('1', '0', '润滑油', null, null, '', '1', '1', '1571971210', '1571971210', null);
INSERT INTO `car_goods_cate` VALUES ('2', '0', '滤清器', null, null, '', '2', '1', '1571971235', '1571971235', null);
INSERT INTO `car_goods_cate` VALUES ('3', '1', '汽机油', null, null, '', '1', '1', '1571971259', '1571971259', null);

-- ----------------------------
-- Table structure for `car_image`
-- ----------------------------
DROP TABLE IF EXISTS `car_image`;
CREATE TABLE `car_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT '0',
  `img` text,
  `title` text,
  `url` text,
  `status` tinyint(4) DEFAULT '1',
  `sort` tinyint(4) DEFAULT '100',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='各种图片';

-- ----------------------------
-- Records of car_image
-- ----------------------------
INSERT INTO `car_image` VALUES ('1', '0', '/uploads/brand/20191025/1U8e684550493ce512a0f098e42e33cea1.png', '21', '123', '1', '100', '1571971135', '1571971135', null);

-- ----------------------------
-- Table structure for `car_user`
-- ----------------------------
DROP TABLE IF EXISTS `car_user`;
CREATE TABLE `car_user` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT '0' COMMENT '用户类型',
  `name` varchar(50) DEFAULT NULL COMMENT '用户名',
  `money` decimal(10,2) DEFAULT '0.00',
  `email` varchar(60) DEFAULT NULL COMMENT '邮箱',
  `sex` tinyint(4) DEFAULT '0' COMMENT '性别',
  `face` varchar(255) DEFAULT NULL COMMENT '头像',
  `phone` bigint(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `salt` char(4) DEFAULT NULL,
  `min_openid` varchar(50) DEFAULT NULL COMMENT '小程序openid',
  `auth_key` varchar(60) DEFAULT NULL,
  `status` int(5) DEFAULT '1' COMMENT '状态',
  `create_time` int(18) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(18) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of car_user
-- ----------------------------
INSERT INTO `car_user` VALUES ('1', '0', '9956用户', '0.00', null, '0', null, '18702629956', null, null, null, null, null, '1', '1572252952', '1572252952', null);
INSERT INTO `car_user` VALUES ('2', '0', '3614用户', '0.00', null, '0', '/assets/images/avatar04.png', '18702783614', null, null, null, null, null, '1', '1572417767', '1572417767', null);

-- ----------------------------
-- Table structure for `car_user_cars`
-- ----------------------------
DROP TABLE IF EXISTS `car_user_cars`;
CREATE TABLE `car_user_cars` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `number` varchar(15) DEFAULT NULL COMMENT '车牌',
  `brand_model` varchar(255) DEFAULT NULL COMMENT '品牌+车型',
  `car_frame` varchar(30) DEFAULT NULL COMMENT '车架',
  `engine` varchar(20) DEFAULT NULL COMMENT '发动机号',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '默认车型 ：1 是；0 否',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='爱车';

-- ----------------------------
-- Records of car_user_cars
-- ----------------------------
INSERT INTO `car_user_cars` VALUES ('1', '1', '京S15566', 'qq 袖珍', 'XLF416516545646', 'LLF1899SS45', '1', null, '1572321289', '1572318897');

-- ----------------------------
-- Table structure for `car_user_feedback`
-- ----------------------------
DROP TABLE IF EXISTS `car_user_feedback`;
CREATE TABLE `car_user_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `content` text,
  `phone` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户反馈';

-- ----------------------------
-- Records of car_user_feedback
-- ----------------------------

-- ----------------------------
-- Table structure for `car_user_notice`
-- ----------------------------
DROP TABLE IF EXISTS `car_user_notice`;
CREATE TABLE `car_user_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT '0' COMMENT '通知类型',
  `cond_id` int(11) DEFAULT '0' COMMENT '条件id',
  `uid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`uid`),
  KEY `uid` (`uid`,`cond_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统通知';

-- ----------------------------
-- Records of car_user_notice
-- ----------------------------

-- ----------------------------
-- Table structure for `car_user_notice_read`
-- ----------------------------
DROP TABLE IF EXISTS `car_user_notice_read`;
CREATE TABLE `car_user_notice_read` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `nid` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`nid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阅读';

-- ----------------------------
-- Records of car_user_notice_read
-- ----------------------------

-- ----------------------------
-- Table structure for `sms`
-- ----------------------------
DROP TABLE IF EXISTS `sms`;
CREATE TABLE `sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT '0',
  `phone` bigint(20) DEFAULT NULL COMMENT '手机号码',
  `content` text,
  `verify` char(6) DEFAULT NULL,
  `use_time` datetime DEFAULT NULL COMMENT '使用时间',
  `info` text,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='短信';

-- ----------------------------
-- Records of sms
-- ----------------------------
INSERT INTO `sms` VALUES ('1', '0', '18702783614', '您本次验证码为66624。请您尽快输入验证，谢谢。', '66624', null, '0,2019103011225076759708739,0,1,0,提交成功', '1572405769', '1572405769');

-- ----------------------------
-- Table structure for `sys_manager`
-- ----------------------------
DROP TABLE IF EXISTS `sys_manager`;
CREATE TABLE `sys_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `account` varchar(50) DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `rid` int(11) DEFAULT '0',
  `password` char(32) DEFAULT NULL,
  `salt` char(5) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1正常 2禁用',
  `login_times` int(11) DEFAULT NULL,
  `last_login_ip` varchar(50) DEFAULT NULL,
  `last_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account` (`account`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='系统管理员';

-- ----------------------------
-- Records of sys_manager
-- ----------------------------
INSERT INTO `sys_manager` VALUES ('1', 'admin', 'admin', '18702783615', '1', '2e7db8a1a08e24caa0c5c582789d514b', '70214', '1', '16', '127.0.0.1', '1572422167', '1557994845', '1572422167', null);
INSERT INTO `sys_manager` VALUES ('2', 'admin_group_1', 'admin_group_1', '18702783614', '2', '4bd562752cf0e79b7f91feb3b91e63b4', '43423', '1', null, null, null, '1557994975', '1557994975', '1568689050');
INSERT INTO `sys_manager` VALUES ('3', 'test', 'test', null, '1', '16f1dd3b0b0083312530f6c466479c9e', '31143', '1', null, null, null, '1557995091', '1557995091', '1557995097');
INSERT INTO `sys_manager` VALUES ('4', 'store_1', 'store_1', '18702783614', '2', 'd75c715f0df327f1f6332e766509b56f', '43436', '1', null, null, null, '1559614515', '1559614515', '1559648695');

-- ----------------------------
-- Table structure for `sys_nav_page`
-- ----------------------------
DROP TABLE IF EXISTS `sys_nav_page`;
CREATE TABLE `sys_nav_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `en` varchar(100) DEFAULT NULL,
  `route` varchar(100) NOT NULL DEFAULT '',
  `route_alias` varchar(100) DEFAULT NULL,
  `html` text NOT NULL,
  `image` text NOT NULL,
  `content` text,
  `key` text NOT NULL,
  `desc` text NOT NULL,
  `is_tip` tinyint(4) DEFAULT '0' COMMENT '推送',
  `is_top` tinyint(4) DEFAULT '0' COMMENT '置顶-顶级栏目',
  `sort` smallint(6) NOT NULL DEFAULT '0',
  `status` tinyint(4) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='栏目管理具体页面';

-- ----------------------------
-- Records of sys_nav_page
-- ----------------------------
INSERT INTO `sys_nav_page` VALUES ('1', '0', '2', '商品優惠', null, 'goods/index', '', '', '', '<p><span style=\"color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, &quot;PingFang SC&quot;, Tahoma, Arial, sans-serif; font-size: 14px; font-weight: 700; text-align: right; background-color: rgb(255, 255, 255);\">栏目介绍</span></p>', 'SEO关键字', 'SEO描述', '0', '1', '1', '1', '1559702921', '1559702944', null);
INSERT INTO `sys_nav_page` VALUES ('2', '0', '2', '按類別選購', null, 'goods/index', '', '', '', '', '', '', '0', '0', '100', '1', '1559702984', '1559702984', null);
INSERT INTO `sys_nav_page` VALUES ('3', '2', '2', '套裝組', '', '', '', '', '', '<p><span style=\"color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, &quot;PingFang SC&quot;, Tahoma, Arial, sans-serif; font-size: 14px; font-weight: 700; text-align: right; background-color: rgb(255, 255, 255);\">栏目介绍</span></p>', 'SEO关键字', 'SEO描述', '0', '1', '2', '1', '1559703024', '1559716197', null);
INSERT INTO `sys_nav_page` VALUES ('4', '2', '2', '獺祭二割三分', 'TAJI TWO CUT THREE POINTS', '', '', '', '', '', '', '', '1', '1', '3', '1', '1559703045', '1559715744', null);
INSERT INTO `sys_nav_page` VALUES ('5', '2', '0', '遠心分離', null, '', '', '', '', '<p><span style=\"color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, &quot;PingFang SC&quot;, Tahoma, Arial, sans-serif; font-size: 14px; font-weight: 700; text-align: right; background-color: rgb(255, 255, 255);\">栏目介绍</span></p>', 'SEO描述', 'SEO描述', '0', '1', '4', '1', '1559703063', '1559703081', null);
INSERT INTO `sys_nav_page` VALUES ('6', '0', '0', '關於我們', '', 'article/about', '', '', '/assets/uploads/nav/2019-06-05/663eac992c392f9052eab1d179be617c.jpg', '<p><img src=\"http://yii.mjbs.com/assets/images/about.jpg\"/></p>', 'SEO关键字關於我們', 'SEO描述關於我們', '0', '1', '5', '1', '1559703156', '1559722961', null);
INSERT INTO `sys_nav_page` VALUES ('7', '2', '2', '三割九分', null, '', '', '', '', '', 'EO关键字', 'SEO描述', '0', '1', '6', '1', '1559703187', '1559703187', null);
INSERT INTO `sys_nav_page` VALUES ('8', '2', '2', '純米大吟嚷', 'PURE METERS BIG YIN WINE', '', '', '', '', '', '', '', '1', '1', '9', '1', '1559703205', '1559715801', null);
INSERT INTO `sys_nav_page` VALUES ('9', '2', '2', '獺祭發泡濁酒', 'SACRIFICE PRODUCES SPARKLING WINE', '', '', '', '', null, '', '', '1', '1', '8', '1', '1559713230', '1559715766', null);

-- ----------------------------
-- Table structure for `sys_node`
-- ----------------------------
DROP TABLE IF EXISTS `sys_node`;
CREATE TABLE `sys_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `pid` int(11) DEFAULT '0' COMMENT '父id',
  `status` tinyint(4) DEFAULT '1' COMMENT '平台 状态 1显示 0关闭 -1叶子节点',
  `sort` tinyint(4) DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COMMENT='页面节点';

-- ----------------------------
-- Records of sys_node
-- ----------------------------
INSERT INTO `sys_node` VALUES ('1', null, '系统设置', 'fa fa-home', '0', '1', '100');
INSERT INTO `sys_node` VALUES ('2', null, '网站管理员', null, '1', '1', '2');
INSERT INTO `sys_node` VALUES ('3', null, '用户管理', 'fa fa-weixin', '0', '1', '1');
INSERT INTO `sys_node` VALUES ('4', null, '栏目管理', 'fa fa-leaf', '0', '1', '3');
INSERT INTO `sys_node` VALUES ('5', null, '新闻管理', 'fa fa-leanpub', '0', '1', '4');
INSERT INTO `sys_node` VALUES ('6', null, '产品管理', 'fa fa-cubes', '0', '1', '5');
INSERT INTO `sys_node` VALUES ('7', null, '订单管理', 'fa fa-line-chart', '0', '1', '6');
INSERT INTO `sys_node` VALUES ('8', null, '广告管理', 'fa fa-video-camera', '0', '0', '9');
INSERT INTO `sys_node` VALUES ('9', 'system/setting', '基本资料', null, '1', '1', '1');
INSERT INTO `sys_node` VALUES ('10', 'index/setting', '分销设置', null, '1', '0', '2');
INSERT INTO `sys_node` VALUES ('11', 'system/protocol', '服务协议', null, '1', '1', '1');
INSERT INTO `sys_node` VALUES ('12', 'system/manager-add', '添加/编辑', null, '13', '1', '100');
INSERT INTO `sys_node` VALUES ('13', 'system/manager', '管理员列表', null, '2', '1', '100');
INSERT INTO `sys_node` VALUES ('14', 'user/index', '用户列表', null, '3', '1', '100');
INSERT INTO `sys_node` VALUES ('15', 'system/nav-page-add', '添加栏目', null, '4', '1', '100');
INSERT INTO `sys_node` VALUES ('16', 'system/nav-page', '栏目列表', null, '4', '1', '100');
INSERT INTO `sys_node` VALUES ('17', 'article/add', '添加新闻', null, '5', '1', '100');
INSERT INTO `sys_node` VALUES ('18', 'article/index', '新闻列表', null, '5', '1', '100');
INSERT INTO `sys_node` VALUES ('19', 'goods/add', '添加/编辑', null, '20', '-1', '100');
INSERT INTO `sys_node` VALUES ('20', 'goods/index', '产品列表', null, '6', '1', '100');
INSERT INTO `sys_node` VALUES ('21', 'order/index', '订单列表', null, '7', '1', '100');
INSERT INTO `sys_node` VALUES ('23', 'system/ad-add', '添加/编辑', null, '24', '-1', '100');
INSERT INTO `sys_node` VALUES ('24', 'system/ad', '广告列表', null, '1', '1', '1');
INSERT INTO `sys_node` VALUES ('25', 'system/roles', '角色管理', null, '2', '1', '1');
INSERT INTO `sys_node` VALUES ('26', 'system/roles-add', '添加/编辑', null, '36', '1', '100');
INSERT INTO `sys_node` VALUES ('27', 'system/ad-del', '删除', null, '8', '-1', '100');
INSERT INTO `sys_node` VALUES ('28', 'user/detail', '详情', null, '17', '-1', '100');
INSERT INTO `sys_node` VALUES ('29', 'goods/brand', '品牌管理', null, '6', '1', '100');
INSERT INTO `sys_node` VALUES ('30', 'goods/brand-add', '新增/编辑', null, '29', '-1', '100');
INSERT INTO `sys_node` VALUES ('31', 'goods/brand-del', '删除', null, '29', '-1', '100');
INSERT INTO `sys_node` VALUES ('32', 'order/detail', '详情', null, '21', '-1', '100');
INSERT INTO `sys_node` VALUES ('33', 'goods/comment', '商品评论', null, '6', '1', '100');

-- ----------------------------
-- Table structure for `sys_role`
-- ----------------------------
DROP TABLE IF EXISTS `sys_role`;
CREATE TABLE `sys_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0' COMMENT '顶级',
  `name` varchar(50) DEFAULT NULL COMMENT '角色名称',
  `node` text COMMENT '可操作的权限节点',
  `sort` tinyint(4) DEFAULT '100' COMMENT '排序',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  `is_sys` tinyint(4) DEFAULT '0' COMMENT '系统指定角色 ',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色';

-- ----------------------------
-- Records of sys_role
-- ----------------------------
INSERT INTO `sys_role` VALUES ('1', '0', '超级管理员', null, '1', '1', '1', '1557994721', '1557994721', null);
INSERT INTO `sys_role` VALUES ('2', '0', '角色', null, '2', '1', '0', '1557994747', '1557998307', null);
INSERT INTO `sys_role` VALUES ('3', '0', 'test1', 'user/index', '2', '2', '0', '1557995046', '1557995056', '1558002637');
INSERT INTO `sys_role` VALUES ('4', '0', '菜单栏管理', 'user/index,system/nav,system/nav-add', '100', '1', '0', '1558002561', '1558002561', '1559615663');

-- ----------------------------
-- Table structure for `sys_setting`
-- ----------------------------
DROP TABLE IF EXISTS `sys_setting`;
CREATE TABLE `sys_setting` (
  `type` varchar(50) NOT NULL DEFAULT '',
  `content` text,
  PRIMARY KEY (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='系统缓存数据';

-- ----------------------------
-- Records of sys_setting
-- ----------------------------
INSERT INTO `sys_setting` VALUES ('normal_vip_money', '112');
INSERT INTO `sys_setting` VALUES ('normal_up_vip_money', '221');
INSERT INTO `sys_setting` VALUES ('normal_tel', '33323');
INSERT INTO `sys_setting` VALUES ('hot_key', '关键字1\r\n关键字\r\n关键字2');
INSERT INTO `sys_setting` VALUES ('normal_drawwith', '50');
INSERT INTO `sys_setting` VALUES ('normal_commission_one_award', '0.1');
