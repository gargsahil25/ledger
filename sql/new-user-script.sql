--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (6,'Sahil','2525');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;


--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` (user_id, name, type, balance) VALUES 
    (6,'CASH','cash',0),
    (6,'HOME_EXPENSE','home',0),
    (6,'FACTORY_EXPENSE','factory_expense',0),
    (6,'FACTORY_MALL','factory_mall',0),
    (6,'CAPITAL','capital',0),
    (6,'FACTORY_PROPERTY','factory',0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;