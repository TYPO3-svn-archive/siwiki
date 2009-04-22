<?php
/**
 *
 * Versions of articles
 * 
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_articleVersion.php 1169 2009-03-26 10:15:18Z sisak $
 * @see tx_siwiki_models_article
 *
 */
tx_div::load('tx_siwiki_models_article');
class tx_siwiki_models_articleVersion extends tx_siwiki_models_article {

        public $comment;

        protected $table = 'tx_siwiki_articles';
        protected $tableForVersions = 'tx_siwiki_articles_versions';

        /**
         * Set the properties to those of the specified article in version $version
         *
         * @param int $uid
         * @param int $pid
         * @param int $version (optional)
         * @return bool
         */
        public function load($uid, $pid, $version) {
                $select = 'tstamp,title,namespace,editor,version,article,comment';
                $where = 'deleted = 0 AND hidden = 0 AND pid = '.$this->s($pid).' AND  article_uid = '.$this->s($uid).' AND version = '.$this->s($version);

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,'tx_siwiki_articles_versions',$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                $entry = new tx_lib_object($row);
                                $this->append($entry);
                        }
                        return true;
                }
                else {
                        throw new exception('Could not get article with uid = '.$uid.' and version = '.$version);
                }
        }


        /**
         * Save a version of an article by loading it from db and writing it into the versions table
         *
         * @param int $uid
         * @param int $pid
         * @param string $comment
         */
        public function save($uid, $pid, $comment) {
                $field_values = '*'; 
                $where = 'pid = '.$this->s($pid).' AND uid = '.$this->s($uid);
                $query =  $GLOBALS['TYPO3_DB']->exec_SELECTquery($field_values,$this->table,$where);
                if($query){
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)){
                                foreach($row as $key => $value){
                                        $insertArray[$key] = $value; 
                                }
                        }  
                } else {
                        throw new Exception ('Can not get article from database');
                }
                $insertArray['article_uid'] = $insertArray['uid'];
                $insertArray['uid'] = ''; 
                $insertArray['tstamp'] = time();
                $insertArray['comment'] = $comment;

                $status = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tableForVersions,$insertArray);

                if(! $status){
                        throw new Exception ('Can not write article version into database');
                }

        }

                



}
?>
