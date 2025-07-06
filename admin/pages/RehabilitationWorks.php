<?
if($Action=='RehabilitationWorks'){ // ==================== RehabilitationWorks
	/** /
	mysqli_query($baza,'CREATE TABLE IF NOT EXISTS `News` (
		  `ID` int NOT NULL AUTO_INCREMENT,
		  `Pos` int NOT NULL,
		  `ThemeID` int DEFAULT NULL,
		  `DTime` datetime NOT NULL,
		  `Header` text NOT NULL,
		  `Text` longtext NOT NULL,
		  `Photo` text NOT NULL,
		  `Media` text NOT NULL,
		  PRIMARY KEY (`ID`),
		  KEY `Pos` (`Pos`),
		  KEY `DTime` (`DTime`),
		  KEY `ThemeID` (`ThemeID`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;');
	/**/
	$Img_width=1200;
	$Img_height=900;
	$Img_fit=false;
	$imgFilling=false;
	include 'newsTable.php';
	}
?>