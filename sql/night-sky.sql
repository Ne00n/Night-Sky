SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `checks` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `EMAIL_ID` int(11) NOT NULL,
  `ENABLED` int(1) NOT NULL DEFAULT '1',
  `SLOT` int(1) NOT NULL,
  `ONLINE` int(1) NOT NULL DEFAULT '0',
  `NAME` varchar(50) NOT NULL,
  `IP` varchar(50) NOT NULL,
  `PORT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `emails` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `EMail` varchar(50) NOT NULL,
  `Status` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `threads` (
  `THREAD_ID` varchar(11) NOT NULL,
  `THREAD_LOCK` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Rank` int(11) NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0',
  `activation_hash` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `checks`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `emails`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `EMail` (`EMail`);

ALTER TABLE `threads`
  ADD PRIMARY KEY (`THREAD_ID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `activation_hash` (`activation_hash`);


ALTER TABLE `checks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `emails`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
