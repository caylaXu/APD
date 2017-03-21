CREATE TABLE IF NOT EXISTS `RltThirdUser` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `UserId` bigint(15) NOT NULL COMMENT '用户Id',
  `ThirdId` varchar(32) NOT NULL,
  `Type` enum('wechat','weibo','qq') NOT NULL,
  `NickName` varchar(32) NOT NULL,
  `Status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '-1:已删除 0：未激活 1：已激活',
  `Method` int(2) NOT NULL DEFAULT '0' COMMENT '-1：删除 0:新增 1：修改',
  `Modified` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='第三方用户关联表';

ALTER DATABASE APD CHARACTER SET utf8mb4;
ALTER TABLE Checklist CONVERT TO CHARACTER SET utf8mb4;
ALTER TABLE Projects CONVERT TO CHARACTER SET utf8mb4;
ALTER TABLE Tasks CONVERT TO CHARACTER SET utf8mb4;
ALTER TABLE Users CONVERT TO CHARACTER SET utf8mb4;
ALTER TABLE RltThirdUser CONVERT TO CHARACTER SET utf8mb4;
SHOW VARIABLES WHERE Variable_name LIKE 'character\_set\_%' OR Variable_name LIKE 'collation%'


