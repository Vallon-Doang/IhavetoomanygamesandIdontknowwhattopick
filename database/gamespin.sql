/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_user_id_foreign` (`user_id`),
  KEY `announcements_is_published_published_at_index` (`is_published`,`published_at`),
  CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES
(1,2,'Community feed is live','You can now post updates, attach an image, and edit your own posts. Administrators moderate anything that breaks the rules.',1,'2026-10-15 06:29:08','2026-10-15 06:29:07','2026-10-15 06:29:08'),
(2,2,'Steam syncing is optional','The demo library works without Steam credentials. Link a public Steam account when you want to sync your real owned games.',1,'2026-10-15 06:29:08','2026-10-15 06:29:07','2026-10-15 06:29:08');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('gamespin-cache-1b6453892473a467d07372d45eb05abc2031647a','i:1;',1792073212),
('gamespin-cache-1b6453892473a467d07372d45eb05abc2031647a:timer','i:1792073212;',1792073212),
('gamespin-cache-77de68daecd823babbb58edb1c8e14d7106e83bb','i:1;',1792073333),
('gamespin-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer','i:1792073333;',1792073333),
('gamespin-cache-ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4','i:2;',1792073704),
('gamespin-cache-ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4:timer','i:1792073704;',1792073704),
('gamespin-cache-c1dfd96eea8cc2b62785275bca38ac261256e278','i:1;',1792073833),
('gamespin-cache-c1dfd96eea8cc2b62785275bca38ac261256e278:timer','i:1792073833;',1792073833),
('gamespin-cache-indopaperc@gmail.com|127.0.0.1','i:1;',1792047178),
('gamespin-cache-indopaperc@gmail.com|127.0.0.1:timer','i:1792047178;',1792047178),
('gamespin-cache-member@gamespin.test|127.0.0.1','i:2;',1792047358),
('gamespin-cache-member@gamespin.test|127.0.0.1:timer','i:1792047358;',1792047358);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `games` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `steam_app_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `cover_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `games_steam_app_id_unique` (`steam_app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES
(1,620,'Portal 2','https://cdn.cloudflare.steamstatic.com/steam/apps/620/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(2,400,'Portal','https://cdn.cloudflare.steamstatic.com/steam/apps/400/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(3,220,'Half-Life 2','https://cdn.cloudflare.steamstatic.com/steam/apps/220/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(4,440,'Team Fortress 2','https://cdn.cloudflare.steamstatic.com/steam/apps/440/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(5,570,'Dota 2','https://cdn.cloudflare.steamstatic.com/steam/apps/570/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(6,730,'Counter-Strike 2','https://cdn.cloudflare.steamstatic.com/steam/apps/730/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(7,292030,'The Witcher 3: Wild Hunt','https://cdn.cloudflare.steamstatic.com/steam/apps/292030/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(8,271590,'Grand Theft Auto V','https://cdn.cloudflare.steamstatic.com/steam/apps/271590/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(9,105600,'Terraria','https://cdn.cloudflare.steamstatic.com/steam/apps/105600/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(10,413150,'Stardew Valley','https://cdn.cloudflare.steamstatic.com/steam/apps/413150/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(11,1091500,'Cyberpunk 2077','https://cdn.cloudflare.steamstatic.com/steam/apps/1091500/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(12,1145360,'Hades','https://cdn.cloudflare.steamstatic.com/steam/apps/1145360/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(13,548430,'Deep Rock Galactic','https://cdn.cloudflare.steamstatic.com/steam/apps/548430/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(14,322330,'Don\'t Starve Together','https://cdn.cloudflare.steamstatic.com/steam/apps/322330/header.jpg','2026-10-11 09:21:28','2026-10-11 09:21:28'),
(15,NULL,'GameSpin Demo Exclusive',NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(16,320,'Half-Life 2: Deathmatch','https://cdn.cloudflare.steamstatic.com/steam/apps/320/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(17,360,'Half-Life Deathmatch: Source','https://cdn.cloudflare.steamstatic.com/steam/apps/360/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(18,4000,'Garry\'s Mod','https://cdn.cloudflare.steamstatic.com/steam/apps/4000/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(19,11020,'TrackMania Nations Forever','https://cdn.cloudflare.steamstatic.com/steam/apps/11020/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(20,33230,'Assassin\'s Creed II','https://cdn.cloudflare.steamstatic.com/steam/apps/33230/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(21,49520,'Borderlands 2','https://cdn.cloudflare.steamstatic.com/steam/apps/49520/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(22,205100,'Dishonored','https://cdn.cloudflare.steamstatic.com/steam/apps/205100/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(23,208650,'Batman™: Arkham Knight','https://cdn.cloudflare.steamstatic.com/steam/apps/208650/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(24,243470,'Watch_Dogs','https://cdn.cloudflare.steamstatic.com/steam/apps/243470/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(25,244210,'Assetto Corsa','https://cdn.cloudflare.steamstatic.com/steam/apps/244210/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(26,286690,'Metro 2033 Redux','https://cdn.cloudflare.steamstatic.com/steam/apps/286690/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(27,291550,'Brawlhalla','https://cdn.cloudflare.steamstatic.com/steam/apps/291550/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(28,321270,'UNLOVED','https://cdn.cloudflare.steamstatic.com/steam/apps/321270/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(29,447040,'Watch_Dogs 2','https://cdn.cloudflare.steamstatic.com/steam/apps/447040/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(30,552500,'Warhammer: Vermintide 2','https://cdn.cloudflare.steamstatic.com/steam/apps/552500/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(31,582660,'Black Desert','https://cdn.cloudflare.steamstatic.com/steam/apps/582660/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(32,606150,'Moonlighter','https://cdn.cloudflare.steamstatic.com/steam/apps/606150/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(33,646910,'The Crew 2','https://cdn.cloudflare.steamstatic.com/steam/apps/646910/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(34,674750,'Yet Another Zombie Defense HD','https://cdn.cloudflare.steamstatic.com/steam/apps/674750/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(35,698780,'Doki Doki Literature Club','https://cdn.cloudflare.steamstatic.com/steam/apps/698780/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(36,866020,'Growtopia','https://cdn.cloudflare.steamstatic.com/steam/apps/866020/header.jpg','2026-10-11 09:37:31','2026-10-11 09:37:31'),
(37,871720,'Ultimate Custom Night','https://cdn.cloudflare.steamstatic.com/steam/apps/871720/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(38,1200580,'One Gun Guy','https://cdn.cloudflare.steamstatic.com/steam/apps/1200580/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(39,1201270,'A Space for the Unbound','https://cdn.cloudflare.steamstatic.com/steam/apps/1201270/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(40,1222680,'Need for Speed™ Heat ','https://cdn.cloudflare.steamstatic.com/steam/apps/1222680/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(41,1241100,'POLYGON','https://cdn.cloudflare.steamstatic.com/steam/apps/1241100/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(42,1302110,'Inertial Drift Sunset Prologue','https://cdn.cloudflare.steamstatic.com/steam/apps/1302110/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(43,1456200,'Initial Drift Online','https://cdn.cloudflare.steamstatic.com/steam/apps/1456200/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(44,1625450,'Muck','https://cdn.cloudflare.steamstatic.com/steam/apps/1625450/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(45,1659040,'HITMAN World of Assassination','https://cdn.cloudflare.steamstatic.com/steam/apps/1659040/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(46,1755300,'Smart Factory Tycoon','https://cdn.cloudflare.steamstatic.com/steam/apps/1755300/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(47,1815780,'Asphalt Legends ','https://cdn.cloudflare.steamstatic.com/steam/apps/1815780/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(48,1977530,'One-armed cook','https://cdn.cloudflare.steamstatic.com/steam/apps/1977530/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(49,2019300,'Dokimon','https://cdn.cloudflare.steamstatic.com/steam/apps/2019300/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(50,2021910,'FPS Chess','https://cdn.cloudflare.steamstatic.com/steam/apps/2021910/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(51,2225070,'Trackmania','https://cdn.cloudflare.steamstatic.com/steam/apps/2225070/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(52,2551020,'One-armed robber','https://cdn.cloudflare.steamstatic.com/steam/apps/2551020/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(53,2881650,'Content Warning','https://cdn.cloudflare.steamstatic.com/steam/apps/2881650/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32'),
(54,3983810,'The Spike Cross','https://cdn.cloudflare.steamstatic.com/steam/apps/3983810/header.jpg','2026-10-11 09:37:32','2026-10-11 09:37:32');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_01_01_000001_create_steam_accounts_table',1),
(5,'2026_01_01_000002_create_games_table',1),
(6,'2026_01_01_000003_create_user_games_table',1),
(7,'2026_01_01_000004_create_spin_history_table',1),
(8,'2026_01_01_000005_create_steam_sync_runs_table',1),
(9,'2026_01_02_000001_create_posts_table',2),
(10,'2026_01_02_000002_create_announcements_table',2),
(11,'2026_01_02_000003_create_steam_reset_requests_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `body` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `hidden_reason` varchar(255) DEFAULT NULL,
  `hidden_by` bigint(20) unsigned DEFAULT NULL,
  `hidden_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_foreign` (`user_id`),
  KEY `posts_hidden_by_foreign` (`hidden_by`),
  KEY `posts_created_at_index` (`created_at`),
  KEY `posts_is_hidden_index` (`is_hidden`),
  CONSTRAINT `posts_hidden_by_foreign` FOREIGN KEY (`hidden_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES
(1,1,'Finally cleared my backlog roulette and landed on Hades — the picker has good taste.',NULL,0,NULL,NULL,NULL,NULL,'2026-10-15 06:29:07','2026-10-15 06:29:07'),
(2,1,'Anyone else use GameSpin before deciding what to play on a Friday night? It has saved me from choice paralysis.',NULL,0,NULL,NULL,NULL,NULL,'2026-10-15 06:29:07','2026-10-15 06:29:07'),
(3,2,'Welcome to the GameSpin community feed! Share what you are playing and keep it friendly.',NULL,0,NULL,NULL,NULL,NULL,'2026-10-15 06:29:07','2026-10-15 06:29:07'),
(4,1,'This demo post is hidden to show how admin moderation works in the feed.',NULL,1,'Example moderation - hidden from the public feed.',2,'2026-10-15 06:29:07',NULL,'2026-10-15 06:29:07','2026-10-15 06:29:07');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('L9OV0VaQTAccdkk4Zzo0uoHb5dMMSlALs0m3uYtz',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:157.0) Gecko/20100101 Firefox/157.0','eyJfdG9rZW4iOiJSQXNXY1Jxc3Z1OTlsUlhtbFZmcmJHcjF1Wk9WQ1ZVemRIazZuNEdFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9zdGVhbVwvY29ubmVjdCIsInJvdXRlIjoic3RlYW0uY29ubmVjdCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImRiYjkwZTNmODcwMGY4M2M1ZjA4MzBiMGI4MDlhNzYzMzgzM2VjNWY4MDdjMWRmZjE0M2FhN2E5OTQ0OTdmM2EifQ==',1792047037),
('NDNBW6hRvrJUSTx1pzlV3S47JCfFZL6uPqaRSkoY',3,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:157.0) Gecko/20100101 Firefox/157.0','eyJfdG9rZW4iOiJBZGxGb29LMVNFTUUyVkwySFNFTGZFd1dWWFVYemY4RGZhdDdncnJ5IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL3BpY2tlciIsInJvdXRlIjoicGlja2VyLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjozLCJwYXNzd29yZF9oYXNoX3dlYiI6IjU5MjBhM2FiNWEyMzgxNWViMzMyNGI1ZjZmMWM5OWVhYTZjNTc4ZGRiZjA5NzVlN2NiZTM0ZjVmMTIyYjFmMDUifQ==',1792049735),
('NVoulijPome0Gk2tHJMPwiqkQiHYrfA9kvsW8dXy',6,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:157.0) Gecko/20100101 Firefox/157.0','eyJfdG9rZW4iOiJ0UW9POGs3MkJVRWZkNjhZdW5yaGREQ0lscmN1eGl3c2c5SlFxR2lWIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo2LCJwYXNzd29yZF9oYXNoX3dlYiI6IjIxZGJkZDY3MmIzOTM0MTc5MDA5ODIxNzc1ZjQ2MzgwOWM5MzRhMTFhOGYxMzY1MTMyOWYxOTFjYTZmYzc4MTYiLCJ1cmwiOnsiaW50ZW5kZWQiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvZGFzaGJvYXJkIn19',1792073774),
('vryxMsVOXbDxDmvlQxT8TokbfwP325fKP5Q3sqmh',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:157.0) Gecko/20100101 Firefox/157.0','eyJfdG9rZW4iOiJlU2ZHRmhUTE15T2E1ZzdWOVdCc3lIdkY3cUJydUprek5Sbzhzc3U2IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2Rhc2hib2FyZCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1792073379),
('yObXocMvvQAyVRdyDLMxwaeOPonHOxfiLV2BEFVO',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64; rv:157.0) Gecko/20100101 Firefox/157.0','eyJfdG9rZW4iOiJMMlBtQUp4TTI5RkxMNFlnd1kwQzJtRmVOaHZNR2Zsdlpob09jYUZYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1792073379);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `spin_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `game_id` bigint(20) unsigned DEFAULT NULL,
  `game_name` varchar(255) NOT NULL,
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `spin_history_game_id_foreign` (`game_id`),
  KEY `spin_history_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `spin_history_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE SET NULL,
  CONSTRAINT `spin_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `spin_history` WRITE;
/*!40000 ALTER TABLE `spin_history` DISABLE KEYS */;
INSERT INTO `spin_history` VALUES
(1,1,6,'Counter-Strike 2',NULL,'2026-10-11 09:21:28'),
(2,1,11,'Cyberpunk 2077',NULL,'2026-10-11 09:21:28'),
(3,1,13,'Deep Rock Galactic',NULL,'2026-10-11 09:21:28'),
(4,1,13,'Deep Rock Galactic',NULL,'2026-10-11 09:21:39'),
(6,1,1,'Portal 2','{\"search\":\"Portal\"}','2026-10-11 09:41:51'),
(11,1,1,'Portal 2','{\"search\":\"Portal\"}','2026-10-11 09:52:40');
/*!40000 ALTER TABLE `spin_history` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `steam_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `steam_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `steam_id` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `steam_accounts_user_id_unique` (`user_id`),
  UNIQUE KEY `steam_accounts_steam_id_unique` (`steam_id`),
  CONSTRAINT `steam_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `steam_accounts` WRITE;
/*!40000 ALTER TABLE `steam_accounts` DISABLE KEYS */;
INSERT INTO `steam_accounts` VALUES
(5,6,'76561199198388795','Vallon_Doang','https://avatars.steamstatic.com/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg','2026-10-15 07:16:13','2026-10-15 07:16:09','2026-10-15 07:16:13');
/*!40000 ALTER TABLE `steam_accounts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `steam_reset_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `steam_reset_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `steam_id` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `resolved_by` bigint(20) unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `steam_reset_requests_user_id_foreign` (`user_id`),
  KEY `steam_reset_requests_resolved_by_foreign` (`resolved_by`),
  KEY `steam_reset_requests_status_index` (`status`),
  CONSTRAINT `steam_reset_requests_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `steam_reset_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `steam_reset_requests` WRITE;
/*!40000 ALTER TABLE `steam_reset_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `steam_reset_requests` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `steam_sync_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `steam_sync_runs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `games_imported` int(10) unsigned NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `steam_sync_runs_user_id_foreign` (`user_id`),
  KEY `steam_sync_runs_status_index` (`status`),
  CONSTRAINT `steam_sync_runs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `steam_sync_runs` WRITE;
/*!40000 ALTER TABLE `steam_sync_runs` DISABLE KEYS */;
INSERT INTO `steam_sync_runs` VALUES
(7,6,'success',41,NULL,'2026-10-15 07:16:13','2026-10-15 07:16:14','2026-10-15 07:16:13','2026-10-15 07:16:14');
/*!40000 ALTER TABLE `steam_sync_runs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `user_games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_games` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `game_id` bigint(20) unsigned NOT NULL,
  `playtime_minutes` int(10) unsigned NOT NULL DEFAULT 0,
  `source` varchar(255) NOT NULL DEFAULT 'demo',
  `is_excluded` tinyint(1) NOT NULL DEFAULT 0,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_games_user_id_game_id_unique` (`user_id`,`game_id`),
  KEY `user_games_game_id_foreign` (`game_id`),
  CONSTRAINT `user_games_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_games_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user_games` WRITE;
/*!40000 ALTER TABLE `user_games` DISABLE KEYS */;
INSERT INTO `user_games` VALUES
(1,1,1,1380,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(2,1,2,610,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(3,1,3,1240,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(4,1,4,3420,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(5,1,5,9180,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(6,1,6,7640,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(7,1,7,5210,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(8,1,8,2890,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(9,1,9,4560,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(10,1,10,3870,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(11,1,11,1980,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(12,1,12,2450,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(13,1,13,1760,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(14,1,14,890,'demo',0,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(15,1,15,15,'demo',1,NULL,'2026-10-11 09:21:28','2026-10-11 09:21:28'),
(100,6,3,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(101,6,16,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(102,6,17,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(103,6,6,220,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(104,6,18,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(105,6,19,1415,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(106,6,20,289,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(107,6,21,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(108,6,22,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(109,6,23,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(110,6,24,667,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(111,6,25,706,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(112,6,26,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(113,6,27,1118,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(114,6,28,86,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(115,6,29,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(116,6,30,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(117,6,31,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(118,6,32,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(119,6,33,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(120,6,34,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(121,6,36,3904,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(122,6,37,46,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(123,6,11,76,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(124,6,38,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(125,6,39,264,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(126,6,40,774,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(127,6,41,237,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(128,6,42,31,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(129,6,43,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(130,6,44,136,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(131,6,45,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(132,6,46,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(133,6,47,78,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(134,6,48,74,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(135,6,49,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(136,6,50,116,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(137,6,51,943,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(138,6,52,52,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(139,6,53,0,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13'),
(140,6,54,125,'steam',0,'2026-10-15 07:16:13','2026-10-15 07:16:13','2026-10-15 07:16:13');
/*!40000 ALTER TABLE `user_games` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'member',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Demo Member','member@gamespin.test',NULL,'$2y$12$jsADLlEjmvXUB0pM1X66GeH6XRLkS.jDfOU1eSwWxyTYQpOkP.aPe','member','UMO3HxuXICGksy3sha3qYuh4tOrBBe2Bq21I1iUIMPZWaL95FGH0uV3IDmrj','2026-10-11 09:21:28','2026-10-15 06:29:08'),
(2,'Demo Admin','admin@gamespin.test',NULL,'$2y$12$Y0yqrtvH7jbDOP9Awg.hu.ELjmQ9B7lV0OHmWd1dBFMA5qGGjDWei','admin',NULL,'2026-10-11 09:21:28','2026-10-15 06:29:08'),
(6,'Vallon','vallondoang@gmail.com',NULL,'$2y$12$oF20e4xSGuHo7I0W5aFHoO7plbpwnEusJ1EJAa.Ipp0G5NbVmPo3C','member',NULL,'2026-10-15 07:16:01','2026-10-15 07:16:01');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

