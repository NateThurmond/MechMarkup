CREATE DATABASE IF NOT EXISTS mechMarkup;
USE mechMarkup;

CREATE TABLE `views` (
  `uuid` text NOT NULL,
  `view1` text NOT NULL,
  `view2` text NOT NULL,
  `view3` text NOT NULL,
  `view4` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1

