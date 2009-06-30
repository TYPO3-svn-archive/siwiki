<?php
/**
 *
 * The diff class, checking for differences between two articles
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id$
 *
 */
require_once(t3lib_extMgm::extPath('siwiki').'resources/DifferenceEngine.php');

class tx_siwiki_models_diff extends tx_lib_object {

        private $oldArticle;
        private $oldArticleArray = Array();
        private $newArticle;
        private $newArticleArray = Array();

        public function createDiff(tx_siwiki_models_article $newArticle, tx_siwiki_models_articleVersion $oldArticle){

                foreach($newArticle as $entry) {
                        $diff["newTitle"] = $entry->get('title');
                        $diff["newNamespaceName"] = $entry->get('namespaceName');
                        $diff["newVersion"] = $entry->get('version');
                        $diff["newTime"] = $entry->get('tstamp');
                        $diff["newEditor"] = $entry->get('editor');
                        
                        $this->newArticle = $entry->get('article');
                        $this->newArticleArray = $this->parseArticle($this->newArticle);
                }

                foreach($oldArticle as $entry) {
                        $diff["oldTitle"] = $entry->get('title');
                        $diff["oldVersion"] = $entry->get('version');
                        $diff["oldTime"] = $entry->get('tstamp');
                        $diff["oldEditor"] = $entry->get('editor');
                        $diff["oldArticle"] = $entry->get('article');

                        $this->oldArticle = $entry->get('article');
                        $this->oldArticleArray = $this->parseArticle($this->oldArticle);
                }
                $this->differenceEngine = new tx_siwiki_diff($this->oldArticleArray,$this->newArticleArray);


                $showResult = new tx_siwiki_tableDiffFormatter();
                $diff["diff"] = $showResult->format($this->differenceEngine);
                $entry = new tx_lib_object($diff);
                $this->append($entry);
        }

        public function parseArticle($article){
                $breaks = Array('<br />',
                                '</h1>',
                                '</h2>',
                                '</h3>',
                                '</h4>',
                                '</p>',
                                '</li>');
                foreach($breaks as $key){
                        $article = str_replace($key,'\n',$article);
                }
                $search = '/<img .+\/>/';
                $article = preg_replace_callback($search,
                        create_function('$hit',
                        'preg_match("/(?<=[\/])([\w\.\-]+)(?=[\'\"])/i",$hit[0],$match);return "[IMG $match[0]]";'),
                        $article);
                $article = strip_tags($article);
                $article = explode('\n',$article);
                return $article;
        }
}
?>
