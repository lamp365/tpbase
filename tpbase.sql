-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 年 03 月 27 日 01:11
-- 服务器版本: 5.5.46-0ubuntu0.14.04.2
-- PHP 版本: 5.5.9-1ubuntu4.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `tpbase`
--

-- --------------------------------------------------------

--
-- 表的结构 `a_admin`
--

CREATE TABLE IF NOT EXISTS `a_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id 自增',
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户账号',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `phone` varchar(255) NOT NULL DEFAULT '' COMMENT '联系电话',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(30) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `login_num` int(10) NOT NULL DEFAULT '0' COMMENT '登录次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='后台管理员表' AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `a_admin`
--

INSERT INTO `a_admin` (`id`, `username`, `password`, `name`, `email`, `phone`, `sort`, `addtime`, `last_time`, `last_ip`, `status`, `login_num`) VALUES
(1, 'admin', '1a449bd939aa083e2aa5456fa3c4ce68', '刘建凡', 'jidun121@126.com', '18210560183', 99, 1449711626, 1490545022, '10.211.55.2', 1, 7),
(2, 'test', '9c1f2221917376f0eb78fbcac8b2ba0f', 'test365', 'test@126.com', '18210560183', 99, 1453465475, 1458629986, '127.0.0.1', 1, 0),
(16, 'kevin', '201615ceebe62be24c21b32d6f86a127', 'kevin.liu', '791845283@qq.com', '18850737047', 0, 1490426135, 1490426135, '127.0.0.1', 1, 0),
(17, 'hellow', '41d0a29d62eb523f78b68ea0bc527064', 'hellow', 'jidun121@126.com', '18210560183', 0, 1490427491, 1490427491, '10.211.55.2', 1, 0),
(18, 'lisi', '65aa6b85cb854ba98941bdce0e0965fc', '李四', 'jidun121@126.com', '18210008611', 0, 1490435227, 1490435227, '10.211.55.2', 1, 0),
(19, 'xiaosan', '49e12375747e56903f820c66d91ee2cd', '小三', 'jidun121@126.com', '18210560183', 0, 1490435293, 1490435293, '10.211.55.2', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `a_auth_cate`
--

CREATE TABLE IF NOT EXISTS `a_auth_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '等级',
  `module` varchar(50) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(50) NOT NULL DEFAULT '' COMMENT '控制器',
  `method` varchar(50) NOT NULL DEFAULT '' COMMENT '方法',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则路径',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级分类',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序规则',
  `condition` char(100) NOT NULL DEFAULT '',
  `is_menu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0菜单 1非菜单',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限菜单表' AUTO_INCREMENT=51 ;

--
-- 转存表中的数据 `a_auth_cate`
--

INSERT INTO `a_auth_cate` (`id`, `title`, `level`, `module`, `controller`, `method`, `name`, `pid`, `sort`, `condition`, `is_menu`, `status`) VALUES
(2, '网站配置', 1, 'Admin', 'Config', '', 'Admin/Config', 0, 99, '', 0, 1),
(3, '微信管理', 1, 'Admin', 'Weixin', '', 'Admin/Weixin', 0, 20, '', 1, 1),
(4, '基础设置', 2, 'Admin', 'Config', 'base', 'Admin/Config/base', 2, 100, '', 0, 1),
(5, '广告设置', 2, 'Admin', 'Adv', 'lists', 'Admin/Adv/lists', 2, 99, '', 0, 1),
(6, '分组列表', 2, 'Admin', 'Auth', 'group', 'Admin/Auth/group', 33, 6, '', 0, 1),
(7, '快捷登陆', 2, 'Admin', 'Thirdlogin', 'lists', 'Admin/Thirdlogin/lists', 2, 99, '', 0, 1),
(14, '菜单管理', 1, 'Admin', 'Menus', '', 'Admin/Menus', 0, 98, '', 0, 1),
(16, '主页菜单', 2, 'Admin', 'Menus', 'home', 'Admin/Menus/home', 14, 99, '', 0, 1),
(24, '个人中心', 2, 'Admin', 'Menus', 'center', 'Admin/Menus/center', 14, 99, '', 1, 1),
(28, '数据管理', 1, 'Admin', 'Database', '', 'Admin/Database', 0, 10, '', 1, 1),
(29, '备份数据', 2, 'Admin', 'Database', 'lists', 'Admin/Database/lists', 28, 99, '', 0, 1),
(31, '会员管理', 1, 'Admin', 'User', '', 'Admin/User', 0, 5, '', 0, 1),
(33, '权限管理', 1, 'Admin', 'Auth', '', 'Admin/Auth', 0, 0, '', 0, 1),
(35, '会员列表', 2, 'Admin', 'User', 'userlists', 'Admin/User/userlists', 31, 0, '', 0, 1),
(36, '管理员列表', 2, 'Admin', 'User', 'rootlists', 'Admin/User/rootlists', 31, 3, '', 0, 1),
(37, '菜单节点', 2, 'Admin', 'Auth', 'navlists', 'Admin/Auth/navlists', 33, 5, '', 0, 1),
(38, '会员等级', 2, 'Admin', 'User', 'level', 'Admin/User/level', 31, 0, '', 0, 1),
(39, '微信号设置', 2, 'Admin', 'Weixin', 'config', 'Admin/Weixin/config', 3, 0, '', 0, 1),
(40, '微信菜单', 2, 'Admin', 'Weixin', 'menu', 'Admin/Weixin/menu', 3, 0, '', 0, 1),
(41, '自定义回复', 2, 'Admin', 'Weixin', 'replay', 'Admin/Weixin/replay', 3, 0, '', 0, 1),
(42, '关注回复', 2, 'Admin', 'Weixin', 'guanzhu', 'Admin/Weixin/guanzhu', 3, 0, '', 0, 1),
(43, '恢复数据', 2, 'Admin', 'Database', 'return', 'Admin/Database/return', 28, 0, '', 0, 1),
(44, '支付方式', 2, 'Admin', 'Payway', 'lists', 'Admin/Payway/lists', 2, 0, '', 0, 1),
(45, '清除缓存', 2, 'Admin', 'Database', 'clcache', 'Admin/Database/clcache', 28, 0, '', 0, 1),
(46, '查子菜单', 2, 'Admin', 'Auth', 'showson', 'Admin/Auth/showson', 37, 0, '', 0, 1),
(47, '修改菜单', 3, 'Admin', 'Auth', 'editmenu', 'Admin/Auth/editmenu', 37, 0, '', 0, 1),
(48, '添加分类菜单', 3, 'Admin', 'Auth', 'addcate', 'Admin/Auth/addcate', 37, 3, '', 0, 1),
(49, '添加分组', 3, 'Admin', 'Auth', 'addgroup', 'Admin/Auth/addgroup', 6, 0, '', 0, 1),
(50, '分配权限', 3, 'Admin', 'Auth', 'groupaccess', 'Admin/Auth/groupaccess', 6, 0, '', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `a_group`
--

CREATE TABLE IF NOT EXISTS `a_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(1000) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '0',
  `desc` varchar(255) DEFAULT '' COMMENT '简介',
  `createtime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分组表' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `a_group`
--

INSERT INTO `a_group` (`id`, `title`, `status`, `rules`, `sort`, `desc`, `createtime`) VALUES
(1, '主管管理', 1, '14,24,16,3,42,41,40,39,28,29,45,43,31,36,35,38,33,6,49,50,37,48,47,46', 8, '基本具备所有', 1490498077),
(4, '财务管理', 1, '3,42,41,28,29,45,43,31,36,35,38', 4, '只能管理财务模块', 1490498081),
(5, '客服管理', 1, '2,4,7,5,44,14,24', 3, '管理用户模块和营销模块', 1490457564);

-- --------------------------------------------------------

--
-- 表的结构 `a_group_access`
--

CREATE TABLE IF NOT EXISTS `a_group_access` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户-用户组关联表';

--
-- 转存表中的数据 `a_group_access`
--

INSERT INTO `a_group_access` (`uid`, `group_id`) VALUES
(1, 1),
(2, 1),
(1, 4),
(16, 4),
(16, 5),
(17, 5),
(18, 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
