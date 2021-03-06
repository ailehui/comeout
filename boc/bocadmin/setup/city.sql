/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.0.95 : Database - sq_osdrichcom
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `boc_city` */

DROP TABLE IF EXISTS `boc_city`;

CREATE TABLE `boc_city` (
  `id` int(11) NOT NULL auto_increment,
  `yid` int(11) default NULL,
  `type_id` int(11) default NULL,
  `sort_id` int(11) default NULL,
  `depth` tinyint(4) default NULL,
  `path` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `parent_id` int(11) default NULL,
  `photo` varchar(255) default NULL,
  `thumb` varchar(255) default NULL,
  `timeline` int(13) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Data for the table `boc_city` */

insert  into `boc_city`(`id`,`yid`,`type_id`,`sort_id`,`depth`,`path`,`title`,`parent_id`,`photo`,`thumb`,`timeline`) values (1,110000,0,0,0,'','北京市',0,'','',1368120198),(2,120000,0,0,0,'','天津市',0,'','',1368120198),(3,130000,0,0,0,'','河北省',0,'','',1368120198),(4,140000,0,0,0,'','山西省',0,'','',1368120198),(5,150000,0,0,0,'','内蒙自治区',0,'','',1368120198),(6,210000,0,0,0,'','辽宁省',0,'','',1368120198),(7,220000,0,0,0,'','吉林市',0,'','',1368120198),(8,230000,0,0,0,'','黑龙江省',0,'','',1368120198),(9,310000,0,0,0,'','上海市',0,'','',1368120198),(10,320000,0,0,0,'','江苏省',0,'','',1368120198),(11,330000,0,0,0,'','浙江省',0,'','',1368120198),(12,340000,0,0,0,'','安徽省',0,'','',1368120198),(13,350000,0,0,0,'','福建省',0,'','',1368120198),(14,360000,0,0,0,'','江西省',0,'','',1368120198),(15,370000,0,0,0,'','山东省',0,'','',1368120198),(16,410000,0,0,0,'','河南省',0,'','',1368120198),(17,420000,0,0,0,'','湖北省',0,'','',1368120198),(18,430000,0,0,0,'','湖南省',0,'','',1368120198),(19,440000,0,0,0,'','广东省',0,'','',1368120198),(20,450000,0,0,0,'','广西自治区',0,'','',1368120198),(21,460000,0,0,0,'','海南省',0,'','',1368120198),(22,500000,0,0,0,'','重庆市',0,'','',1368120198),(23,510000,0,0,0,'','四川省',0,'','',1368120198),(24,520000,0,0,0,'','贵州省',0,'','',1368120198),(25,530000,0,0,0,'','云南省',0,'','',1368120198),(26,540000,0,0,0,'','西藏自治区',0,'','',1368120198),(27,610000,0,0,0,'','陕西省',0,'','',1368120198),(28,620000,0,0,0,'','甘肃省',0,'','',1368120198),(29,630000,0,0,0,'','青海省',0,'','',1368120198),(30,640000,0,0,0,'','宁夏自治区',0,'','',1368120198),(31,650000,0,0,0,'','新疆自治区',0,'','',1368120198),(32,710000,0,0,0,'','台湾省',0,'','',1368120198),(33,810000,0,0,0,'','香港特区',0,'','',1368120198),(34,820000,0,0,0,'','澳门特区',0,'','',1368120198);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
