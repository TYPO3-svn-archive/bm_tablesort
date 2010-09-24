<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/Tablesort/', 'Tablesort');

t3lib_div::loadTCA('tt_content');
if (t3lib_div::int_from_ver(TYPO3_version) >= 4002000) { //since version 4.2
	t3lib_extMgm::addPiFlexFormValue('*', 'FILE:EXT:bm_tablesort/flexform_ds.xml','table'); 
} else {
	$TCA['tt_content']['columns']['pi_flexform']['config']['ds']['default'] = 'FILE:EXT:bm_tablesort/flexform_ds.xml';
}
?>
