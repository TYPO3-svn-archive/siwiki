<?php
/**
 *
 * Model for articles written to cache 
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_articleCache.php 1201 2009-04-28 10:00:45Z sisak $
 * @see tx_siwiki_models_article
 *
 */

tx_div::load('tx_siwiki_models_article');
class tx_siwiki_models_articleCache extends tx_siwiki_models_article {

        protected $table = 'tx_siwiki_articles_cache';
        private $toc; 
        private $pid;
        private $uid;
        private $namespace;

        /**
         * Save the article
         *
         * @param int $uid
         * @param int $pid
         *
         */
        public function save($uid, $pid) {
                $this->pid = $pid; 
                $this->uid = $uid;
                $updateArray = Array();
                foreach($this->selectHashArray('title, article, version, namespace') as $key => $value) {
                        $updateArray[$key] = $value;
                }

                $this->namespace = $updateArray['namespace'];

                $updateArray['tstamp'] = time(); // $updateArray['crdate'] = time();
                $updateArray['version']++;
                $updateArray['editor'] = tx_siwiki_classes_misc::getUsername();

                // Render the article:
                $updateArray['article'] = $this->renderArticle($updateArray['article']);

                $where = 'pid = '.$this->s($this->pid).' AND uid  = '.$this->s($uid);

                $status = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->table, $where, $updateArray);
                if(!$status) {
                        throw new Exception ('Article Cache '.$uid.' could not been saved!');
                }
        }


        /**
         * Insert a new article
         *
         * @param int $uid We get this from the article insert before
         * @param int $pid
         */
        public function insert($uid, $pid) {
                $this->uid = $uid;  
                $this->pid = $pid;  
                foreach($this->selectHashArray('title, article, namespace') as $key => $value) {
                        $insertArray[$key] = $value;
                }
                $insertArray['tstamp'] = $insertArray['crdate'] = time();
                $insertArray['version'] = 1;
                $insertArray['creator'] = tx_siwiki_classes_misc::getUsername();
                $insertArray['uid'] = $this->uid;
                $insertArray['pid'] = $this->pid;

                // Render the article:
                $insertArray['article'] = $this->renderArticle($insertArray['article']);
                $status = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->table,$insertArray);

                if(! $status){
                        throw new Exception('could not insert entry for: cache uid'.$this->uid);
                }

                // Update reference
                $this->updateReferenceTableForNewEntry($this->uid,$insertArray['title'],$insertArray['namespace']);

                // Update Wiki Articles
                $this->updateWikiArticles($this->uid,$insertArray['title'],$insertArray['namespace']);
        }

        /**
         * Deletes an article
         * 
         * @param int $uid
         * @param int $pid
         * @param string $title
         * @param int $namespace
         *
         */
        public function delete($uid, $pid, $title, $namespace) {
               $this->pid = $pid;
               $where = 'pid = '.$this->s($pid).' AND uid = '.$this->s($uid);
               $status = $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->table,$where);

               if(! $status){
                        throw new Exception('could not delete cached article: '.$uid);
               }
               
               $this->updateWikiArticles($uid,$title,$namespace);
               $this->updateReferenceTableForDeletedEntry($uid);
        }


        /**
         * Render the article
         *
         * @param string $article
         * @param bool $updateReferenceTable
         * @return string
         */
        private function renderArticle($article,$updateReferenceTable=true) {
                $article = $this->renderWikiLinks($article,$updateReferenceTable);
                $article = $this->renderTableOfContents($article);
                return $article;
        }

        /**
         * Replace the TOC Label with the actual TOC
         *
         * @param string $article
         * @return string
         */
        private function renderTableOfContents($article) {
                $hTags  = array();
                preg_match_all("/<h\d>.*?<\/h\d>/",$article,$hTags,PREG_SET_ORDER);

                $linksForHeaders = $this->createTableOfContents($hTags);
                // Create links besides headers
                foreach($linksForHeaders as $key=>$val) {
                        $article = str_replace($key, $val, $article);
                }
                $article = preg_replace("/###TOC###/", $this->toc, $article);
        
                return $article;
        }

        /**
         * Create the Table Of Contents
         *
         * @param array $hTags
         * @return string
         */
        private function createTableOfContents($hTags) {
                $toc = '';
                $currentLevel = 0;
                $levelCounter = 1;
                $linksForHeaders=array();

                foreach($hTags as $hArray) {
                        $hTag = $hArray[0];
                        $level = preg_replace("/.*<h(\d)>.*/", "\\1", $hTag)-1;
                        $text = preg_replace("/<h\d>(.*?)<\/h\d>/", "\\1", $hTag);
                        $hashOfText = substr(md5($text),0,8);
                        $linkClassName = tx_div::makeInstanceClassName('tx_lib_link');
                        $link = new $linkClassName();
                        $link->designator($this->controller->getDesignator());
                        $link->destination($this->controller->getDestination());
                        $link->anchor($hashOfText);
                        $link->classAttribute('siwiki-article-link');
                        $link->parameters(array('namespace' => $this->namespace, 'uid' => $this->uid));
                        $link->label($text);
                        $link->noHash();
                        $url = $link->makeTag();
                        if($level > $currentLevel){
                                $levelCounter++;
                                $currentLevel = $level;
                                $toc .= '<ol>';
                        } 
                        if($level < $currentLevel){
                                $currentLevel = $level;
                                $toc .= '</ol>';
                        }
                        if($level == $currentLevel) {
                                $toc .= '<li class="siwiki-article-toc-h'.$level.'">'.$url.'</li>'.chr(10);
                        } 
                        $linksForHeaders[$hTag] = '<a name="'.$hashOfText.'">'.$hTag.'</a>';
                }
                for($x=0;$x<$levelCounter;$x++){
                        $toc .= '</ol>';
                }
                $this->toc = '<div class="siwiki-article-toc">'.$toc.'</div>';
                return $linksForHeaders;
        }

        /**
         * Render wikiLinks
         * @param string $article
         * @param bool updateReferenceTable?
         * @return string
         */
        private function renderWikiLinks($article, $updateReferenceTable) {
                $aTags = array();
                preg_match_all("/<a.+?href=[\"'].+?[\"'].*?>.*?<\/a>/",$article,$aTags,PREG_SET_ORDER);

                
                $linkArray = $this->createLinkArray($aTags, $updateReferenceTable);
                foreach($linkArray as $key=>$val) {
                        $article = str_replace($key, $val, $article);
                }
		return $article;
	}

        /**
         * Creates valid links out of pseudo wiki-links (wiki://)
         *
         * @param array $matches
         * @param bool $updateReferenceTable
         * @return array 
         */
        private function createLinkArray($matches, $updateReferenceTable) {
                $links = array();
                $insertArray = array();
                foreach($matches as $tagArray) {
                        $tag = $tagArray[0];
                        if(preg_match("/href=\"wiki:\/\/.[^\"]*\"/", $tag)) {
                                preg_match_all("/.*style=\"(.[^\"]*)\".*/", $tag, $style);
                                $title = urldecode(preg_replace("/.*href=\"wiki:\/\/(.[^@]*)@.*\".*/", "\\1", $tag));
                                $namespace = urldecode(preg_replace("/.*href=\"wiki:\/\/.[^@]*@(.[^\"]*)\".*/", "\\1", $tag));
                                $namespace = str_replace("/","",$namespace); 
                                $linked_namespace = tx_siwiki_models_namespace::getNamespaceByName($namespace, $this->pid);
                                $linked_uid = $this->getUidByArticleTitle($title, $this->pid, $linked_namespace);
                                preg_match_all("/(?<=^|>)[^><]+?(?=<|$)/",$tag,$linktext);
                                //$linktext = preg_replace("/.*>(.*?)<\/a>/", "\\1", $tag);
                                $linktext = implode(" ",$linktext[0]);

                                $linkClassName = tx_div::makeInstanceClassName('tx_lib_link');
                                $link = new $linkClassName();
                                $link->destination($this->controller->getDestination());
                                $link->designator($this->controller->getDesignator());
                                if(! empty($style)) $link->attributes(array('style' => $style[1][0]));
                                $link->noHash();
                                $link->label($linktext);
                                $link->title($title.'@'.$namespace);

                                if($linked_uid) {
                                        $link->classAttribute('siwiki-article-link');
                                        $link->parameters(array('namespace' => $linked_namespace, 'uid' => $linked_uid));
                                        $url = $link->makeTag();
                                } else {
                                        //$link->classAttribute('siwiki-article-link');
                                        $link->classAttribute('siwiki-article-futurelink');
                                        $link->parameters(array( 'namespace' => $linked_namespace, 'title' => $title,'action' => 'new'));
                                        $url = $link->makeTag();
                                }

                                if($updateReferenceTable) $metaInsertArray[] = array('linked_uid' => $linked_uid, 'linked_title' => $title, 'linked_namespace' => $linked_namespace);

                                // Add old and new version to array to keep track of them
                                $links[$tag] = $url;
                        }
                }
                // Update reference table
                if($updateReferenceTable && count($metaInsertArray)) $this->updateReferenceTable($metaInsertArray);

                return $links;
        }

        /** 
         * Update the reference table
         *
         * @param array $insertArray
         */
        private function updateReferenceTable($insertArray) {

                $linking_uid = $this->uid; 

                $table = 'tx_siwiki_articles_references';
                $where = 'linking_uid = "'.$this->s($linking_uid);
                $GLOBALS['TYPO3_DB']->exec_DELETEquery($table,$where);

                foreach($insertArray as $row) {
                        $row['linking_uid'] = $linking_uid;
                        $row['tstamp'] = time();
                        $GLOBALS['TYPO3_DB']->exec_INSERTquery($table,$row);
                }

        }


        /**
         * Update Articles
         *
         * Updates all articles linking to $uid because it has changed (deleted, inserted or something else)
         *
         * @param int $uid
         * @param string $title
         * @param string $namespace
         */
        private function updateWikiArticles($uid, $title, $namespace) {
                // Select all linking articles
                $select = 'a.uid,a.article';
                $from = 'tx_siwiki_articles as a, tx_siwiki_articles_references as r';
                $where = 'a.pid = '.$this->s($this->pid).' AND r.linking_uid = a.uid AND a.hidden = 0 AND a.deleted = 0 AND r.linked_uid = '.$this->s($uid);
                $insertArray['creator'] = tx_siwiki_classes_misc::getUsername();

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);

                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                $insertArray['article'] = $this->renderArticle($row['article'],false);
                                $this->saveUpdate($row['uid'], 'tx_siwiki_articles_cache', $insertArray);
                        }
                }
        }

        /**
         * Save the newly rendered article
         * @see updateWikiArticles
         * @param int $uid
         * @param string $table
         * @param array $insertArray
         */
        private function saveUpdate($uid,$table,$insertArray) {
                $where = 'pid = '.$this->s($this->pid).' AND uid = '.$this->s($uid);
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,$where,$insertArray);
        }

        /**
         * Updates the linked_uid in every entry in reference table because it has been
         * inserted now, so a UID is known
         * 
         * @param int $uid
         * @param string $title
         * @param string $namespace
         */
        private function updateReferenceTableForNewEntry($uid,$title,$namespace) {
                // Update reference table
                $fields = array('linked_uid' => $uid);
                $table = 'tx_siwiki_articles_references';
                $where = 'linked_title = '.$this->s($title).' AND linked_namespace = '.$this->s($namespace);
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,$where,$fields);
        }


        /**
         * Updates the linked_uid in every entry in reference table because it has
         * been deleted and is 0 now
         *
         * @param int $uid
         */
        private function updateReferenceTableForDeletedEntry($uid) {
                // Update reference table
                $table = 'tx_siwiki_articles_references';
                $where = 'linking_uid = '.$this->s($uid);
                $GLOBALS['TYPO3_DB']->exec_DELETEquery($table,$where);

                $fields = array('linked_uid' => 0);
                $where = 'linked_uid = '.$this->s($uid);
                $query = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,$where,$fields);
        }


}

?>
