<?php if (!defined ('TYPO3_MODE')) 	die ('Access denied.'); ?>
<div>
<?php
$this->rewind();
if($this->valid()){
        $entry = $this->current();
        print "<h2>%%%tocBody%%% ".$entry->get('name')."</h2><ul>";
        //$this->rewind();
        foreach($this as $entry) {
                print "<li>";
                $link = tx_div::makeInstance('tx_lib_link');
                $link->designator($entry->getDesignator());
                $link->destination($entry->getDestination());
                $link->noHash();
                $link->label($entry->get('title'));
                $link->parameters(array('namespace' => $entry->get('namespace'),
                                        'uid' => $entry->get('uid'),
                                        'action' => 'display'));
                print $link->makeTag();
                print "</li>";
        }
}
?>
</ul></div>
