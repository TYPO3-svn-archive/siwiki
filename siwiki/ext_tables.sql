--
-- Tabellenstruktur für Tabelle `tx_siwiki_articles`
--

CREATE TABLE `tx_siwiki_articles` (
  `uid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `crdate` int(11) NOT NULL default '0',
  `cruser_id` int(11) NOT NULL default '0',
  `deleted` tinyint(4) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  `title` char(255) NOT NULL default '',
  `namespace` int(11) NOT NULL default '0',
  `article` text NOT NULL,
  `creator` varchar(255) NOT NULL default '',
  `editor` varchar(255) NOT NULL default '',
  `version` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `title` (`title`,`namespace`),
  KEY `parent` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tx_siwiki_articles_cache`
--

CREATE TABLE `tx_siwiki_articles_cache` (
  `uid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `crdate` int(11) NOT NULL default '0',
  `cruser_id` int(11) NOT NULL default '0',
  `deleted` tinyint(4) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  `title` char(255) NOT NULL default '',
  `namespace` int(11) NOT NULL default '0',
  `article` text NOT NULL,
  `creator` varchar(255) NOT NULL default '',
  `editor` varchar(255) NOT NULL default '',
  `version` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `title` (`title`,`namespace`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tx_siwiki_articles_references`
--

CREATE TABLE `tx_siwiki_articles_references` (
  `uid` int(11) NOT NULL auto_increment,
  `tstamp` int(11) NOT NULL default '0',
  `linking_uid` int(11) NOT NULL default '0',
  `linked_uid` int(11) NOT NULL default '0',
  `linked_title` char(255) NOT NULL default '',
  `linked_namespace` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `linking_uid` (`linking_uid`,`linked_uid`,`linked_title`,`linked_namespace`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tx_siwiki_articles_versions`
--

CREATE TABLE `tx_siwiki_articles_versions` (
  `uid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `crdate` int(11) NOT NULL default '0',
  `cruser_id` int(11) NOT NULL default '0',
  `deleted` tinyint(4) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  `article_uid` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `namespace` int(11) NOT NULL default '0',
  `article` text NOT NULL,
  `creator` varchar(255) NOT NULL default '', 
  `editor` varchar(255) NOT NULL default '',
  `comment` text,
  `version` int(11) default '0',
  PRIMARY KEY  (`uid`),
  KEY `parent` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC  ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tx_siwiki_namespaces`
--

CREATE TABLE `tx_siwiki_namespaces` (
  `uid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  `deleted` tinyint(4) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `crdate` int(11) NOT NULL default '0',
  `cruser_id` int(11) NOT NULL default '0',
  `name` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`uid`),
  KEY `parent` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tx_siwiki_notifications`
--

CREATE TABLE `tx_siwiki_notifications` (
  `uid` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `user_uid` int(11) NOT NULL default '0',
  `article_uid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `user_uid` (`user_uid`,`article_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tx_siwiki_articles_locking`
--

CREATE TABLE `tx_siwiki_articles_locking` (
  `uid` int(11) NOT NULL default '0',
  `crdate` int(11) NOT NULL default '0',
  `tstamp` int(11) NOT NULL default '0',
  `user` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
