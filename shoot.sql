CREATE TABLE `lichun`.`shoot` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '姓名',
  `team` varchar(20) unsigned NOT NULL DEFAULT '' COMMENT '队名',
  `shots` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '射门数',
  `left` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '左脚',
  `right` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '右脚',
  `head` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '头球',
  `other` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '其它部位',
  `rank` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT '排名',
  `updated_at` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  `created_at` int(10) unsigned DEFAULT 0 COMMENT '注册时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间（软删）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='shoot';
