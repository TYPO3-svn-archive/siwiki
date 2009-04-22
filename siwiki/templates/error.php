<?php if (!defined ('TYPO3_MODE')) 	die ('Access denied.'); ?>
<div style="background-color: red; color: white; padding: 1em;">
<?php foreach($this as $entry) { ?>
        <span style="padding: 1em;"><?php print $entry->get('siwikiException'); ?></span>
<?php  } ?>
</div>
