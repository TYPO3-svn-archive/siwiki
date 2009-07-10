<?php if (!defined('TYPO3_MODE')) die('Access denied.'); ?>
<?php
foreach($this as $entry) { 
        if( count($entry->get('response'))){
                print t3lib_div::array2json($entry->get('response'));
                //   print json_encode($entry->get('response'))
         }
} 
?>
