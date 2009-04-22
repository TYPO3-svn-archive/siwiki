<?php if (!defined('TYPO3_MODE')) die('Access denied.'); ?>
<?php
foreach($this as $entry) { ?>
        <?php //print json_encode($entry->get('response'));
         print json_encode($entry->get('response'))?>
<?php } /* foreach */ ?>
