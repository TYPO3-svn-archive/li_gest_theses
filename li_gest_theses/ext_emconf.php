<?php

########################################################################
# Extension Manager/Repository config file for ext: "li_gest_theses"
#
# Auto generated 03-06-2009 14:33
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Managing Thesis/HDR',
	'description' => 'Insert a list of thesis/HDR',
	'category' => 'plugin',
	'author' => 'Bruno Gallet',
	'author_email' => 'gallet.bruno@gmail.com',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tx_ligestmembrelabo_MembreDuLabo',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.3',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:67:{s:9:"ChangeLog";s:4:"e5f1";s:12:"ext_icon.gif";s:4:"0e19";s:17:"ext_localconf.php";s:4:"1896";s:14:"ext_tables.php";s:4:"f924";s:14:"ext_tables.sql";s:4:"4738";s:19:"flexform_ds_pi1.xml";s:4:"8a7c";s:34:"icon_tx_ligesttheses_Cotutelle.gif";s:4:"0e19";s:31:"icon_tx_ligesttheses_Dirige.gif";s:4:"0e19";s:36:"icon_tx_ligesttheses_Financement.gif";s:4:"0e19";s:33:"icon_tx_ligesttheses_TheseHDR.gif";s:4:"0e19";s:13:"locallang.xml";s:4:"73b5";s:16:"locallang_db.xml";s:4:"fc47";s:7:"tca.php";s:4:"fafb";s:14:"pi1/ce_wiz.gif";s:4:"e8cd";s:33:"pi1/class.tx_ligesttheses_pi1.php";s:4:"dc0b";s:41:"pi1/class.tx_ligesttheses_pi1_wizicon.php";s:4:"e6d5";s:13:"pi1/clear.gif";s:4:"cc11";s:32:"pi1/li_gest_theses_template.html";s:4:"0dab";s:37:"pi1/li_gest_theses_template_test.html";s:4:"cb0e";s:17:"pi1/locallang.xml";s:4:"5838";s:24:"pi1/static/editorcfg.txt";s:4:"6d0e";s:67:"csh/ligesttheses_locallang_csh_tx_ligestmembrelabo_MembreDuLabo.xml";s:4:"140b";s:60:"csh/ligesttheses_locallang_csh_tx_ligesttheses_Cotutelle.xml";s:4:"2a17";s:57:"csh/ligesttheses_locallang_csh_tx_ligesttheses_Dirige.xml";s:4:"e20a";s:59:"csh/ligesttheses_locallang_csh_tx_ligesttheses_TheseHDR.xml";s:4:"171f";s:12:"doc/Doxyfile";s:4:"7973";s:31:"doc/Typo3_Gallet_RapportPFE.pdf";s:4:"c864";s:38:"doc/bd_membres_theses_publications.pdf";s:4:"6110";s:25:"doc/doxygen_main_page.dox";s:4:"782b";s:22:"doc/li_gest_theses.pdf";s:4:"b411";s:18:"doc/html/Thumbs.db";s:4:"ee24";s:23:"doc/html/annotated.html";s:4:"c5a8";s:54:"doc/html/class_8tx__ligesttheses__pi1_8php-source.html";s:4:"e2a5";s:47:"doc/html/class_8tx__ligesttheses__pi1_8php.html";s:4:"b21e";s:63:"doc/html/class_8tx__ligesttheses__pi1__wizicon_8php-source.html";s:4:"89a4";s:56:"doc/html/class_8tx__ligesttheses__pi1__wizicon_8php.html";s:4:"1f73";s:48:"doc/html/classtx__ligesttheses__pi1-members.html";s:4:"0c8c";s:40:"doc/html/classtx__ligesttheses__pi1.html";s:4:"311a";s:57:"doc/html/classtx__ligesttheses__pi1__wizicon-members.html";s:4:"6f9e";s:49:"doc/html/classtx__ligesttheses__pi1__wizicon.html";s:4:"974d";s:20:"doc/html/doxygen.css";s:4:"2b5b";s:20:"doc/html/doxygen.png";s:4:"33f8";s:38:"doc/html/doxygen__main__page_8dox.html";s:4:"b018";s:37:"doc/html/ext__emconf_8php-source.html";s:4:"af5c";s:30:"doc/html/ext__emconf_8php.html";s:4:"9598";s:40:"doc/html/ext__localconf_8php-source.html";s:4:"f59f";s:33:"doc/html/ext__localconf_8php.html";s:4:"ceb8";s:37:"doc/html/ext__tables_8php-source.html";s:4:"5af2";s:30:"doc/html/ext__tables_8php.html";s:4:"c4c2";s:19:"doc/html/files.html";s:4:"a0ee";s:23:"doc/html/functions.html";s:4:"cec1";s:28:"doc/html/functions_func.html";s:4:"4cf1";s:28:"doc/html/functions_vars.html";s:4:"d459";s:21:"doc/html/globals.html";s:4:"2b1f";s:26:"doc/html/globals_vars.html";s:4:"7a61";s:25:"doc/html/graph_legend.dot";s:4:"2555";s:26:"doc/html/graph_legend.html";s:4:"238a";s:25:"doc/html/graph_legend.png";s:4:"5700";s:19:"doc/html/index.html";s:4:"39b6";s:32:"doc/html/namespace_t_y_p_o3.html";s:4:"288a";s:24:"doc/html/namespaces.html";s:4:"c84a";s:18:"doc/html/tab_b.gif";s:4:"a22e";s:18:"doc/html/tab_l.gif";s:4:"749f";s:18:"doc/html/tab_r.gif";s:4:"9802";s:17:"doc/html/tabs.css";s:4:"9656";s:29:"doc/html/tca_8php-source.html";s:4:"16e9";s:22:"doc/html/tca_8php.html";s:4:"40b7";}',
	'suggests' => array(
	),
);

?>