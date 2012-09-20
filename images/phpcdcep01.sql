/*
 Navicat Premium Data Transfer

 Source Server         : General Admin
 Source Server Type    : MySQL
 Source Server Version : 50162
 Source Host           : db-slave
 Source Database       : phpcdcep01

 Target Server Type    : MySQL
 Target Server Version : 50162
 File Encoding         : utf-8

 Date: 07/19/2012 17:48:56 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `tblUsers`
-- ----------------------------
DROP TABLE IF EXISTS `tblUsers`;
CREATE TABLE `tblUsers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `emailAddress` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `tblUsers`
-- ----------------------------
BEGIN;
INSERT INTO `tblUsers` VALUES ('1', 'matthew', 'setter', 'ms@example.com'), ('2', 'don', 'bradman', 'db@example.com'), ('3', 'alan', 'border', 'ab@example.com');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
