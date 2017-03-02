
DROP TABLE IF EXISTS uploads;
CREATE TABLE IF NOT EXISTS uploads (
  id bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  good_id bigint(20) NOT NULL,
  transaction_id bigint(20) NOT NULL,
  quantity int(10) NOT NULL,
  amount decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table uploads
--
ALTER TABLE uploads
  ADD PRIMARY KEY (id),
  ADD KEY uploads_fk1 (good_id),
  ADD KEY transaction_id (transaction_id);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table uploads
--
ALTER TABLE uploads
  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table uploads
--
ALTER TABLE uploads
  ADD CONSTRAINT uploads_fk1 FOREIGN KEY (good_id) REFERENCES goods (id),
  ADD CONSTRAINT uploads_fk2 FOREIGN KEY (transaction_id) REFERENCES upload_transactions (id);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
