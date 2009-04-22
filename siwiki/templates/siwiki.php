<?php if (!defined ('TYPO3_MODE')) 	die ('Access denied.'); ?>
<?php foreach($this as $entry) {
        $entry->printTitle($entry->get('title'),$entry->get('namespaceName'));
	$entry->printToolbar('display');
        $entry->printAsHtml('article');
}
?>
