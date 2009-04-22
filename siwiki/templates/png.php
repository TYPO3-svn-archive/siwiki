<?php if (!defined ('TYPO3_MODE')) 	die ('Access denied.'); ?>
<?php
        foreach($this as $entry) {
                print base64_decode($entry->get('plotRelations'));
        }
?>
