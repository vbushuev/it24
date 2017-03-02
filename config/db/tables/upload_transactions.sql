

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `a0124380_it24`
--

-- --------------------------------------------------------

--
-- Table structure for table `upload_transactions`
--

DROP TABLE IF EXISTS `upload_transactions`;
CREATE TABLE IF NOT EXISTS `upload_transactions` (
  `id` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_end` timestamp NULL DEFAULT NULL,
  `schedule_id` bigint(20) NOT NULL,
  `supply_id` bigint(20) NOT NULL,
  `status_id` bigint(20) NOT NULL,
  `error_id` bigint(20) NOT NULL,
  `message` text,
  `total` int(6) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `upload_transactions`
--
ALTER TABLE `upload_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `supply_id` (`supply_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `error_id` (`error_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `upload_transactions`
--
ALTER TABLE `upload_transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `upload_transactions`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
