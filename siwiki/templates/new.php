<?php if (!defined ('TYPO3_MODE')) 	die ('Access denied.'); ?>
<?php 
        foreach($this as $entry) {
	        $this->printFormTag('siwiki_insert');
                $this->printToolbar('new',true);
	        $this->printAsYuiRte($entry->get('title'),$entry->get('namespaceName'),'');
                $this->printBottomToolbar('insert');
?>
<div class="siwiki-menuitemsbottom">
<input type="hidden" name="siwiki[title]" value="<?php print $this->controller->parameters['title']; ?>" />
<input type="hidden" name="siwiki[namespace]" value="<?php print $this->controller->parameters['namespace']; ?>" />
</div>
</form>
<?php } /* foreach */ ?>
