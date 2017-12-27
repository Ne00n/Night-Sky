SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

ALTER TABLE `users` ADD `WebHookLimit` INT NOT NULL DEFAULT '15' AFTER `StatusPage_Limit`;

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


ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`ID`);


ALTER TABLE `webhooks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
