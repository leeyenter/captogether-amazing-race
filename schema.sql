
--
-- Table structure for table `foc_affiliation_cards`
--

CREATE TABLE `foc_affiliation_cards` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `affiliation` text COLLATE utf8_bin NOT NULL,
  `hash` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `foc_affiliation_cards`
--

INSERT INTO `foc_affiliation_cards` (`id`, `affiliation`, `hash`) VALUES
(1, 'Miner', 'fd26b78'),
(2, 'Miner', '7627bec'),
(3, 'Miner', '6148b14'),
(4, 'Miner', 'ff4217f'),
(5, 'Miner', 'd81b10d'),
(6, 'Miner', '5d6972a'),
(7, 'Miner', '251709f'),
(8, 'Miner', '0017aa6'),
(9, 'Miner', '63226a4'),
(10, 'Miner', 'cb3397c'),
(11, 'Miner', '089aca5'),
(12, 'Miner', '8ef80e1'),
(13, 'Saboteur', '777f2ad'),
(14, 'Saboteur', '264ba84'),
(15, 'Saboteur', '739ac5c'),
(16, 'Saboteur', 'aedcdfd'),
(17, 'Saboteur', '1a67fa4'),
(18, 'Saboteur', '8397fc9');

-- --------------------------------------------------------

--
-- Table structure for table `foc_cards_on_board`
--

CREATE TABLE `foc_cards_on_board` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `board` tinyint(1) UNSIGNED NOT NULL,
  `row` tinyint(3) UNSIGNED NOT NULL,
  `col` tinyint(3) UNSIGNED NOT NULL,
  `card_type_id` tinyint(3) UNSIGNED NOT NULL,
  `rotated` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `foc_cards_on_hand`
--

CREATE TABLE `foc_cards_on_hand` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `card_type_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `foc_card_types`
--

CREATE TABLE `foc_card_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 for path, 2 for map, 3 for block, 4 for rockfall',
  `count` tinyint(3) UNSIGNED NOT NULL,
  `connect_top` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `connect_right` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `connect_bottom` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `connect_left` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `path` tinyint(1) UNSIGNED NOT NULL,
  `filename` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `foc_card_types`
--

INSERT INTO `foc_card_types` (`id`, `type`, `count`, `connect_top`, `connect_right`, `connect_bottom`, `connect_left`, `path`, `filename`) VALUES
(0, 0, 0, 0, 0, 0, 0, 0, 'normal_backing.jpg'),
(1, 2, 6, 0, 0, 0, 0, 0, 'map.jpg'),
(2, 3, 4, 0, 0, 0, 0, 0, 'block.jpg'),
(3, 4, 5, 0, 0, 0, 0, 0, 'rockfall.jpg'),
(4, 1, 8, 1, 0, 1, 1, 1, 'path02.jpg'),
(5, 1, 8, 1, 1, 1, 1, 1, 'path03.jpg'),
(6, 1, 7, 1, 0, 1, 0, 1, 'path04.jpg'),
(7, 1, 8, 0, 1, 1, 0, 1, 'path05.jpg'),
(8, 1, 9, 0, 0, 1, 1, 1, 'path06.jpg'),
(9, 1, 7, 0, 1, 0, 1, 1, 'path07.jpg'),
(10, 1, 2, 1, 1, 1, 0, 0, 'path08.jpg'),
(11, 1, 2, 1, 0, 0, 0, 0, 'path09.jpg'),
(12, 1, 2, 1, 1, 1, 1, 0, 'path10.jpg'),
(13, 1, 2, 0, 1, 1, 0, 0, 'path11.jpg'),
(14, 1, 2, 0, 0, 1, 1, 0, 'path12.jpg'),
(15, 1, 2, 0, 1, 0, 0, 0, 'path13.jpg'),
(16, 1, 2, 1, 0, 1, 0, 0, 'path14.jpg'),
(17, 1, 2, 1, 1, 0, 1, 0, 'path15.jpg'),
(18, 1, 2, 0, 1, 0, 1, 0, 'path16.jpg'),
(19, 1, 8, 0, 1, 1, 1, 1, 'path01.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `foc_groups`
--

CREATE TABLE `foc_groups` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` text COLLATE utf8_bin NOT NULL,
  `board` tinyint(1) UNSIGNED NOT NULL,
  `affiliation` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `hash` text COLLATE utf8_bin,
  `blocked` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `last_stn` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `foc_groups`
--

INSERT INTO `foc_groups` (`id`, `name`, `board`, `affiliation`, `hash`, `blocked`, `last_stn`) VALUES
(1, 'Bifur', 1, 1, 'e52cf2', 0, 1),
(2, 'Bombur', 1, 2, 'b299e3', 0, 1),
(3, 'Borin', 1, 1, 'b23a44', 0, 0),
(4, 'Dori', 1, 1, '2ab276', 0, 1),
(5, 'Durin', 1, 2, '806dff', 0, 1),
(6, 'Fíli', 1, 1, '44fe46', 0, 1),
(7, 'Narvi', 1, 1, '68abf2', 1, 1),
(8, 'Telchar', 1, 1, '4bad98', 1, 1),
(9, 'Balin', 2, 2, 'c13abf', 0, 1),
(10, 'Bofur', 2, 1, '7fb4b9', 0, 0),
(11, 'Dwalin ', 2, 2, '54f146', 0, 1),
(12, 'Flói', 2, 1, '76074b', 0, 1),
(13, 'Gimli', 2, 1, '0ffeb0', 0, 0),
(14, 'Kíli', 2, 1, 'ead1a3', 0, 0),
(15, 'Nori', 2, 1, '0150f6', 0, 1),
(16, 'Thorin', 2, 2, 'e40a75', 0, 0),
(18, 'Admin (Board 1)', 1, 1, 'hash1', 0, 0),
(21, 'Admin (Board 2)', 2, 1, 'hash2', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `foc_log_cards_given`
--

CREATE TABLE `foc_log_cards_given` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stn_id` tinyint(3) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `card_type_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `foc_log_cards_placed`
--

CREATE TABLE `foc_log_cards_placed` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL,
  `card_type_id` tinyint(3) UNSIGNED NOT NULL,
  `row` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `col` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `rotated` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `foc_log_groups_closed`
--

CREATE TABLE `foc_log_groups_closed` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stn_id` tinyint(3) UNSIGNED NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `foc_stations`
--

CREATE TABLE `foc_stations` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` text COLLATE utf8_bin NOT NULL,
  `hash` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `foc_stations`
--

INSERT INTO `foc_stations` (`id`, `name`, `hash`) VALUES
(1, 'h(EAR)', 'a803a9'),
(2, 'Relay (Zone 1)', '7f95c6'),
(3, 'Crossing the Danger Zone', '6ea002'),
(4, 'Riddlers', '89a42b'),
(5, 'Own or Get Owned', '665800'),
(6, 'Counting Stars', '05cffd'),
(7, 'Puppet Show', '73c127'),
(8, 'Dance Off', '8fa9ff'),
(9, 'Creation Time!', '0c403b'),
(10, 'Can you Math', '0116e9'),
(11, 'Knowledgeable', '465312'),
(12, 'Relay (Zone 2)', '84d644'),
(13, 'Crackkk', '13819b'),
(14, 'Blind Shapes', 'c8ce69'),
(15, 'Treasure Hunt', '46f5d5'),
(16, 'Dance Off', 'c2da80'),
(17, 'Admin', 'adminhash');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `foc_affiliation_cards`
--
ALTER TABLE `foc_affiliation_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foc_cards_on_board`
--
ALTER TABLE `foc_cards_on_board`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_type_id` (`card_type_id`);

--
-- Indexes for table `foc_cards_on_hand`
--
ALTER TABLE `foc_cards_on_hand`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `foc_card_types`
--
ALTER TABLE `foc_card_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foc_groups`
--
ALTER TABLE `foc_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foc_log_cards_given`
--
ALTER TABLE `foc_log_cards_given`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foc_log_cards_placed`
--
ALTER TABLE `foc_log_cards_placed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foc_log_groups_closed`
--
ALTER TABLE `foc_log_groups_closed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foc_stations`
--
ALTER TABLE `foc_stations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `foc_affiliation_cards`
--
ALTER TABLE `foc_affiliation_cards`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `foc_cards_on_board`
--
ALTER TABLE `foc_cards_on_board`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `foc_cards_on_hand`
--
ALTER TABLE `foc_cards_on_hand`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `foc_card_types`
--
ALTER TABLE `foc_card_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `foc_groups`
--
ALTER TABLE `foc_groups`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `foc_log_cards_given`
--
ALTER TABLE `foc_log_cards_given`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `foc_log_cards_placed`
--
ALTER TABLE `foc_log_cards_placed`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `foc_log_groups_closed`
--
ALTER TABLE `foc_log_groups_closed`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `foc_stations`
--
ALTER TABLE `foc_stations`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
