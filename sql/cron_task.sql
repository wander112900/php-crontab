/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.230
Source Server Version : 50527
Source Host           : 192.168.1.230:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2013-11-21 14:28:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `cron_task`
-- ----------------------------
DROP TABLE IF EXISTS `cron_task`;
CREATE TABLE `cron_task` (
  `cid` mediumint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  `program` varchar(50) DEFAULT NULL,
  `is_run` tinyint(2) DEFAULT NULL,
  `enable` tinyint(2) DEFAULT '0',
  `last_excute` int(11) DEFAULT '0',
  `retry` mediumint(5) DEFAULT '0',
  `fail_time` int(11) DEFAULT '0',
  `start_time` int(11) DEFAULT '0',
  `end_time` int(11) DEFAULT '0',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cron_task
-- ----------------------------
INSERT INTO `cron_task` VALUES ('1', '测试', '*/10 * * * * *', './Order.php', '0', '1', '1385014020', null, null, '1385014026', '1385014026');
