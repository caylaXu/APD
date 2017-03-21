-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 广03 朿21 旿19:24
-- 服务器版本: 5.6.21
-- PHP 版本: 5.5.18

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `APD0317`
--

-- --------------------------------------------------------

--
-- 表的结构 `Checklist`
--

CREATE TABLE IF NOT EXISTS `Checklist` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL COMMENT '项目名称',
  `TaskId` bigint(15) DEFAULT NULL,
  `IsComplete` tinyint(2) DEFAULT '0' COMMENT '完成度',
  `CreateTime` int(11) DEFAULT '0' COMMENT '创建时间',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1：已删除 0：未激活 1：已激活',
  `Method` tinyint(4) DEFAULT '0' COMMENT '0:新建 1：修改 -1：删除 9已同步 ',
  `Modified` int(11) DEFAULT '0' COMMENT '本地版本',
  `UniCode` varchar(25) DEFAULT '0' COMMENT '唯一识别',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `Config`
--

CREATE TABLE IF NOT EXISTS `Config` (
  `Id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Type` tinyint(4) DEFAULT '1' COMMENT '1:提醒设置',
  `UserId` bigint(15) DEFAULT '0' COMMENT '用户Id',
  `Value` varchar(1000) NOT NULL COMMENT '具体配置：json串',
  `Status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '-1:已删除 0：未激活 1：已激活',
  `Method` tinyint(2) NOT NULL DEFAULT '0' COMMENT '-1：删除 0：新增 1：更新 ',
  `Modified` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='配置表' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `Groups`
--

CREATE TABLE IF NOT EXISTS `Groups` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL COMMENT '团队名称',
  `HeadId` bigint(15) DEFAULT NULL COMMENT '团队领导id',
  `CreatorId` bigint(15) DEFAULT '0' COMMENT '创建者id',
  `CreateTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1已删除 0未激活 1已激活',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  `UniCode` bigint(15) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `Projects`
--

CREATE TABLE IF NOT EXISTS `Projects` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL COMMENT '项目名称',
  `Description` varchar(255) DEFAULT NULL COMMENT '项目描述',
  `ProjectManagerId` bigint(15) DEFAULT '0',
  `ParentId` bigint(15) NOT NULL DEFAULT '0' COMMENT '父级项目Id',
  `StartDate` int(11) DEFAULT NULL COMMENT '开始时间',
  `DueDate` int(11) DEFAULT NULL COMMENT '完成时间',
  `TrueDueDate` int(11) DEFAULT NULL COMMENT '实际结束时间',
  `CreateTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `CreatorId` bigint(15) DEFAULT '0' COMMENT '创建者id',
  `CompleteProgress` tinyint(2) DEFAULT '0' COMMENT '0到100',
  `Status` int(4) DEFAULT '1' COMMENT '-1：已删除 0：未激活 1：已激活',
  `Method` tinyint(4) DEFAULT '0' COMMENT '0:新建 1：修改 -1:删除',
  `Modified` int(11) DEFAULT '0' COMMENT '本地版本',
  `UniCode` bigint(15) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;



--
-- 表的结构 `RltGroupUser`
--

CREATE TABLE IF NOT EXISTS `RltGroupUser` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `GroupId` int(20) DEFAULT '0',
  `UserId` int(20) DEFAULT '0',
  `Status` tinyint(4) DEFAULT '1',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `RltProjectUser`
--

CREATE TABLE IF NOT EXISTS `RltProjectUser` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `ProjectId` bigint(15) DEFAULT '0',
  `UserId` bigint(15) DEFAULT '0',
  `Type` int(4) NOT NULL DEFAULT '1' COMMENT '1:责任人2：普通成员3：关注人',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1:已删除 0：未激活 1：已激活',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;



--
-- 表的结构 `RltTag`
--

CREATE TABLE IF NOT EXISTS `RltTag` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `TagId` int(20) DEFAULT '0' COMMENT '标签id',
  `RelationId` int(20) DEFAULT '0',
  `Type` tinyint(4) DEFAULT '1' COMMENT '关联id类型',
  `Status` tinyint(4) DEFAULT '0',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `RltTaskUser`
--

CREATE TABLE IF NOT EXISTS `RltTaskUser` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `TaskId` bigint(15) DEFAULT '0',
  `UserId` bigint(15) DEFAULT '0',
  `Type` tinyint(4) DEFAULT '1' COMMENT '1:责任人2：主责任人3:关注人',
  `Remind` varchar(1000) DEFAULT '' COMMENT '具体提醒：json串',
  `Duration` int(11) unsigned DEFAULT '0' COMMENT '任务开始后历时',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1已删除 0：未激活 1：已激活 2：进行中',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;



--
-- 表的结构 `Tags`
--

CREATE TABLE IF NOT EXISTS `Tags` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL COMMENT '头衔',
  `Type` tinyint(4) DEFAULT '0' COMMENT '标签类型',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1已删除 0：未激活 1：已激活',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  `UniCode` bigint(15) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `Tasks`
--

CREATE TABLE IF NOT EXISTS `Tasks` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL COMMENT '项目名称',
  `Description` varchar(255) DEFAULT NULL COMMENT '任务描述',
  `ProjectId` bigint(15) NOT NULL DEFAULT '0' COMMENT '项目描述',
  `StartDate` int(11) DEFAULT NULL COMMENT '预计开始时间',
  `DueDate` int(11) DEFAULT NULL COMMENT '预计结束时间',
  `Priority` tinyint(4) DEFAULT '0' COMMENT '优先级',
  `ParentId` bigint(15) DEFAULT '0' COMMENT '父id',
  `IsMilestone` tinyint(2) DEFAULT '0' COMMENT '0:不是1：是',
  `CompleteProgress` tinyint(2) DEFAULT '0' COMMENT '完成度',
  `TrueDueDate` int(11) DEFAULT '0' COMMENT '真正的完成时间',
  `CreateTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `CreatorId` bigint(15) DEFAULT '0' COMMENT '创建者id',
  `FinisherId` bigint(15) NOT NULL DEFAULT '0' COMMENT '完成者Id',
  `Sort` bigint(15) DEFAULT '0' COMMENT '排序字段',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1：已删除 0：未激活 1：已激活',
  `Method` tinyint(4) DEFAULT '0' COMMENT '0:新建 1：修改 -1：删除 9已同步 ',
  `Modified` int(11) DEFAULT '0' COMMENT '本地版本',
  `UniCode` bigint(15) DEFAULT '0' COMMENT '唯一识别',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;



--
-- 表的结构 `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `Email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `Mobile` char(11) DEFAULT NULL COMMENT '手机',
  `Title` varchar(255) DEFAULT NULL COMMENT '头衔',
  `Avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `RegistrationDate` int(11) DEFAULT NULL COMMENT '注册时间',
  `Password` varchar(50) DEFAULT NULL,
  `Salt` int(11) NOT NULL DEFAULT '0' COMMENT '盐',
  `Status` tinyint(4) DEFAULT '1' COMMENT '-1:已删除 0：未激活 1：已激活',
  `Method` tinyint(4) DEFAULT '0',
  `Modified` int(11) DEFAULT '0',
  `UniCode` bigint(15) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
