-- --------------------------------------------------------

-- 
-- 資料表格式： `dgpd_articles`
-- 

CREATE TABLE `dgpd_articles` (
  `aid` int(10) unsigned NOT NULL auto_increment,
  `iid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL default '0',
  `atitle` varchar(255) default NULL,
  `asubtitle` varchar(255) default NULL,
  `actb_uid` int(10) unsigned NOT NULL default '0',
  `acontributor` varchar(40) default NULL,
  `atch_uid` int(10) unsigned default '0',
  `ateacher` varchar(40) default NULL,
  `aauthor` varchar(40) default NULL,
  `acontent` text,
  `aorder` int(10) unsigned default '0',
  `acounter` int(10) unsigned default NULL,
  `adate_time` varchar(20) default NULL,
  `ruid` int(10) unsigned default NULL,
  `rcomment` text,
  `rdate_time` varchar(20) default NULL,
  `rpass` tinyint(2) unsigned default '0',
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 資料表格式： `dgpd_categories`
-- 

CREATE TABLE `dgpd_categories` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `iid` int(10) unsigned NOT NULL,
  `ucid` int(10) unsigned NOT NULL default '0',
  `ctitle` varchar(255) default NULL,
  `cintroduction` text,
  `corder` int(10) unsigned default '0',
  `ccanctb` tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 資料表格式： `dgpd_files`
-- 

CREATE TABLE `dgpd_files` (
  `fid` int(10) unsigned NOT NULL auto_increment,
  `iid` int(10) unsigned NOT NULL,
  `aid` int(10) unsigned NOT NULL,
  `file_name` varchar(255) default NULL,
  `file_type` varchar(50) default NULL,
  `file_size` varchar(25) default NULL,
  `real_name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `counter` int(10) unsigned default NULL,
  `date_time` varchar(20) default NULL,
  PRIMARY KEY  (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 資料表格式： `dgpd_issues`
-- 

CREATE TABLE `dgpd_issues` (
  `iid` int(10) unsigned NOT NULL auto_increment,
  `ititle` varchar(40) NOT NULL,
  `idate` varchar(20) default NULL,
  `iintroduction` text,
  `ipublished` tinyint(2) unsigned default '0',
  PRIMARY KEY  (`iid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
