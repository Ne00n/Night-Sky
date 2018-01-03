SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `checks` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `ENABLED` int(1) NOT NULL DEFAULT 1,
  `SLOT` int(1) NOT NULL,
  `ONLINE` int(1) NOT NULL DEFAULT 0,
  `NAME` varchar(50) NOT NULL,
  `IP` varchar(50) NOT NULL,
  `PORT` int(11) NOT NULL,
  `Check_Interval` int(2) NOT NULL DEFAULT 10,
  `Lastrun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `emails` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `EMail` varchar(50) NOT NULL,
  `Status` int(1) NOT NULL DEFAULT 0,
  `activation_hash` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `groups` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `groups_checks` (
  `ID` int(11) NOT NULL,
  `CheckID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `groups_emails` (
  `ID` int(11) NOT NULL,
  `EmailID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `history` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `CHECK_ID` int(11) NOT NULL,
  `Status` int(1) NOT NULL,
  `Timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `login_blacklist` (
  `id` int(11) NOT NULL,
  `ip_remote` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `timestamp_expires` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `register_blacklist` (
  `id` int(11) NOT NULL,
  `ip_remote` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `timestamp_expires` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `remote` (
  `ID` int(11) NOT NULL,
  `Location` varchar(50) NOT NULL,
  `IP` varchar(50) NOT NULL,
  `Port` int(5) NOT NULL,
  `Online` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `serversCPU` (
  `ID` int(11) NOT NULL,
  `serversTokenID` int(11) NOT NULL,
  `core` int(11) NOT NULL,
  `user` double NOT NULL,
  `nice` double NOT NULL,
  `system` double NOT NULL,
  `idle` double NOT NULL,
  `iowait` double NOT NULL,
  `irq` double NOT NULL,
  `softirq` double NOT NULL,
  `steal` double NOT NULL,
  `guest` double NOT NULL,
  `guest_nice` double NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `serversDisk` (
  `ID` int(11) NOT NULL,
  `serversTokenID` int(11) NOT NULL,
  `mount` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `used` int(11) NOT NULL,
  `free` int(11) NOT NULL,
  `percent` double NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `serversMemory` (
  `ID` int(11) NOT NULL,
  `serversTokenID` int(11) NOT NULL,
  `total` bigint(11) NOT NULL,
  `available` bigint(11) NOT NULL,
  `percent` double NOT NULL,
  `used` bigint(11) NOT NULL,
  `free` bigint(11) NOT NULL,
  `active` bigint(11) NOT NULL,
  `inactive` bigint(11) NOT NULL,
  `buffers` bigint(11) NOT NULL,
  `cached` bigint(11) NOT NULL,
  `shared` bigint(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `serversNetwork` (
  `ID` int(11) NOT NULL,
  `serversTokenID` int(11) NOT NULL,
  `nic` int(11) NOT NULL,
  `bytesTX` bigint(11) NOT NULL,
  `bytesRX` bigint(11) NOT NULL,
  `packetsTX` bigint(11) NOT NULL,
  `packetsRX` bigint(11) NOT NULL,
  `errorTX` int(11) NOT NULL,
  `errorRX` int(11) NOT NULL,
  `droppedTX` int(11) NOT NULL,
  `droppedRX` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `serversSwap` (
  `ID` int(11) NOT NULL,
  `serversTokenID` int(11) NOT NULL,
  `total` bigint(11) NOT NULL,
  `used` bigint(11) NOT NULL,
  `free` bigint(11) NOT NULL,
  `percent` double NOT NULL,
  `sinTX` int(11) NOT NULL,
  `sinRX` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `serversToken` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL DEFAULT 'Bermuda',
  `Token` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `status_pages` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Token` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `threads` (
  `THREAD_ID` varchar(11) NOT NULL,
  `THREAD_LOCK` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Rank` int(11) NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT 0,
  `activation_hash` varchar(40) NOT NULL,
  `Check_Limit` int(11) NOT NULL DEFAULT 10,
  `Contact_Limit` int(11) NOT NULL DEFAULT 4,
  `Same_IP_Limit` int(11) NOT NULL DEFAULT 2,
  `Group_Limit` int(11) NOT NULL DEFAULT 15,
  `StatusPage_Limit` int(11) DEFAULT 4,
  `WebHookLimit` int(11) NOT NULL DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `webhooks` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Method` varchar(10) NOT NULL,
  `urlDown` varchar(200) NOT NULL,
  `jsonDown` text NOT NULL,
  `headersDown` text NOT NULL,
  `urlUp` varchar(200) NOT NULL,
  `jsonUp` text NOT NULL,
  `headersUp` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `checks`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `emails`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `EMail` (`EMail`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`);

ALTER TABLE `groups`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `groups_checks`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `groups_emails`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `history`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `login_blacklist`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `register_blacklist`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `remote`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `serversCPU`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `serversDisk`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `serversMemory`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `serversNetwork`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `serversSwap`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `serversToken`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `status_pages`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Token` (`Token`);

ALTER TABLE `threads`
  ADD PRIMARY KEY (`THREAD_ID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`);

ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`ID`);


ALTER TABLE `checks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `emails`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `groups_checks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `groups_emails`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `history`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `login_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `register_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `remote`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `serversCPU`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `serversDisk`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `serversMemory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `serversNetwork`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `serversSwap`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `serversToken`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `status_pages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `webhooks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
