<?php

########################################################################
# Extension Manager/Repository config file for ext: "siwiki"
#
# Auto generated 15-06-2009 16:52
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'siwiki',
	'description' => 'this is a mvc based wiki using yui rte',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.0.3',
	'dependencies' => 'lib,div',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stefan Isak, Andreas Lappe',
	'author_email' => 'stefan.isak@konplan.com, andreas.lappe@konplan.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'lib' => '0.1.0',
			'div' => '0.1.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'nd_yui_css' => '1.2.0',
		),
	),
	'_md5_values_when_last_written' => 'a:212:{s:9:"ChangeLog";s:4:"5b13";s:9:"docgen.sh";s:4:"c396";s:12:"ext_icon.gif";s:4:"dac8";s:20:"ext_icon_article.png";s:4:"1e34";s:22:"ext_icon_namespace.png";s:4:"d847";s:17:"ext_localconf.php";s:4:"9fce";s:14:"ext_tables.php";s:4:"64b0";s:14:"ext_tables.sql";s:4:"3ff8";s:13:"locallang.xml";s:4:"95dc";s:16:"locallang_db.xml";s:4:"ef68";s:7:"tca.php";s:4:"cda3";s:45:"views/class.tx_siwiki_views_latestUpdates.php";s:4:"f986";s:38:"views/class.tx_siwiki_views_siwiki.php";s:4:"a214";s:38:"models/class.tx_siwiki_models_ajax.php";s:4:"b99f";s:41:"models/class.tx_siwiki_models_article.php";s:4:"0432";s:46:"models/class.tx_siwiki_models_articleCache.php";s:4:"805a";s:48:"models/class.tx_siwiki_models_articleVersion.php";s:4:"ec2f";s:38:"models/class.tx_siwiki_models_diff.php";s:4:"0662";s:39:"models/class.tx_siwiki_models_files.php";s:4:"c45e";s:47:"models/class.tx_siwiki_models_latestUpdates.php";s:4:"14c1";s:41:"models/class.tx_siwiki_models_locking.php";s:4:"faf3";s:43:"models/class.tx_siwiki_models_namespace.php";s:4:"9567";s:46:"models/class.tx_siwiki_models_notification.php";s:4:"eb1a";s:47:"models/class.tx_siwiki_models_plotrelations.php";s:4:"c426";s:37:"models/class.tx_siwiki_models_toc.php";s:4:"c2c0";s:40:"models/class.tx_siwiki_models_upload.php";s:4:"5ec8";s:14:"doc/blank.html";s:4:"c225";s:25:"doc/classtrees_TYPO3.html";s:4:"5540";s:27:"doc/classtrees_default.html";s:4:"1655";s:21:"doc/elementindex.html";s:4:"0d6f";s:27:"doc/elementindex_TYPO3.html";s:4:"9422";s:29:"doc/elementindex_default.html";s:4:"31d0";s:15:"doc/errors.html";s:4:"2f53";s:14:"doc/index.html";s:4:"8bde";s:17:"doc/li_TYPO3.html";s:4:"2295";s:19:"doc/li_default.html";s:4:"2b5a";s:14:"doc/manual.sxw";s:4:"37fb";s:17:"doc/packages.html";s:4:"a649";s:22:"doc/ric_ChangeLog.html";s:4:"89a7";s:46:"doc/TYPO3/tx_siwiki/_class.ext_update.php.html";s:4:"69c1";s:68:"doc/TYPO3/tx_siwiki/_classes---class.tx_siwiki_classes_misc.php.html";s:4:"4cfb";s:76:"doc/TYPO3/tx_siwiki/_controllers---class.tx_siwiki_controllers_ajax.php.html";s:4:"b045";s:85:"doc/TYPO3/tx_siwiki/_controllers---class.tx_siwiki_controllers_latestUpdates.php.html";s:4:"8564";s:78:"doc/TYPO3/tx_siwiki/_controllers---class.tx_siwiki_controllers_siwiki.php.html";s:4:"db5d";s:40:"doc/TYPO3/tx_siwiki/_ext_tables.php.html";s:4:"9ef4";s:66:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_ajax.php.html";s:4:"36b5";s:69:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_article.php.html";s:4:"92fa";s:74:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_articleCache.php.html";s:4:"d441";s:76:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_articleVersion.php.html";s:4:"4e35";s:66:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_diff.php.html";s:4:"8b29";s:67:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_files.php.html";s:4:"7fde";s:75:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_latestUpdates.php.html";s:4:"f521";s:69:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_locking.php.html";s:4:"aca1";s:71:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_namespace.php.html";s:4:"990d";s:74:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_notification.php.html";s:4:"58d2";s:75:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_plotrelations.php.html";s:4:"25fd";s:65:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_toc.php.html";s:4:"0c5f";s:68:"doc/TYPO3/tx_siwiki/_models---class.tx_siwiki_models_upload.php.html";s:4:"85e4";s:33:"doc/TYPO3/tx_siwiki/_tca.php.html";s:4:"17cd";s:73:"doc/TYPO3/tx_siwiki/_views---class.tx_siwiki_views_latestUpdates.php.html";s:4:"c81b";s:66:"doc/TYPO3/tx_siwiki/_views---class.tx_siwiki_views_siwiki.php.html";s:4:"b4e6";s:35:"doc/TYPO3/tx_siwiki/ext_update.html";s:4:"2a09";s:47:"doc/TYPO3/tx_siwiki/tx_siwiki_classes_misc.html";s:4:"7d1c";s:51:"doc/TYPO3/tx_siwiki/tx_siwiki_controllers_ajax.html";s:4:"3404";s:60:"doc/TYPO3/tx_siwiki/tx_siwiki_controllers_latestUpdates.html";s:4:"c523";s:53:"doc/TYPO3/tx_siwiki/tx_siwiki_controllers_siwiki.html";s:4:"2297";s:46:"doc/TYPO3/tx_siwiki/tx_siwiki_models_ajax.html";s:4:"7b7a";s:49:"doc/TYPO3/tx_siwiki/tx_siwiki_models_article.html";s:4:"4909";s:54:"doc/TYPO3/tx_siwiki/tx_siwiki_models_articleCache.html";s:4:"e388";s:56:"doc/TYPO3/tx_siwiki/tx_siwiki_models_articleVersion.html";s:4:"3518";s:46:"doc/TYPO3/tx_siwiki/tx_siwiki_models_diff.html";s:4:"5372";s:47:"doc/TYPO3/tx_siwiki/tx_siwiki_models_files.html";s:4:"265d";s:55:"doc/TYPO3/tx_siwiki/tx_siwiki_models_latestUpdates.html";s:4:"b0bc";s:49:"doc/TYPO3/tx_siwiki/tx_siwiki_models_locking.html";s:4:"1fc6";s:51:"doc/TYPO3/tx_siwiki/tx_siwiki_models_namespace.html";s:4:"c758";s:54:"doc/TYPO3/tx_siwiki/tx_siwiki_models_notification.html";s:4:"be8f";s:55:"doc/TYPO3/tx_siwiki/tx_siwiki_models_plotrelations.html";s:4:"8981";s:45:"doc/TYPO3/tx_siwiki/tx_siwiki_models_toc.html";s:4:"9cbe";s:48:"doc/TYPO3/tx_siwiki/tx_siwiki_models_upload.html";s:4:"045c";s:54:"doc/TYPO3/tx_siwiki/tx_siwiki_views_latestUpdates.html";s:4:"ca34";s:47:"doc/TYPO3/tx_siwiki/tx_siwiki_views_siwiki.html";s:4:"931c";s:20:"doc/media/banner.css";s:4:"e43c";s:24:"doc/media/stylesheet.css";s:4:"be83";s:32:"doc/default/_ext_emconf.php.html";s:4:"8641";s:35:"doc/default/_ext_localconf.php.html";s:4:"3b75";s:50:"doc/default/_resources---DifferenceEngine.php.html";s:4:"f0a0";s:40:"doc/default/_resources---upload.php.html";s:4:"6b31";s:38:"doc/default/_templates---diff.php.html";s:4:"d9af";s:38:"doc/default/_templates---edit.php.html";s:4:"5f25";s:39:"doc/default/_templates---error.php.html";s:4:"ad60";s:38:"doc/default/_templates---json.php.html";s:4:"41c9";s:37:"doc/default/_templates---new.php.html";s:4:"dfff";s:37:"doc/default/_templates---png.php.html";s:4:"a4ad";s:38:"doc/default/_templates---push.php.html";s:4:"5ca4";s:40:"doc/default/_templates---siwiki.php.html";s:4:"361d";s:37:"doc/default/_templates---toc.php.html";s:4:"1ea6";s:41:"doc/default/_templates---updates.php.html";s:4:"d8ab";s:31:"doc/default/tx_siwiki_diff.html";s:4:"7e9b";s:40:"doc/default/tx_siwiki_diffFormatter.html";s:4:"a035";s:33:"doc/default/tx_siwiki_diffop.html";s:4:"f660";s:37:"doc/default/tx_siwiki_diffop_Add.html";s:4:"4cb5";s:40:"doc/default/tx_siwiki_diffop_Change.html";s:4:"16b6";s:38:"doc/default/tx_siwiki_diffop_Copy.html";s:4:"9a0c";s:40:"doc/default/tx_siwiki_diffop_Delete.html";s:4:"e917";s:47:"doc/default/tx_siwiki_hwldfWordAccumulator.html";s:4:"418d";s:37:"doc/default/tx_siwiki_mappedDiff.html";s:4:"863c";s:45:"doc/default/tx_siwiki_tableDiffFormatter.html";s:4:"c208";s:47:"doc/default/tx_siwiki_unifiedDiffFormatter.html";s:4:"1271";s:40:"doc/default/tx_siwiki_wordLevelDiff.html";s:4:"5d15";s:40:"classes/class.tx_siwiki_classes_misc.php";s:4:"1baf";s:48:"controllers/class.tx_siwiki_controllers_ajax.php";s:4:"2263";s:57:"controllers/class.tx_siwiki_controllers_latestUpdates.php";s:4:"32b5";s:50:"controllers/class.tx_siwiki_controllers_siwiki.php";s:4:"2f9b";s:18:"templates/diff.php";s:4:"7329";s:18:"templates/edit.php";s:4:"821c";s:19:"templates/error.php";s:4:"d047";s:18:"templates/json.php";s:4:"18c3";s:17:"templates/new.php";s:4:"9747";s:17:"templates/png.php";s:4:"0b18";s:18:"templates/push.php";s:4:"fcb9";s:20:"templates/siwiki.php";s:4:"a50e";s:17:"templates/toc.php";s:4:"2151";s:21:"templates/updates.php";s:4:"66bf";s:28:"configurations/constants.txt";s:4:"ee12";s:27:"configurations/flexform.xml";s:4:"966b";s:24:"configurations/setup.txt";s:4:"1cfc";s:30:"resources/DifferenceEngine.php";s:4:"142f";s:28:"resources/siwiki-combined.js";s:4:"43a0";s:20:"resources/upload.php";s:4:"7a0f";s:31:"resources/yui-combined_2.6.0.js";s:4:"b3da";s:32:"resources/yui-combined_2.7.0b.js";s:4:"9ae5";s:23:"resources/yui_2.7.0b.js";s:4:"d9d7";s:26:"resources/images/add16.png";s:4:"0fde";s:27:"resources/images/back16.png";s:4:"3138";s:29:"resources/images/cancel16.png";s:4:"18c3";s:29:"resources/images/delete16.png";s:4:"10a2";s:27:"resources/images/edit16.png";s:4:"31cb";s:28:"resources/images/error22.png";s:4:"6a5d";s:34:"resources/images/filemanager16.png";s:4:"b8a5";s:34:"resources/images/filemanager32.png";s:4:"21ce";s:27:"resources/images/home16.png";s:4:"4660";s:28:"resources/images/index16.png";s:4:"0ec9";s:27:"resources/images/info16.png";s:4:"a1d8";s:27:"resources/images/info32.png";s:4:"a579";s:30:"resources/images/loading22.gif";s:4:"a831";s:31:"resources/images/loadingBar.gif";s:4:"7d98";s:35:"resources/images/notification16.png";s:4:"2774";s:25:"resources/images/ok22.png";s:4:"799e";s:31:"resources/images/overview16.png";s:4:"c8b4";s:29:"resources/images/revert16.png";s:4:"dd76";s:31:"resources/images/revision22.png";s:4:"5729";s:27:"resources/images/save16.png";s:4:"6e05";s:29:"resources/images/search16.png";s:4:"213d";s:32:"resources/images/signature16.png";s:4:"9804";s:32:"resources/images/signature22.png";s:4:"c59f";s:32:"resources/images/signature32.png";s:4:"e3e1";s:26:"resources/images/toc16.png";s:4:"0e98";s:31:"resources/images/versions16.png";s:4:"9f82";s:30:"resources/images/warning32.png";s:4:"10c4";s:40:"resources/build/assets/skins/sam/asc.gif";s:4:"7053";s:49:"resources/build/assets/skins/sam/autocomplete.css";s:4:"c4e2";s:47:"resources/build/assets/skins/sam/blankimage.png";s:4:"91c1";s:43:"resources/build/assets/skins/sam/button.css";s:4:"d0ce";s:45:"resources/build/assets/skins/sam/calendar.css";s:4:"cdfa";s:45:"resources/build/assets/skins/sam/carousel.css";s:4:"890b";s:48:"resources/build/assets/skins/sam/colorpicker.css";s:4:"1a19";s:46:"resources/build/assets/skins/sam/container.css";s:4:"8672";s:46:"resources/build/assets/skins/sam/datatable.css";s:4:"1e79";s:41:"resources/build/assets/skins/sam/desc.gif";s:4:"4708";s:48:"resources/build/assets/skins/sam/dt-arrow-dn.png";s:4:"ee0d";s:48:"resources/build/assets/skins/sam/dt-arrow-up.png";s:4:"2749";s:48:"resources/build/assets/skins/sam/editor-knob.gif";s:4:"43c2";s:57:"resources/build/assets/skins/sam/editor-sprite-active.gif";s:4:"e7a7";s:50:"resources/build/assets/skins/sam/editor-sprite.gif";s:4:"b72b";s:43:"resources/build/assets/skins/sam/editor.css";s:4:"e79b";s:54:"resources/build/assets/skins/sam/header_background.png";s:4:"4122";s:48:"resources/build/assets/skins/sam/html_editor.gif";s:4:"f1cf";s:43:"resources/build/assets/skins/sam/hue_bg.png";s:4:"73ae";s:49:"resources/build/assets/skins/sam/imagecropper.css";s:4:"0adc";s:43:"resources/build/assets/skins/sam/layout.css";s:4:"4d10";s:50:"resources/build/assets/skins/sam/layout_sprite.png";s:4:"0f5a";s:44:"resources/build/assets/skins/sam/loading.gif";s:4:"8f13";s:43:"resources/build/assets/skins/sam/logger.css";s:4:"97ff";s:63:"resources/build/assets/skins/sam/menu-button-arrow-disabled.png";s:4:"4df7";s:54:"resources/build/assets/skins/sam/menu-button-arrow.png";s:4:"6305";s:41:"resources/build/assets/skins/sam/menu.css";s:4:"5062";s:65:"resources/build/assets/skins/sam/menubaritem_submenuindicator.png";s:4:"1424";s:74:"resources/build/assets/skins/sam/menubaritem_submenuindicator_disabled.png";s:4:"d8c2";s:54:"resources/build/assets/skins/sam/menuitem_checkbox.png";s:4:"01d5";s:63:"resources/build/assets/skins/sam/menuitem_checkbox_disabled.png";s:4:"6d9c";s:62:"resources/build/assets/skins/sam/menuitem_submenuindicator.png";s:4:"10f0";s:71:"resources/build/assets/skins/sam/menuitem_submenuindicator_disabled.png";s:4:"42a8";s:46:"resources/build/assets/skins/sam/paginator.css";s:4:"b5e6";s:48:"resources/build/assets/skins/sam/picker_mask.png";s:4:"a4d3";s:51:"resources/build/assets/skins/sam/profilerviewer.css";s:4:"6cf0";s:43:"resources/build/assets/skins/sam/resize.css";s:4:"4650";s:49:"resources/build/assets/skins/sam/simpleeditor.css";s:4:"e79b";s:41:"resources/build/assets/skins/sam/skin.css";s:4:"a01d";s:43:"resources/build/assets/skins/sam/slider.css";s:4:"cde3";s:62:"resources/build/assets/skins/sam/split-button-arrow-active.png";s:4:"8902";s:64:"resources/build/assets/skins/sam/split-button-arrow-disabled.png";s:4:"db73";s:61:"resources/build/assets/skins/sam/split-button-arrow-focus.png";s:4:"36e6";s:61:"resources/build/assets/skins/sam/split-button-arrow-hover.png";s:4:"36e6";s:55:"resources/build/assets/skins/sam/split-button-arrow.png";s:4:"ced9";s:43:"resources/build/assets/skins/sam/sprite.png";s:4:"96b2";s:43:"resources/build/assets/skins/sam/sprite.psd";s:4:"1c35";s:44:"resources/build/assets/skins/sam/tabview.css";s:4:"dd0f";s:53:"resources/build/assets/skins/sam/treeview-loading.gif";s:4:"8f13";s:52:"resources/build/assets/skins/sam/treeview-sprite.gif";s:4:"115a";s:45:"resources/build/assets/skins/sam/treeview.css";s:4:"cf07";s:41:"resources/build/assets/skins/sam/wait.gif";s:4:"b0cd";s:44:"resources/build/assets/skins/sam/yuitest.css";s:4:"fc05";}',
	'suggests' => array(
	),
);

?>
