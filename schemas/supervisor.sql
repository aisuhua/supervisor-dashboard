-- MySQL dump 10.16  Distrib 10.2.21-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: supervisor
-- ------------------------------------------------------
-- Server version	10.2.21-MariaDB-10.2.21+maria~xenial

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
-- Table structure for table `program`
--

DROP TABLE IF EXISTS `program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `server_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '服务器ID',
  `program` varchar(64) NOT NULL DEFAULT '' COMMENT '程序名',
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT '命令',
  `process_name` varchar(64) NOT NULL DEFAULT '' COMMENT '进程名',
  `numprocs` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '进程数',
  `numprocs_start` int(10) unsigned NOT NULL DEFAULT 1 COMMENT '进程下标起始值',
  `directory` varchar(255) NOT NULL DEFAULT '' COMMENT '目录',
  `autostart` char(5) NOT NULL DEFAULT 'true' COMMENT '自动启动',
  `startretries` tinyint(3) unsigned NOT NULL DEFAULT 20 COMMENT '启动重试次数',
  `autorestart` char(5) NOT NULL DEFAULT 'true' COMMENT '自动启动',
  `user` varchar(32) NOT NULL DEFAULT '' COMMENT '执行进程的用户',
  `redirect_stderr` char(5) NOT NULL DEFAULT 'true' COMMENT '错误重定向(redirect_stderr)',
  `stdout_logfile` varchar(255) NOT NULL DEFAULT 'AUTO' COMMENT '标准输出日志文件(stdout_logfile)',
  `stdout_logfile_backups` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '标准输出日志备份',
  `stdout_logfile_maxbytes` varchar(64) NOT NULL DEFAULT '1MB' COMMENT '标准输出日志的最大字节数',
  `update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `program` (`program`),
  KEY `server_id` (`server_id`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1370 DEFAULT CHARSET=utf8mb4 COMMENT='进程配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program`
--

LOCK TABLES `program` WRITE;
/*!40000 ALTER TABLE `program` DISABLE KEYS */;
INSERT INTO `program` VALUES (357,47,'SUPERVISOR_COMMAND_EXEC_SERVICE','/usr/bin/php /www/web/supervisor.ops.115.com/worker/SUPERVISOR_COMMAND_EXEC_SERVICE.php --server-ip=172.16.210.76','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'true','','true','AUTO',0,'1M',1566983140,1566983140),(358,47,'cat1','/bin/cat','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'false','','false','AUTO',1,'10M',1566983171,1566983171),(1129,46,'cat2','/bin/cat','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'false','www-data','true','AUTO',0,'1M',0,0),(1130,46,'FOLDER_ID_TRANSCODE','/usr/bin/php /www/web/site.115.com/worker/transcode/FOLDER_ID_TRANSCODE.php','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'true','www-data','true','AUTO',0,'1M',0,0),(1131,46,'PUSHARE_CLEAN_EXPIRED_NEW','/usr/bin/php /www/web/site.115.com/worker/pushare/PUSHARE_CLEAN_EXPIRED_NEW.php','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'true','www-data','true','AUTO',0,'1M',0,0),(1132,46,'PUSHARE_RECV_FILE_0','/usr/bin/php /www/web/site.115.com/worker/pushare/PUSHARE_RECV_FILE.php -u  %(process_num)s','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'true','www-data','true','AUTO',0,'1M',0,0),(1366,41,'aria2c','aria2c --conf-path=/etc/aria2c.conf','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'true','suhua','true','AUTO',0,'1MB',1567419768,0),(1367,41,'cat1','/bin/cat','%(program_name)s_%(process_num)s',1,0,'%(here)s','true',20,'true','www-data','true','AUTO',0,'1MB',1567471865,0),(1368,41,'demo','php /www/web/demo.php','%(program_name)s_%(process_num)s',3,0,'%(here)s','true',20,'true','www-data','true','AUTO',0,'1MB',1567472097,0);
/*!40000 ALTER TABLE `program` ENABLE KEYS */;
UNLOCK TABLES;

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
  `conf_path` varchar(255) NOT NULL DEFAULT '' COMMENT '配置文件写入的路径',
  `sync_conf_port` smallint(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新配置服务所监听的端口',
  `sort` smallint(11) unsigned NOT NULL DEFAULT 0 COMMENT '排序字段',
  `update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_port` (`ip`,`port`),
  KEY `group_id` (`server_group_id`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COMMENT='服务器表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `server`
--

LOCK TABLES `server` WRITE;
/*!40000 ALTER TABLE `server` DISABLE KEYS */;
INSERT INTO `server` VALUES (41,161,'192.168.1.229',9001,'worker','111111','/etc/supervisor/conf.d/program.conf',8089,0,1567147230,1566459420),(44,99,'172.16.0.63',9001,'worker','111111','/etc/supervisor/conf.d/program.conf',8089,0,1567147277,1566464463),(46,99,'172.16.0.61',9001,'worker','111111','/etc/supervisor/conf.d/program.conf',8089,11,1567147488,1566475933),(47,99,'172.16.0.69',9001,'worker','111111','/etc/supervisor/conf.d/program.conf',8089,0,1567147363,1566475973);
/*!40000 ALTER TABLE `server` ENABLE KEYS */;
UNLOCK TABLES;

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
  `sort` smallint(11) unsigned NOT NULL DEFAULT 0 COMMENT '排序字段',
  `update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4 COMMENT='服务器分组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `server_group`
--

LOCK TABLES `server_group` WRITE;
/*!40000 ALTER TABLE `server_group` DISABLE KEYS */;
INSERT INTO `server_group` VALUES (99,'115内网','115内网',10,1566483736,0),(161,'默认分组','系统内置分组',100,1566822895,1566348880);
/*!40000 ALTER TABLE `server_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'supervisor'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-03 10:53:55
