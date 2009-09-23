
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- admin table
--
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` INT NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY(admin_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- content table
--
CREATE TABLE IF NOT EXISTS `content` (
  `content_id` INT NOT NULL AUTO_INCREMENT,
  `keyword` CHAR(25) NOT NULL,
  `value` LONGTEXT NOT NULL,
  PRIMARY KEY(content_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE content ADD UNIQUE INDEX content_idx1(keyword);

--
-- Data dump to content table
--
INSERT INTO `content` (`keyword`, `value`) VALUES
('front','<h1> HvZ</h1>\r\n<p>Humans Vs. Zombies has been unleashed! <br >\r\n<br>If you have any questions or concerns, please contact your moderators.<br><br>\r\nHappy Hunting!</p>');
INSERT INTO `content` (`keyword`, `value`) VALUES
('rules', '<li>Don''t be a D-Bag!</li>\r\n<li>No realistic looking weaponry</li>\r\n<li>Guns may not be visible inside of academic buildings or jobs on campus</li>\r\n<li>No cars (segways allowed)</li>\r\n<p><strong>Required Equipment:</strong></p>\r\n\r\n<li>Bandana</li> \r\n\r\n<li>Dart Launcher (or socks)</li>\r\n<li>One ID index card</li><br>\r\n<p><strong>Safe Zones:</strong><br>Dorm rooms, Bathrooms, Academic buildings, Library, SRC, \r\n  Health Center, Dining Halls.<br>\r\n  Everywhere else is Free Game. <br><br>\r\n\r\n*A zombie must have both feet \r\n  outside of a safe zone to tag a human.</p>\r\n<p><strong>Non-participants</strong><br>\r\n People who are not registered participants may not directly \r\n  interact with the game.</p>\r\n\r\n<p><strong>Human Rules</strong></p>\r\n<p><strong>Conditions for Winning:</strong><br>\r\n  Humans win when the last \r\n  zombie starves to death.</p>\r\n<p><strong>Staying on campus:<br>\r\n  </strong>Humans must sleep on campus. If for whatever reason you need to leave \r\n  campus for longer than 24 hours, we apologize, but there are no exceptions.</p>\r\n<p><strong>ID number:</strong><br>\r\n  Every Human player must keep one index card with their unique identification \r\n  number on them at all times.</p>\r\n\r\n<p><strong>Stunning a Zombie:</strong><br>\r\nHumans may stun a Zombie for 15 minutes by shooting them \r\n  with a nerf gun or throwing a sock at them.</p>\r\n<p>*Only single shot/Non-automatic \r\n  weapons are allowed until the Humans are notified otherwise.</p>\r\n<p><strong>When tagged by a Zombie:<br>\r\n  </strong>When tagged by a Zombie, a Human is required to distribute their ID \r\n  card. One hour from being tagged a Human becomes a member of the Zombie team. \r\n</p>\r\n<p>*One hour from being tagged \r\n  you must begin wearing your bandana around your head - you are then allowed \r\n  to tag other Humans.</p>\r\n<p><strong>Zombie Rules </strong></p>\r\n<p><strong>Conditions for Winning:<br>\r\n\r\n  </strong>The Zombies win when the Human team has no remaining members.</p>\r\n<p><strong>Wearing your Headband:<br>\r\n  </strong>The Zombie team must wear a bandana around their head at all times.</p>\r\n<p><strong>Tagging:<br>\r\n  </strong>A tag is a firm touch to any part of a Human. After tagging a Human \r\n  the Zombie must collect their ID card. Kills must be reported within three hours.</p>\r\n<p><strong>Feeding:<br>\r\n  </strong>Zombie must feed every 48 hours. A zombie feeds by reporting their \r\n  kill on the website. A zombie may choose two other zombies to join in the feed.</p>\r\n\r\n<p><strong>Getting shot:<br>\r\n  </strong>When shot with a nerf gun or hit with a sock a Zombie is stunned for \r\n  15 minutes. A stunned zombie may not interact with the game in any way.</p>\r\n<p>*This includes shielding other \r\n  zombies from bullets or continuing to run toward a human.<br>\r\n  *If shot while stunned, a zombie remains stunned for the next 15 minutes.</p>');

--
-- timezone table
--
CREATE TABLE IF NOT EXISTS `timezone` (
  `timezone_id` INT NOT NULL AUTO_INCREMENT,
  `zone` tinytext NOT NULL,
  PRIMARY KEY(timezone_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Data dump to timezone table
--
INSERT INTO `timezone` (`zone`) VALUES
('US/Eastern');

--
-- users table
--
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pic_path` varchar(255) NOT NULL,
  `state` mediumint(9) NOT NULL,
  `killed` timestamp NULL,
  `feed` timestamp NULL,
  `kills` mediumint(9) NOT NULL,
  `killed_by` varchar(255) NOT NULL,
  `oz_opt` tinyint(4) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `starved` timestamp NULL,
  PRIMARY KEY(user_id, id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE users ADD INDEX users_idx1(state);
ALTER TABLE users ADD UNIQUE INDEX users_idx2(id);

--
-- variables table
--
CREATE TABLE IF NOT EXISTS `variables` (
  `variable_id` INT NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `value` mediumint(9) NOT NULL,
  PRIMARY KEY(variable_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE variables ADD UNIQUE INDEX variables_idx1(keyword);

--
-- Data dump to variables table
--
INSERT INTO `variables` (`keyword`, `value`) VALUES
('oz-selected', 0),
('game-started', 0),
('oz-revealed', 0),
('reg-open', 0),
('reg-closed', 0),
('game-over', 0);
