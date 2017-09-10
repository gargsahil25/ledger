-- MySQL dump 10.13  Distrib 5.6.19, for osx10.7 (i386)
--
-- Host: eu-cdbr-west-01.cleardb.com    Database: heroku_566ad7804a89ed1
-- ------------------------------------------------------
-- Server version	5.5.56-log

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'Cash','cash'),(2,'Home','home'),(3,'Factory','factory'),(18,'Capital','capital'),(74,'Sahil','client'),(84,'Factory Rent','client'),(94,'Interest','client'),(104,'Babu Lal','client'),(114,'JK Bansal','client'),(124,'Munim Ji','client'),(134,'Vinod Bansal','client'),(144,'Sardaar Ji','client'),(154,'Manoj Thekedaar','client'),(164,'Shubhash Hissar','client'),(174,'Munim Ji Bassai','client'),(184,'Pump Drum Circle','client'),(194,'Ahuja Enterprices Khayala','client');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_account` int(11) NOT NULL,
  `to_account` int(11) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `from_account_idx` (`from_account`,`to_account`),
  KEY `to_account` (`to_account`),
  CONSTRAINT `from_account` FOREIGN KEY (`from_account`) REFERENCES `accounts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `to_account` FOREIGN KEY (`to_account`) REFERENCES `accounts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=685 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (224,74,1,'factory start krne k liye',100000,'2016-04-22 23:30:48','2017-05-14 18:00:48'),(234,74,1,'sahil dusari baar',200000,'2016-05-09 23:31:20','2017-05-14 18:01:20'),(244,74,1,'sahil teesari baar',200000,'2016-05-12 23:32:06','2017-05-14 18:02:06'),(254,1,84,'Factory rent',8000,'2016-04-15 00:00:00','2017-05-14 18:06:41'),(264,1,84,'Factory rent',8000,'2016-03-04 23:38:46','2017-05-14 18:08:46'),(274,1,84,'Factory rent',5000,'2016-05-16 23:40:20','2017-05-14 18:10:20'),(284,1,94,'Munim ji ko',2000,'2016-03-22 23:42:06','2017-05-14 18:12:06'),(294,1,94,'Munim ji ko',2000,'2016-04-22 23:43:32','2017-05-14 18:13:32'),(304,1,104,'Payment 1',40000,'2016-05-09 23:44:57','2017-05-14 18:14:57'),(314,1,104,'Payment 2',40000,'2016-05-15 23:45:23','2017-05-14 18:15:23'),(324,1,114,'Payment 1',200000,'2016-05-12 23:46:40','2017-05-14 18:16:40'),(334,1,124,'500 Kit, 8 soot, 100 daya',40000,'2016-05-12 23:48:57','2017-05-14 18:18:57'),(344,1,2,'Gaadi ka kaam and oil',2000,'2016-04-28 23:49:54','2017-05-14 18:19:54'),(364,114,3,'Drum Circle 4750kg@ Rs32',152000,'2016-05-13 23:52:55','2017-05-14 18:22:55'),(384,1,3,'Gaadi kanda and rickshaw rent',200,'2016-05-18 23:59:23','2017-05-14 18:29:23'),(394,1,3,'Mobil, Oil Motor Cycle, Prasaad',410,'2016-05-20 00:01:26','2017-05-14 18:31:26'),(404,1,3,'Rippat, 7 and 6 soot, 26 kg 400 grams @ 76',2000,'2016-05-27 00:03:42','2017-05-14 18:33:42'),(414,1,3,'Mohan Rooter (1000 : 15/05, 5000 : 17/05, 10000 : 20/05, 4000 : 25/05)',20000,'2016-05-20 00:06:17','2017-05-14 18:36:17'),(434,134,3,'Pump Circle 630kg @ Rs 41.50,      Rs 65 Tulai',26200,'2016-05-24 00:20:37','2017-05-14 18:50:37'),(444,144,3,'Pump Circle 500kg @ Rs42 (Sardaar se)',21000,'2016-05-18 00:22:27','2017-05-14 18:52:27'),(454,1,144,'Cash ',21000,'2016-05-18 00:24:23','2017-05-14 18:54:23'),(464,144,3,'Pump Circle 1000kg@ Rs40',40000,'2016-05-28 00:25:40','2017-05-14 18:55:40'),(474,1,74,'Cash',20000,'2016-05-26 00:30:05','2017-05-14 19:00:05'),(484,1,144,'Cash',25000,'2016-05-28 00:00:00','2017-05-14 19:20:06'),(494,1,84,'3000 16/05 and 2000 advance 16/06',5000,'2016-05-26 00:54:05','2017-05-14 19:24:06'),(504,1,3,'Mohan Router ( Rs6000 26/05 and Rs5300 27/05 )',11300,'2016-05-26 00:56:44','2017-05-14 19:26:44'),(514,1,154,'Cash',10000,'2016-05-24 00:57:32','2017-05-14 19:27:32'),(524,1,3,'Dheeraj Die Fitter',2000,'2016-05-28 01:04:39','2017-05-14 19:34:39'),(534,164,1,'Cash ( 30000 : 25/05, 50000 : 30/05, 25000 : 30/05)',105000,'2016-05-30 01:07:15','2017-05-14 19:37:16'),(544,174,1,'Cash',20000,'2016-05-21 01:09:08','2017-05-14 19:39:08'),(554,174,1,'Cash ( Auto Rs 300, Interest : Rs 2000)',2300,'2016-05-29 01:10:38','2017-05-14 19:40:38'),(564,1,94,'Cash Munim ji ',2000,'2016-05-22 01:12:12','2017-05-14 19:42:12'),(574,1,134,'Cash ( 6000, 20200 : 31/05)',26200,'2016-05-31 01:14:04','2017-05-14 19:44:04'),(584,1,144,'Cash ',15000,'2016-05-31 01:15:11','2017-05-14 19:45:11'),(614,184,3,'Pump Circle 1695kg@ Rs29',50000,'2016-06-01 01:20:30','2017-05-14 19:50:30'),(624,1,184,'Cash ',50000,'2016-05-31 01:22:13','2017-05-14 19:52:13'),(634,1,154,'Cash',10000,'2016-05-30 01:23:06','2017-05-14 19:53:06'),(644,1,3,'Cash ( Auto : 300, Auto : 800, Gate Welding : 240)',1340,'2016-06-01 01:26:58','2017-05-14 19:56:58'),(654,3,134,'1100 Piece Strator Pump, 5 soot @ 6.40',35200,'2016-05-27 00:00:00','2017-05-14 19:59:20'),(664,3,174,'500 Kit Strator, 6 soot @ Rs10.50 , Rs16 per rooter',39500,'2016-05-25 01:31:59','2017-05-14 20:01:59'),(674,3,164,'Bocher no. 3',158545,'2016-05-29 01:34:25','2017-05-14 20:04:25'),(684,3,194,'500 pump Strator, 5 soot@6.40',16000,'2016-05-31 01:37:03','2017-05-14 20:07:03');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-11  1:00:56
