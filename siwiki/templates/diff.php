<?php if (!defined('TYPO3_MODE')) die('Access denied.'); ?>

<?php $this->printToolbar('diff',true); ?>

<table class="diff">

<?php foreach($this as $entry) {  
        print '<tr><th colspan=2><strong>'.$entry->get('oldTitle').'</strong> -  %%%version%%% <strong>'.$entry->get('oldVersion').'</strong>
                <br />'.date('Y-m-d H:i:s',$entry->get('oldTime')).' %%%editor%%%'.$entry->get('oldEditor').'</th>';

        print '<th colspan=2><strong>'.$entry->get('newTitle').'</strong> -  %%%version%%% <strong>'.$entry->get('newVersion').'</strong>
                <br />'.date('Y-m-d H:i:s',$entry->get('newTime')).' %%%editor%%%'.$entry->get('newEditor').'</th></tr>';
        if($entry->get('diff')!= ''){
                print $entry->get('diff');
        } else {
                print '<tr><td colspan=4>%%%sameVersions%%%</td></tr>';
        }
        print '</table>';
        $this->printFormTag('siwiki_diff');
?>
<textarea name="siwiki[article]" style="display:none"><?php print $entry->get('oldArticle'); ?></textarea>
<input type="hidden" name="siwiki[title]" value="<?php print $entry->get('oldTitle') ?>" />
<input type="hidden" name="siwiki[version]" value="<?php $entry->printAsInteger('newVersion'); ?>" />
<input type="hidden" name="siwiki[uid]" value="<?php print $entry->controller->parameters->get('uid'); ?>" />
<input type="hidden" name="siwiki[namespace]" value="<?php print $entry->controller->parameters->get('namespace') ?>" />
<?php } ?>
</form>

