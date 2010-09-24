<?php
/************************************
*
* @date 19.07.2007
************************************/

//call hook in css_styled_content
$TYPO3_CONF_VARS['EXTCONF']['css_styled_content']['pi1_hooks']['render_table'] = 'EXT:bm_tablesort/lib/class.tx_bmtablesort_cssstyledcontent_hook.php:tx_bmtablesort_cssstyledcontent_hook';



/*  Adding Page TS Config Values
 *
 */

t3lib_extMgm::addPageTSConfig('
	TCEFORM.tt_content.layout.types.table.altLabels.23 = Sort Table
	TCEFORM.pages.layout.addItems = 23
');
?>
