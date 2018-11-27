SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `history_mail` (
  `idMail` int(11) NOT NULL,
  `titleItem` text NOT NULL,
  `messageItem` text NOT NULL,
  `idItem` int(11) NOT NULL,
  `countItem` int(11) NOT NULL,
  `maxCountItem` int(11) NOT NULL,
  `octetItem` text NOT NULL,
  `prototypeItem` int(11) NOT NULL,
  `timeItem` int(11) NOT NULL,
  `maskItem` int(11) NOT NULL,
  `moneyItem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `history_mail`
  ADD PRIMARY KEY (`idMail`);

ALTER TABLE `history_mail`
  MODIFY `idMail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
