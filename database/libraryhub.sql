-- MySQL dump 10.13  Distrib 8.0.40, for Linux (x86_64)
--
-- Host: localhost    Database: libraryhub
-- ------------------------------------------------------
-- Server version	8.0.40-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `author` (
  `authorid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('Enable','Disable') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`authorid`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author`
--

LOCK TABLES `author` WRITE;
/*!40000 ALTER TABLE `author` DISABLE KEYS */;
INSERT INTO `author` VALUES (2,'Rebecca Skloot','Enable'),(3,'Richard Dawkins','Enable'),(5,'Stephen Hawking','Enable'),(9,'Steven Strogatz','Enable'),(10,'Anchor Books','Enable'),(11,'William Strunk Jr.','Enable'),(12,' Stephen King','Enable'),(13,'Howard Zinn','Enable'),(14,'Susan Wise Bauer','Enable'),(15,'Princeton University Press','Enable'),(16,'Penguin Classics','Enable'),(17,'Harper Perennial Modern Classics','Enable'),(18,'W.W. Norton &amp; Company','Enable');
/*!40000 ALTER TABLE `author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book` (
  `bookid` int NOT NULL AUTO_INCREMENT,
  `categoryid` int NOT NULL,
  `authorid` int NOT NULL,
  `rackid` int NOT NULL,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `picture` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `publisherid` int NOT NULL,
  `isbn` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `no_of_copy` int NOT NULL,
  `status` enum('Enable','Disable') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `added_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bookid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
INSERT INTO `book` VALUES (1,4,11,2,'How to Solve It','joy-php.jpg',3,'978-0691119660',10,'Enable','2022-06-12 11:12:48','2022-06-12 11:13:27'),(2,2,3,2,'Head First PHP &amp; MySQL','header-first-php.jpg',9,'0596006306',10,'Enable','2022-06-12 11:16:01','2022-06-12 11:16:01'),(3,4,9,1,'The Joy of x: A Guided Tour of Math, from One to Infinity','',6,'978-0547517655',23,'Enable','2022-06-12 13:29:14','2022-06-12 13:29:14'),(5,6,2,1,'The Immortal Life of Henrietta Lacks',NULL,7,'978-1400052189',1,'Enable','2025-01-16 21:11:45','2025-01-16 21:11:45'),(6,6,3,1,'The Selfish Gene',NULL,8,'978-0199291151',5,'Enable','2025-01-16 21:31:26','2025-01-16 21:31:26'),(7,6,5,2,'A Brief History of Time',NULL,9,'978-0553380163',4,'Enable','2025-01-16 21:31:48','2025-01-16 21:31:48'),(10,3,11,1,'The Elements of Style',NULL,3,'ISBN: ',4,'Enable','2025-01-18 09:50:23','2025-01-18 09:50:23'),(11,2,13,1,'A People&#039;s History of the United States',NULL,6,'978-0062397348',7,'Enable','2025-01-18 09:51:02','2025-01-18 09:51:02');
/*!40000 ALTER TABLE `book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `categoryid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('Enable','Disable') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (2,'History','Enable'),(3,'English','Enable'),(4,'Mathematics','Enable'),(6,'Science','Enable');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issued_book`
--

DROP TABLE IF EXISTS `issued_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `issued_book` (
  `issuebookid` int NOT NULL AUTO_INCREMENT,
  `bookid` int NOT NULL,
  `userid` int NOT NULL,
  `issue_date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expected_return_date` datetime DEFAULT NULL,
  `return_date_time` datetime DEFAULT NULL,
  `status` enum('Issued','Returned','Not Return') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`issuebookid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issued_book`
--

LOCK TABLES `issued_book` WRITE;
/*!40000 ALTER TABLE `issued_book` DISABLE KEYS */;
INSERT INTO `issued_book` VALUES (1,2,2,'2022-06-12 15:33:45','2022-06-15 16:27:59','2022-06-16 16:27:59','Issued'),(3,3,3,'2022-06-12 18:46:07','2022-06-30 18:46:02','2022-06-12 18:46:14','Returned'),(4,1,2,'2025-01-17 21:41:34','2025-01-17 21:41:29','2025-01-31 22:55:56','Returned');
/*!40000 ALTER TABLE `issued_book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publisher`
--

DROP TABLE IF EXISTS `publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publisher` (
  `publisherid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` enum('Enable','Disable') NOT NULL,
  PRIMARY KEY (`publisherid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publisher`
--

LOCK TABLES `publisher` WRITE;
/*!40000 ALTER TABLE `publisher` DISABLE KEYS */;
INSERT INTO `publisher` VALUES (2,'Amazon publishing','Enable'),(3,'Penguin books ltd.','Enable'),(4,'Vintage Publishing','Enable'),(5,'Macmillan Publishers','Enable'),(6,'Houghton Mifflin Harcourt','Enable'),(7,'Broadway Books','Enable'),(8,'Oxford University Press','Enable'),(9,'Bantam Books','Enable');
/*!40000 ALTER TABLE `publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rack`
--

DROP TABLE IF EXISTS `rack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rack` (
  `rackid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('Enable','Disable') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'Enable',
  PRIMARY KEY (`rackid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rack`
--

LOCK TABLES `rack` WRITE;
/*!40000 ALTER TABLE `rack` DISABLE KEYS */;
INSERT INTO `rack` VALUES (1,'R1','Enable'),(2,'R2','Enable'),(6,'test','Enable');
/*!40000 ALTER TABLE `rack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `role` enum('admin','user') DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (2,'William','Brown','william.brown@libraryhub.com','fd820a2b4461bddd116c1518bc4b0f77','user'),(3,'Emily','Davis','emily.davis@libraryhub.com','b02ae5aaefe3f7090668df034b0f2324','user'),(4,'John','Doe','john.doe@libraryhub.com','527bd5b5d689e2c32ae974c6229ff785','admin'),(5,'Jane','Smith','jane.smith@libraryhub.com','5844a15e76563fedd11840fd6f40ea7b','admin');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-18 10:42:44
