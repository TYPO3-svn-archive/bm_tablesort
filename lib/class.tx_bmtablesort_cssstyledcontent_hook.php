<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Content rendering' for the 'css_styled_content' extension.
 *
 * $Id: class.tx_cssstyledcontent_pi1.php 1981 2007-02-04 21:07:58Z mundaun $
 *
 * @author  Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   68: class tx_cssstyledcontent_pi1 extends tslib_pibase
 *
 *              SECTION: Rendering of Content Elements:
 *   96:     function render_bullets($content,$conf)
 *  141:     function render_table($content,$conf)
 *  283:     function render_uploads($content,$conf)
 *  395:     function render_textpic($content, $conf)
 *
 *              SECTION: Helper functions
 *  832:     function getTableAttributes($conf,$type)
 *  861:     function &hookRequest($functionName)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

class tx_bmtablesort_cssstyledcontent_hook  {

	/**
	 * Rendering the "Table" type content element, called from TypoScript (tt_content.table.20)
	 *
	 * @param	string		Content input. Not used, ignore.
	 * @param	array		TypoScript configuration
	 * @return	string		HTML output.
	 * @access private
	 */
	function render_table($content,$conf)	{


				// Init FlexForm configuration
			$this->pObj->pi_initPIflexForm();

				// Get bodytext field content
			$field = (isset($conf['field']) && trim($conf['field']) ? trim($conf['field']) : 'bodytext');
			$content = trim($this->pObj->cObj->data[$field]);
				if (!strcmp($content,''))	return '';
         
			// get flexform values
			//cssstyled standart
			$caption = trim(htmlspecialchars($this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'acctables_caption')));
			$summary = trim(htmlspecialchars($this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'acctables_summary')));
			$useTfoot = trim($this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'acctables_tfoot'));
			$headerPos = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'acctables_headerpos');
			$noStyles = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'acctables_nostyles');
			$tableClass = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'acctables_tableclass');

			$prefix_mode = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_bmtablesort.']['prefix'];
			$prefix_mode = ($prefix_mode == ''?'sortable':$prefix_mode);

			$delimiter = trim($this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tableparsing_delimiter','s_parsing'));
			if ($delimiter)	{
				$delimiter = chr(intval($delimiter));
			} else {
				$delimiter = '|';
			}
			$quotedInput = trim($this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tableparsing_quote','s_parsing'));
			if ($quotedInput)	{
				$quotedInput = chr(intval($quotedInput));
			} else {
				$quotedInput = '';
			}

			//flexform
			//debug($this->pObj->cObj->data['pi_flexform'],'flexform',__FILE__,__LINE__);

			//sortable table
			$tableSortable = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_sortable','s_sortable');
			//header sort datatypes
			$tableHeaderTypes = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_headertypes','s_sortable');
			$tableHeaderTypesArr = explode('|',$tableHeaderTypes);
			//header alignment
			$tableHeaderPos = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_headerpos','s_sortable');
			$tableHeaderPosArr = explode('|',$tableHeaderPos);
			//content alignment
			$tableContentPos = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_contentpos','s_sortable');
			$tableContentPosArr = explode('|',$tableContentPos);

			//table zebra striping
			$tableZebraStriping = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_zebrastriping','s_sortable');
			//table highlight current selected col
			$tableHighlightCur = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_highlightcurrentcol','s_sortable');
         $tableHighlightCur = ($tableHighlightCur ? $conf['sortable.']['defaultClassNames.']['highlightCurCol']:'' );
			//paginatation: amount of rows
			$tablePaginationRows = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_pagination_rows','s_sortable');
			$tablePaginationRows = (is_numeric($tablePaginationRows) ? $tablePaginationRows:0 );
			//paginatation: amount of pages
			$tablePaginationPages = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_pagination_pages','s_sortable');
			$tablePaginationPages = (is_numeric($tablePaginationPages) ? $tablePaginationPages:0 );
			//table columne sort onload
			$tableSortOnLoad = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_sortonload','s_sortable');
			$tableSortOnLoad = (is_numeric($tableSortOnLoad) ? $tableSortOnLoad:-1 );
			$tableSortOnLoadReverse = $this->pObj->pi_getFFvalue($this->pObj->cObj->data['pi_flexform'], 'tablesort_sortonload_reverse','s_sortable');

			if($tableSortable) {

            if(isset($conf['sortable.'])){
               $text1 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_hover_text_before.']);
              $text =  'var sortHoverTextbefore = "'.$text1.'";';
               $text2 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_hover_text_after.']);
              $text .=  '
              var sortHoverTextafter = "'.$text2.'";';

            }
            $GLOBALS['TSFE']->additionalHeaderData['tx_bmtablesort'] = '
            <script type="text/javascript">
               //<![CDATA[
               var prefix = "'.$prefix_mode.'";
               '.$text.'
               //]]>
            </script>
            <script type="text/javascript" src="typo3conf/ext/bm_tablesort/lib/tablesort.js"></script>';
				if($tableSortOnLoad >= 0) {
               $sorttableClass = ' sortable-onload-'.$tableSortOnLoad.($tableSortOnLoadReverse ? '-reverse':'');
				} else {
               $sorttableClass = ' sortable';
				}
            $sorttableClass .= ' no-arrow';

            //highlight current selected sort column by a class name
            if($tableHighlightCur) {
               $sorttableClass .= ' colstyle-'.$tableHighlightCur;
            }

				if($tablePaginationRows>0) {
               $text = '';
               if(isset($conf['sortable.'])){
                  $t1 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_first_page.']);
                  $t2 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_previous_page.']);
                  $t3 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_next_page.']);
                  $t4 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_last_page.']);
                  $t5 = $this->pObj->cObj->stdWrap('',$conf['sortable.']['lang_page_of_pages.']);
                 $text .=  '
                 var pageination_text = ["'.$t1.'","'.$t2.'","'.$t3.'","'.$t4.'","'.$t5.'"];';

               }

					$GLOBALS['TSFE']->additionalHeaderData['tx_bmtablesort'] .= '
					<script type="text/javascript" src="typo3conf/ext/bm_tablesort/lib/paginate.js"></script>
					<script type="text/javascript">
               //<![CDATA[
               '.$text.'
              /* The sortCompleteCallback does nothing but call the pagination object, passing in the table id */
               function sortCompleteCallback(tableId) {
                  tablePaginater.showPage(tableId);
               }
               //]]>
               </script>
					';
               $sorttableClass .= ' paginate-'.$tablePaginationRows;
					if($tablePaginationPages > 0) {
                  $sorttableClass .= ' max-pages-'.$tablePaginationPages;
					}
				}
			}  else {
				//no sort classes //unsort
            $sorttableClass .= '';
			}

         // zebra strip table through this class "alt"
         if($tableZebraStriping ) {
            if($tableZebraStriping != '') {
               $sorttableClass .= ' rowstyle-'.$conf['sortable.']['defaultClassNames.']['zebraStripRow'];
            }
          }
				// generate id prefix for accessible header
			$headerScope = ($headerPos=='top'?'col':'row');
			$headerIdPrefix = $headerScope.$this->pObj->cObj->data['uid'].'-';

				// Split into single lines (will become table-rows):
			$rows = t3lib_div::trimExplode(chr(10),$content);
			reset($rows);

				// Find number of columns to render:
			$cols = t3lib_div::intInRange($this->pObj->cObj->data['cols']?$this->pObj->cObj->data['cols']:count(explode($delimiter,current($rows))),0,100);

			$headerDebug = array();
				// Traverse rows (rendering the table here)
			$rCount = count($rows);
			foreach($rows as $k => $v)	{
				$cells = explode($delimiter,$v);
				$newCells=array();
				for($a=0;$a<$cols;$a++)	{
						// remove quotes if needed
					if ($quotedInput && substr($cells[$a],0,1) == $quotedInput && substr($cells[$a],-1,1) == $quotedInput)	{
						$cells[$a] = substr($cells[$a],1,-1);
					}

					if (!strcmp(trim($cells[$a]),''))	$cells[$a]='&nbsp;';

               //TYPO3 standart classes
               $t3cellAttribs = ($noStyles?'':($a>0 && ($cols-1)==$a) ? 'td-last td-'.$a : 'td-'.$a);
					//content pos
					if(count($tableContentPosArr) > 1 && isset($tableContentPosArr[$a]) && (preg_match('/(left)|(center)|(right)/',$tableContentPosArr[$a]))) {
						if($tableContentposArr[$a] == 'center'){
							$contentalign = ' ';
						} else {
							$contentalign = ' '.$tableContentPosArr[$a];
						}
					} else if (count($tableContentPosArr) == 1 && (preg_match('/(left)|(center)|(right)/',$tableContentPos))) {
						$contentalign = ' '.$tableContentPos;
					}


               $cellAttribs = ' class="'.($noStyles?'':$t3cellAttribs.(($k == 0 && $tableSortable)? $sorttype:'')).$contentalign.'"';
					//Header part
					if (($headerPos == 'top' && !$k) || ($headerPos == 'left' && !$a))	{

						//sort type
						//echo $tableHeaderTypesArr[$a];
						if(count($tableHeaderTypesArr) > 1 && isset($tableHeaderTypesArr[$a]) && (preg_match('/(none)|(text)|(numeric)|(date)|(date-dmy)/',$tableHeaderTypesArr[$a]))) {
							if($tableHeaderTypesArr[$a] == 'none'){
								$sorttype = ' none';
							} else {
								$sorttype = ' sortable-'.$tableHeaderTypesArr[$a];
							}
						} else if (count($tableHeaderTypesArr) == 1 && $tableSortable && (preg_match('/(none)|(text)|(numeric)|(date)|(date-dmy)/',$tableHeaderTypes))) {
							$sorttype = ' sortable-'.$tableHeaderTypes;
						} else if ($tableSortable) {
							$sorttype = ' sortable';
						}

						//header pos
						if(count($tableHeaderPosArr) > 1 && isset($tableHeaderPosArr[$a]) && (preg_match('/(left)|(center)|(right)/',$tableHeaderPosArr[$a]))) {
							if($tableHeaderposArr[$a] == 'center'){
								$headalign = ' ';
							} else {
								$headalign = ' '.$tableHeaderPosArr[$a];
							}
						} else if (count($tableHeaderPosArr) == 1 && (preg_match('/(left)|(center)|(right)/',$tableHeaderPos))) {
							$headalign = ' '.$tableHeaderPos;
						}

						$cellclass = (($k == 0 && $tableSortable)? $sorttype:'').$headalign;
                  $cellAttribs = ' class="'.$t3cellAttribs.$cellclass.$headalign.'"';
						$headerDebug[$a] = $cellclass;
						$scope = ' scope="'.$headerScope.'"';
						$scope .= ' id="'.$headerIdPrefix.(($headerScope=='col')?$a:$k).'"';

						//echo 'xxxx:'.$tableSortable;

						$newCells[$a] = '
							<th'.$cellAttribs.$scope.'>'.$this->pObj->cObj->stdWrap($cells[$a],$conf['innerStdWrap.']).'</th>';
					} else {
						if (empty($headerPos))	{
							$accessibleHeader = '';
						} else {
							$accessibleHeader = ' headers="'.$headerIdPrefix.(($headerScope=='col')?$a:$k).'"';
						}
						$newCells[$a] = '
							<td'.$cellAttribs.$accessibleHeader.'>'.$this->pObj->cObj->stdWrap($cells[$a],$conf['innerStdWrap.']).'</td>';
					}
				}
				if (!$noStyles)	{
               //TYPO3 standart classes
               $oddEven = $k%2 ? 'tr-odd' : 'tr-even';
					//$oddEven = $k%2 ? 'alter' : '';
               $rowAttribs =  ($k>0 && ($rCount-1)==$k) ? ' class="'.$oddEven.' tr-last"' : ' class="'.$oddEven.' tr-'.$k.'"';
            }
				$rows[$k]='
					<tr'.$rowAttribs.'>'.implode('',$newCells).'
					</tr>';
			}

			$addTbody = 0;
			$tableContents = '';
			if ($caption)	{
				$tableContents .= '
					<caption>'.$caption.'</caption>';
			}
			if ($headerPos == 'top' && $rows[0])	{
				$tableContents .= '<thead>'. $rows[0] .'
					</thead>';
				unset($rows[0]);
				$addTbody = 1;
			}
			if ($useTfoot)	{
				$tableContents .= '
					<tfoot>'.$rows[$rCount-1].'</tfoot>';
				unset($rows[$rCount-1]);
				$addTbody = 1;
			}
			$tmpTable = implode('',$rows);
			if ($addTbody)	{
				$tmpTable = '<tbody>'.$tmpTable.'</tbody>';
			}
			$tableContents .= $tmpTable;

				// Set header type:
			$type = intval($this->pObj->cObj->data['layout']);

				// Table tag params.
			$tableTagParams = $this->pObj->getTableAttributes($conf,$type);
         if (!$noStyles)  {
               //TYPO3 standart classes
              $tableTagParams['class'] = 'contenttable contenttable-'.$type.($tableClass?' '.$tableClass:'').$sorttableClass;
         } elseif ($tableClass || $sorttableClass) {
            $tableTagParams['class'] = ($tableClass?' '.$tableClass:'').$sorttableClass;
         }
         //Table id
			$tableTagParams['id'] = $headerIdPrefix.'tableid';


				// Compile table output:
			$out = '
            <table '.t3lib_div::implodeAttributes($tableTagParams).($summary?' summary="'.$summary.'"':'').'>'.   // Omitted xhtmlSafe argument TRUE - none of the values will be needed to be converted anyways, no need to spend processing time on that.
				$tableContents.'
				</table>';

				// Calling stdWrap:
			if ($conf['stdWrap.']) {
				$out = $this->pObj->cObj->stdWrap($out, $conf['stdWrap.']);
			}


			//debug mode
         if($conf['sortable.']['debugClassName'] == 1) {
				$b = '<br />';
            $debug_out = '<div class="bmtablesort_debug">';
				$debug_out .= '<h1>Unobtrusive Table Sort Script (revisited)</h1>';
            $debug_out .= '<h2>Table class: </h2><p>'.$sorttableClass.'</p>';
				$debug_out .= ' <h3>Header classes: </h3><p>';
				foreach ($headerDebug as $i=>$v) {
					$debug_out .= $i.'. '.$v.$b;
				}
				$debug_out .= '</p>';
            $debug_out .= '</div>';
            $out = $debug_out.$out;
			}

			// Return value
			return $out;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bm_tablesort/lib/class.tx_bmtablesort_cssstyledcontent_hook.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/bm_tablesort/lib/class.tx_bmtablesort_cssstyledcontent_hook.php']);
}

?>
