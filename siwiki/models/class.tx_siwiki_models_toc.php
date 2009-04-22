<?php
/**
 *
 * shows a toc for a whole namespace
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_toc.php 1082 2009-02-04 13:55:50Z alappe $
 *
 */
class tx_siwiki_models_toc extends tx_lib_object {

        /**
         * Get a fucking DBAL 
         * escape Strings to avoid mysql injections
         * @param mixed $input
         * @param String $table
         * @return String 
         */
        public function s($input,$table = 'cheese'){
                return $GLOBALS['TYPO3_DB']->fullQuoteStr($input,$table);
        }

        public function loadToc($pid, $namespace){
                $select = 'a.uid, a.title, ns.name, ns.uid as namespace';
                $from = 'tx_siwiki_articles as a, tx_siwiki_namespaces as ns';
                $where = 'a.pid = '.$this->s($pid).' AND a.namespace = '.$this->s($namespace).' AND a.namespace = ns.uid';
                $orderBy = 'title';
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where,$orderBy);
                if($query){
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)){
                                $entry = new tx_lib_object($row);
                                $this->append($entry);
                        }
                }
        }

}
?>
