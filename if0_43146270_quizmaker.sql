-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql202.infinityfree.com
-- Generation Time: Oct 23, 2026 at 01:57 AM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43146270_quizmaker`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `answer_letter` char(1) NOT NULL,
  `answer_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_letter`, `answer_text`) VALUES
(125, 32, 'A', 'Satellite phone'),
(126, 32, 'B', 'Cordless phone'),
(127, 32, 'C', 'VoIP phone'),
(128, 32, 'D', 'Feature phone'),
(129, 33, 'A', 'Rotary phone'),
(130, 33, 'B', 'VoIP phone'),
(131, 33, 'C', 'Analog landline'),
(132, 33, 'D', 'Payphone'),
(133, 34, 'A', 'Smartphone'),
(134, 34, 'B', 'Phablet'),
(135, 34, 'C', 'Feature phone'),
(136, 34, 'D', 'Softphone'),
(137, 35, 'A', 'Touch-tone phone'),
(138, 35, 'B', 'Cordless phone'),
(139, 35, 'C', 'Push-button phone'),
(140, 35, 'D', 'Rotary phone'),
(141, 36, 'A', 'Phablet'),
(142, 36, 'B', 'Flip phone'),
(143, 36, 'C', 'Smartwatch'),
(144, 36, 'D', 'Walkie-talkie'),
(145, 37, 'A', 'tst'),
(146, 37, 'B', 'test'),
(147, 37, 'C', 'test'),
(148, 37, 'D', 'test'),
(149, 38, 'A', 'Dynamic testing'),
(150, 38, 'B', 'Static testing'),
(151, 38, 'C', 'System testing'),
(152, 38, 'D', 'Usability testing'),
(153, 39, 'A', 'Unit testing'),
(154, 39, 'B', 'Integration testing'),
(155, 39, 'C', 'Acceptance testing'),
(156, 39, 'D', 'Alpha testing'),
(157, 40, 'A', 'Patch'),
(158, 40, 'B', 'Bug'),
(159, 40, 'C', 'Feature'),
(160, 40, 'D', 'Release'),
(161, 41, 'A', 'White-box testing'),
(162, 41, 'B', 'Black-box testing'),
(163, 41, 'C', 'Performance testing'),
(164, 41, 'D', 'Load testing'),
(165, 42, 'A', 'Glass-box testing'),
(166, 42, 'B', 'Black-box testing'),
(167, 42, 'C', 'Clear-box testing'),
(168, 42, 'D', 'White-box testing'),
(169, 43, 'A', 'Corpus cavernosum'),
(170, 43, 'B', 'Corpus spongiosum'),
(171, 43, 'C', 'Tunica dartos'),
(172, 43, 'D', 'Glans penis'),
(173, 44, 'A', 'Adrenaline'),
(174, 44, 'B', 'Nitric oxide'),
(175, 44, 'C', 'Serotonin'),
(176, 44, 'D', 'Endothelin'),
(177, 45, 'A', 'Frenulum'),
(178, 45, 'B', 'Smegma'),
(179, 45, 'C', 'Prepuce'),
(180, 45, 'D', 'Perineum'),
(181, 46, 'A', 'Vas deferens'),
(182, 46, 'B', 'Ureter'),
(183, 46, 'C', 'Epididymis'),
(184, 46, 'D', 'Urethra'),
(185, 47, 'A', 'Phimosis'),
(186, 47, 'B', 'Priapism'),
(187, 47, 'C', 'Peyronie\'s disease'),
(188, 47, 'D', 'Balanitis'),
(189, 48, 'A', 'Zeus'),
(190, 48, 'B', 'Poseidon'),
(191, 48, 'C', 'Hades'),
(192, 48, 'D', 'Apollo'),
(193, 49, 'A', 'Thor'),
(194, 49, 'B', 'Loki'),
(195, 49, 'C', 'Odin'),
(196, 49, 'D', 'Baldr'),
(197, 50, 'A', 'Anubis'),
(198, 50, 'B', 'Ra'),
(199, 50, 'C', 'Osiris'),
(200, 50, 'D', 'Horus'),
(201, 51, 'A', 'Shiva'),
(202, 51, 'B', 'Vishnu'),
(203, 51, 'C', 'Brahma'),
(204, 51, 'D', 'Ganesha'),
(205, 52, 'A', 'Mercury'),
(206, 52, 'B', 'Mars'),
(207, 52, 'C', 'Jupiter'),
(208, 52, 'D', 'Vulcan'),
(209, 53, 'A', 'dss'),
(210, 53, 'B', 'dsfs'),
(211, 53, 'C', 'dsf'),
(212, 53, 'D', 'dsfds'),
(233, 59, 'A', 'Haddonfield'),
(234, 59, 'B', 'Springwood'),
(235, 59, 'C', 'Woodsboro'),
(236, 59, 'D', 'Crystal Lake'),
(237, 60, 'A', 'Botany'),
(238, 60, 'B', 'Natural philosophy and chemistry'),
(239, 60, 'C', 'Astronomy'),
(240, 60, 'D', 'Theology'),
(241, 61, 'A', 'The Shining'),
(242, 61, 'B', '\'Salem\'s Lot'),
(243, 61, 'C', 'Pet Sematary'),
(244, 61, 'D', 'It'),
(245, 62, 'A', 'The Overlook Hotel'),
(246, 62, 'B', 'The Bates Motel'),
(247, 62, 'C', 'Hill House'),
(248, 62, 'D', 'The Dolphin Hotel'),
(249, 63, 'A', 'Kurt Russell'),
(250, 63, 'B', 'Sam Neill'),
(251, 63, 'C', 'Jeff Goldblum'),
(252, 63, 'D', 'Bruce Campbell'),
(273, 69, 'A', 'Enceladus'),
(274, 69, 'B', 'Titan'),
(275, 69, 'C', 'Mimas'),
(276, 69, 'D', 'Iapetus'),
(277, 70, 'A', 'Solid iron and nickel'),
(278, 70, 'B', 'Dense silicate rock'),
(279, 70, 'C', 'Water ice with traces of rock and dust'),
(280, 70, 'D', 'Liquid methane droplets'),
(281, 71, 'A', 'Sink to the bottom'),
(282, 71, 'B', 'Float'),
(283, 71, 'C', 'Dissolve'),
(284, 71, 'D', 'Implode from water pressure'),
(285, 72, 'A', 'A hexagonal storm pattern'),
(286, 72, 'B', 'The Great Dark Spot'),
(287, 72, 'C', 'A square-shaped aurora'),
(288, 72, 'D', 'A triple-ring vortex'),
(289, 73, 'A', 'Voyager 2'),
(290, 73, 'B', 'Galileo'),
(291, 73, 'C', 'Cassini-Huygens'),
(292, 73, 'D', 'New Horizons'),
(293, 74, 'A', 'The Westermarck effect'),
(294, 74, 'B', 'The Oedipus complex'),
(295, 74, 'C', 'The Coolidge effect'),
(296, 74, 'D', 'The Halo effect'),
(297, 75, 'A', 'It causes immediate structural mutations in mitochondrial DNA'),
(298, 75, 'B', 'It increases the probability that deleterious recessive alleles will be inherited from both parents'),
(299, 75, 'C', 'It reduces the total number of chromosomes present in the zygote'),
(300, 75, 'D', 'It halts the natural process of crossing over during meiosis'),
(301, 76, 'A', 'Marcel Mauss'),
(302, 76, 'B', 'Émile Durkheim'),
(303, 76, 'C', 'Claude Lévi-Strauss'),
(304, 76, 'D', 'Franz Boas'),
(305, 77, 'A', 'The Romanovs'),
(306, 77, 'B', 'The Habsburgs'),
(307, 77, 'C', 'The Plantagenets'),
(308, 77, 'D', 'The Tudors'),
(309, 78, 'A', 'To emulate deities such as Osiris and Isis and concentrate sacred royal power'),
(310, 78, 'B', 'To fulfill strict legal requirements that applied to all societal classes'),
(311, 78, 'C', 'To eliminate economic competition between rival merchant guilds'),
(312, 78, 'D', 'To prevent foreign kingdoms from learning military strategies'),
(353, 89, 'A', 'Manhua'),
(354, 89, 'B', 'Pornhwa'),
(355, 89, 'C', 'Smutoon'),
(356, 89, 'D', 'Hentai'),
(357, 90, 'A', 'Dae-ho'),
(358, 90, 'B', 'Hyun-soo'),
(359, 90, 'C', 'Jun-woo'),
(360, 90, 'D', 'Min-seok'),
(361, 91, 'A', 'LINE Webtoon'),
(362, 91, 'B', 'Tapas'),
(363, 91, 'C', 'Lezhin Comics'),
(364, 91, 'D', 'KakaoPage'),
(365, 92, 'A', 'Non-Traditional Romance'),
(366, 92, 'B', 'Next-Tier Route'),
(367, 92, 'C', 'Near-Total Rejection'),
(368, 92, 'D', 'Netorare'),
(369, 93, 'A', 'Entire panels are replaced with black screens'),
(370, 93, 'B', 'White bars (often nicknamed \'lightsabers\') or mosaic blurs'),
(371, 93, 'C', 'Characters\' faces are blacked out instead'),
(372, 93, 'D', 'Explicit actions are substituted with chibi animals');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `correct_answer` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `correct_answer`) VALUES
(32, 9, 'What type of phone connects directly to orbiting satellites rather than terrestrial cell towers?', 'A'),
(33, 9, 'Which type of telephone transmits voice calls as digital data packets over the internet?', 'B'),
(34, 9, 'What is the term for a mobile phone that offers basic voice calling and texting, typically with physical keys and limited computing capabilities?', 'C'),
(35, 9, 'Which vintage telephone technology used a finger wheel to send a series of electrical pulses representing the dialed number?', 'D'),
(36, 9, 'What term is used to describe a mobile device that bridges the gap between a smartphone and a tablet, typically featuring a large screen?', 'A'),
(37, 10, 'test', 'B'),
(38, 11, 'What type of software testing evaluates code and documentation without actively executing the program?', 'B'),
(39, 11, 'Which level of testing focuses on verifying communication and data transfer between combined software components?', 'B'),
(40, 11, 'In software engineering, what is the common term for an error or flaw in a computer program that produces an incorrect or unexpected result?', 'B'),
(41, 11, 'Boundary value analysis is a technique primarily used in which category of software testing?', 'B'),
(42, 11, 'Which testing methodology involves evaluating a system\'s functionality without having any access or knowledge of its internal code structure?', 'B'),
(43, 12, 'Which erectile tissue structure fills with blood to produce the majority of penile rigidity during an erection?', 'A'),
(44, 12, 'Which signaling molecule is primarily responsible for relaxing vascular smooth muscle in the penis to facilitate blood flow during sexual arousal?', 'B'),
(45, 12, 'What is the anatomical term for the retractable fold of skin that covers the head of the penis in uncircumcised individuals?', 'C'),
(46, 12, 'Through which anatomical duct does both semen and urine pass to exit the human male body?', 'D'),
(47, 12, 'What is the medical term for an involuntary, prolonged, and often painful erection lasting more than four hours without sexual stimulation?', 'B'),
(48, 13, 'In Greek mythology, who is the god of the sea, earthquakes, and horses?', 'B'),
(49, 13, 'Which Norse god sacrificed one of his eyes at Mimir\'s well in exchange for cosmic wisdom?', 'C'),
(50, 13, 'In ancient Egyptian mythology, which jackal-headed god presided over mummification and guided souls in the underworld?', 'A'),
(51, 13, 'In Hindu tradition, which deity is widely revered as the remover of obstacles and is depicted with the head of an elephant?', 'D'),
(52, 13, 'Which Roman deity is the god of war and the equivalent of the Greek god Ares?', 'B'),
(53, 14, 'sdfdsf', 'C'),
(59, 16, 'In John Carpenter\'s 1978 film \"Halloween\", what is the name of the fictional Illinois town terrorized by Michael Myers?', 'A'),
(60, 16, 'In Mary Shelley\'s 1818 novel \"Frankenstein\", what is Victor Frankenstein\'s primary field of study before creating the creature?', 'B'),
(61, 16, 'Which Stephen King novel features a malevolent, shapeshifting entity that frequently takes the form of Pennywise the Dancing Clown?', 'D'),
(62, 16, 'What is the name of the isolated Colorado hotel featured in the 1980 psychological horror film \"The Shining\"?', 'A'),
(63, 16, 'Directed by David Cronenberg, the 1986 body horror film \"The Fly\" stars which actor as scientist Seth Brundle?', 'C'),
(69, 15, 'What is Saturn\'s largest moon?', 'B'),
(70, 15, 'What are Saturn\'s prominent rings primarily composed of?', 'C'),
(71, 15, 'Due to its low average density compared to other planets, what would Saturn theoretically do in a large enough body of water?', 'B'),
(72, 15, 'What unique, persistent atmospheric cloud feature exists at Saturn\'s north pole?', 'A'),
(73, 15, 'Which NASA-ESA robotic spacecraft orbited Saturn from 2004 to 2017 to extensively study the planet and its moons?', 'C'),
(74, 19, 'What psychological hypothesis suggests that people who live in close domestic proximity during the first few years of their lives become desensitized to sexual attraction toward one another?', 'A'),
(75, 19, 'From a biological standpoint, why does reproduction between close genetic relatives significantly increase the risk of congenital disorders in offspring?', 'B'),
(76, 19, 'Which French anthropologist argued in \'The Elementary Structures of Kinship\' (1949) that the incest taboo is the universal foundation of human society because it forces exogamy and social alliances?', 'C'),
(77, 19, 'Which European royal dynasty is historically notable for extensive consanguineous marriages that contributed to severe health issues and a characteristic mandibular prognathism?', 'B'),
(78, 19, 'In ancient Egypt, sibling marriage was occasionally practiced among rulers primarily for which of the following reasons?', 'A'),
(89, 20, 'What is the widely used colloquial term used by international communities to refer to adult or erotic Korean manhwa?', 'B'),
(90, 20, 'In the widely read adult manhwa \'Secret Class\', what is the name of the male protagonist?', 'A'),
(91, 20, 'Which South Korean digital publishing platform is well-known for officially licensing and distributing mature/R18 webtoons internationally?', 'C'),
(92, 20, 'In the context of adult webtoons and manga, what does the common trope abbreviation \'NTR\' stand for?', 'D'),
(93, 20, 'Due to South Korean digital media regulations, how is explicit genital content typically censored in domestic releases of adult manhwa?', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `share_token` varchar(64) DEFAULT NULL,
  `time_limit_seconds` int(10) UNSIGNED NOT NULL DEFAULT 120,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `share_token`, `time_limit_seconds`, `created_at`, `owner_id`) VALUES
(9, 'types of phone Quiz', '', '8d702add60c02b831a36aff855dd5a0787700f7f3a5d44c8', 120, '2026-10-17 08:00:23', 3),
(10, 'test', '', '0fe89e1fd270e18bd9128eef7483a385ce353d3e75868696', 120, '2026-10-17 08:01:51', 3),
(11, 'generate m test quest with only test option the anwer is b Quiz', '', NULL, 120, '2026-10-17 08:05:33', 3),
(12, 'Practice', 'Test quest', NULL, 120, '2026-10-18 08:41:17', 3),
(13, 'gods Quiz', '', NULL, 120, '2026-10-18 13:19:34', 3),
(14, 'gods Quiz', 'fsd', 'b7bb08a6c0f39c38472ee25adaa7646a6269fe8e6cf3d30e', 120, '2026-10-18 14:42:23', 9),
(15, 'saturn Quiz', '', NULL, 300, '2026-10-18 14:43:19', 9),
(16, 'Horror Quiz', '', NULL, 120, '2026-10-18 14:43:25', 9),
(19, 'Incest', '', NULL, 120, '2026-10-19 22:00:23', 9),
(20, 'Porn manwha Quiz', '', '7ae0d85902d3207163745a268205a861f5221ec997e005dd', 120, '2026-10-21 02:24:19', 9);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `participant_name` varchar(190) NOT NULL,
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_questions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `participant_name`, `score`, `total_questions`, `percentage`, `submitted_at`) VALUES
(7, 10, 'dfsf', 1, 1, '100.00', '2026-10-17 08:02:36'),
(8, 9, 'sasa', 2, 5, '40.00', '2026-10-17 08:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token_hash` char(64) DEFAULT NULL,
  `reset_token_hash` char(64) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `reset_otp_hash` varchar(255) DEFAULT NULL,
  `reset_otp_expires_at` datetime DEFAULT NULL,
  `reset_otp_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `is_verified`, `verification_token_hash`, `reset_token_hash`, `reset_expires_at`, `reset_otp_hash`, `reset_otp_expires_at`, `reset_otp_attempts`, `created_at`) VALUES
(6, 'edwintolcidas8@gmail.com', '$2y$12$5M68J0o8O8w4n1CkOp0v/ectBcMol2LRdrh0in9jcvkQd2.ptosqC', 1, NULL, NULL, NULL, NULL, NULL, 0, '2026-10-15 22:36:28'),
(9, 'powerfirepowerfire@gmail.com', '$2y$12$zJEz5EzcAijoKh1IbblO4ObY8jJOA993fDq7yjQoAQ3etwOe9P/2a', 1, NULL, NULL, NULL, NULL, NULL, 0, '2026-10-18 13:55:53'),
(12, 'magayonjay32@gmail.com', '$2y$12$/P9XQ6/Fi3IuFb3gnjkm0u2aBVCPEZqlm3hHhB9ti.F/fDzk9fP9K', 1, NULL, NULL, NULL, NULL, NULL, 0, '2026-10-19 00:47:10'),
(15, 'reyndale108@gmail.com', '$2y$12$oEAFR/Xxg2gA97xu2.xzZ.Mee/QfacbpEhbmVshQUQpo2j2yjJ8l2', 0, 'e573a8ba21e9474b05984fd34dc80582134a43b994a6da4c6ec070fcb48819f3', NULL, NULL, NULL, NULL, 0, '2026-10-19 02:51:48'),
(24, 'mariasantos@gmail.com', '$2y$12$YDR8FY2N8sLbNa59Sfp1/eJSDjWQkLd9gvcw.bKXTrfzitFQMK4qe', 0, 'dc72374f92c34caaf30b2595ef1a424f56b7bce66f4fb7768c3db723257390e6', NULL, NULL, NULL, NULL, 0, '2026-10-20 12:51:10'),
(27, 'deosagga88@gamail.com', '$2y$12$iWeFLYmqmoTWXFvBjBTx7Ojbo1eo1fpaIMeFwjkM7wAqZRS6jOyoe', 0, '805793133be3c576bb33402642790daf0b35f38c52b2fe684bb9fb67fb687294', NULL, NULL, NULL, NULL, 0, '2026-10-20 12:54:50'),
(37, 'reyndale345@gmail.com', '$2y$12$Mb8HRBTP2dZVLZcMRMY9AOoQxjDCFavehzLQwArhpCCMZHZaSDhxG', 0, '885000fc73022e711d425dc488cd4776879a44aa2c6eb2c514903f90d2a51586', NULL, NULL, NULL, NULL, 0, '2026-10-22 04:41:46'),
(38, 'riveroreyndale@gmail.com', '$2y$12$bnEHddcZcYsANmLwUGxfPu944RvR/XPheMXO0t1FBrt1wPH/3cTMa', 1, NULL, NULL, NULL, NULL, NULL, 0, '2026-10-22 04:43:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_answers_question_letter` (`question_id`,`answer_letter`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_questions_quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_quizzes_share_token` (`share_token`),
  ADD KEY `idx_quizzes_created_at` (`created_at`),
  ADD KEY `idx_quizzes_owner_created` (`owner_id`,`created_at`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attempts_quiz_submitted` (`quiz_id`,`submitted_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=373;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_answers_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `fk_attempts_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
