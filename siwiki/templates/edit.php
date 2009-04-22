<?php if (!defined ('TYPO3_MODE')) 	die ('Access denied.'); ?>
<?php 
foreach($this as $entry) {
	$this->printFormTag('siwiki_edit');
	$this->printToolbar('edit',true);
	$this->printAsYuiRte($entry->get('title'),$entry->get('namespaceName'),$entry->get('article'));
        $this->printBottomToolbar('save');
?>
<input type="hidden" name="siwiki[title]" value="<?php print $entry->get('title') ?>" />
<input type="hidden" name="siwiki[version]" value="<?php $entry->printAsInteger('version'); ?>" />
<input type="hidden" name="siwiki[uid]" value="<?php $entry->printAsInteger('uid'); ?>" />
<input type="hidden" name="siwiki[namespace]" value="<?php print $entry->get('namespace') ?>" />
<input type="hidden" name="siwiki[articleHash]" value="<?php print md5($entry->get('article')) ?>" />
</form>

<?php } ?>
