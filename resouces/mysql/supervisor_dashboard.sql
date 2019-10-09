-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: supervisor_dashboard
-- ------------------------------------------------------
-- Server version	5.7.27-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `command`
--

DROP TABLE IF EXISTS `command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `command` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `program` varchar(255) NOT NULL DEFAULT '' COMMENT '进程名称',
  `user` varchar(64) NOT NULL DEFAULT '' COMMENT '执行命令的用户',
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT '命令',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '启动时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COMMENT='命令执行记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron`
--

DROP TABLE IF EXISTS `cron`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `user` varchar(64) NOT NULL DEFAULT '' COMMENT '执行命令的用户',
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT '命令',
  `time` varchar(64) NOT NULL DEFAULT '' COMMENT '运行时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次运行时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COMMENT='定时任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_log`
--

DROP TABLE IF EXISTS `cron_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `cron_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '定时任务ID',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `user` varchar(64) NOT NULL DEFAULT '' COMMENT '执行命令的用户',
  `program` varchar(255) NOT NULL DEFAULT '' COMMENT '定时任务名称',
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT '命令',
  `status` smallint(4) NOT NULL DEFAULT '0' COMMENT '执行状态',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '启动时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27781 DEFAULT CHARSET=utf8mb4 COMMENT='定时任务执行记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `process`
--

DROP TABLE IF EXISTS `process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `process` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `program` varchar(64) NOT NULL DEFAULT '' COMMENT '程序名',
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT '命令',
  `process_name` varchar(64) NOT NULL DEFAULT '' COMMENT '进程名',
  `numprocs` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '进程数',
  `numprocs_start` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '进程下标起始值',
  `stopwaitsecs` int(10) unsigned NOT NULL DEFAULT '10' COMMENT '停止等待秒数',
  `directory` varchar(255) NOT NULL DEFAULT '' COMMENT '目录',
  `autostart` char(5) NOT NULL DEFAULT 'true' COMMENT '自动启动',
  `startretries` tinyint(3) unsigned NOT NULL DEFAULT '20' COMMENT '启动重试次数',
  `autorestart` char(5) NOT NULL DEFAULT 'true' COMMENT '自动启动',
  `user` varchar(32) NOT NULL DEFAULT '' COMMENT '执行进程的用户',
  `redirect_stderr` char(5) NOT NULL DEFAULT 'true' COMMENT '错误重定向(redirect_stderr)',
  `stdout_logfile` varchar(255) NOT NULL DEFAULT 'AUTO' COMMENT '标准输出日志文件(stdout_logfile)',
  `stdout_logfile_backups` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '标准输出日志备份',
  `stdout_logfile_maxbytes` varchar(64) NOT NULL DEFAULT '1MB' COMMENT '标准输出日志的最大字节数',
  `is_sys` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为系统进程',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `program` (`program`),
  KEY `server_id` (`server_id`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=5104 DEFAULT CHARSET=utf8mb4 COMMENT='进程配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `server`
--

DROP TABLE IF EXISTS `server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `server_group_id` int(11) unsigned NOT NULL COMMENT '服务器分组id',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT '服务器 IP',
  `port` int(10) unsigned NOT NULL COMMENT 'XML-RPC 端口',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT 'XML-RPC 用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT 'XML-RPC 密码',
  `agent_port` int(10) unsigned NOT NULL COMMENT 'supervisor-agent 监听的端口',
  `agent_root` varchar(255) NOT NULL DEFAULT '' COMMENT 'supervisor-agent 项目根目录',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_port` (`ip`,`port`),
  KEY `group_id` (`server_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COMMENT='服务器表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `server_group`
--

DROP TABLE IF EXISTS `server_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '组名',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '组描述',
  `sort` smallint(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序字段',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `suhua` varchar(100) NOT NULL DEFAULT '' COMMENT 'suhua',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COMMENT='服务器分组表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-09 16:39:18
