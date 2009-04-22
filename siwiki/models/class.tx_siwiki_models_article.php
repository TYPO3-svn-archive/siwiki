<?php
/**
 *
 * The main article class... one of the bigger ones in this wiki
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_article.php 1190 2009-04-20 15:01:13Z sisak $
 *
 */
class tx_siwiki_models_article extends tx_lib_object {
        private $namespaceTable = 'tx_siwiki_namespaces';
        protected $table = 'tx_siwiki_articles';

        /**
         * Get a fucking DBAL 
         * escape Strings to avoid mysql injections
         * @param mixed $input
         * @param String $table
         * @return String 
         */
        public function s($input,$table = 'cheese'){
                return $GLOBALS['TYPO3_DB']->fullQuoteStr($input,$table);
        }

        /**
         * Set the properties to those of the specified article
         *
         * @param int $uid
         * @param int $pid
         */
        public function load($uid, $pid) {
                $select = 'a.uid, a.tstamp, a.crdate, a.title, a.namespace, a.article, md5(a.article) as articleHash, a.creator, a.editor, a.version, ns.name as namespaceName';
                $where = 'a.deleted = 0 AND a.hidden = 0 AND a.pid = '.$this->s($pid).' AND a.uid = '.$this->s($uid).' AND a.namespace = ns.uid';
                $from = $this->table.' as a, '.$this->namespaceTable.' as ns';
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);

                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                $entry = new tx_lib_object($row);
                        }
                        if($entry){
                                $this->append($entry);
                        } else {
                                throw new exception('Could not get article! <br /><br />Please consider to have at least one namespace and one article item in place.<br />Keep in mind that this items should be equal to your typoscript values for defaultNamespace and rootpage.');
                        }
                }
                else {
                        throw new exception('Could not get article with uid = '.$uid);
                }
        }


        /**
         * Updates an existing article
         *
         * @param int $uid
         * @param int $pid
         */
        public function save($uid, $pid) {
                $updateArray = Array();
                foreach($this->selectHashArray('title, article, version, namespace') as $key => $value) {
                        $updateArray[$key] = $value;
                }
                $updateArray['tstamp'] = time(); // $updateArray['crdate'] = time();
                $updateArray['version']++;
                $updateArray['editor'] = tx_siwiki_classes_misc::getUsername();
                $where = 'pid = '.$this->s($pid).' AND uid  = '.$this->s($uid);


                $status = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->table, $where, $updateArray);
                if(!$status) {
                        throw new Exception ('Article '.$uid.' could not been saved!');
                }
        }

        /**
         * Inserts a new article
         * 
         * @param int $pid
         * @return int
         */
        public function insert($pid) {
                $this->pid = $pid;
                foreach($this->selectHashArray('title, article, namespace') as $key => $value) {
                        $insertArray[$key] = $value;
                }
                $insertArray['tstamp'] = $insertArray['crdate'] = time();
                $insertArray['version'] = 1;
                $insertArray['creator'] = tx_siwiki_classes_misc::getUsername();
                $insertArray['pid'] = $this->pid;

                $query = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->table,$insertArray);
                $this->uid = $GLOBALS['TYPO3_DB']->sql_insert_id(); 
                if(! $query){
                        throw new Exception('could not insert entry for: article uid'.$this->uid);
                }
                return $this->uid;
        }

        /**
         * Creates a new article, appending title and whatnot to the model
         */
        public function createNewArticle() {
                $params['title'] = $this->controller->parameters->get('title');
                $params['namespace'] = $this->controller->parameters->get('namespace');
                $params['uid'] = $this->controller->parameters->get('uid');
                $params['namespaceName'] = tx_siwiki_models_namespace::getNamespaceByUid($this->controller->parameters->get('namespace'),$this->controller->configurations->get('storageFolder'));
                $entry = new tx_lib_object($params);
                $this->append($entry);
        }

        /**
         * Deletes an article
         * 
         * @param int $uid
         * @param int $pid 
         */
        public function delete($uid, $pid) {
               $where = 'pid = '.$this->s($pid).' AND uid  = '.$this->s($uid);
               $status = $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->table,$where);

               if(! $status){
                        throw new Exception('could not delete article: '.$uid);
               }
        }

        /**
         * Generic getter
         * @param string $key
         * @return mixed
         */
        public function __get($key) {

                return $this->$key;
        }

        /**
         * Generic setter
         * @param string $key
         * @param mixed $val
         */
        public function __set($key,$val) {

                $this->$key = $val;
                //$this->fb->log(__CLASS__.'->'.__METHOD__);

        }

        /**
         * Gets the uid of an article by title (and namespace and pid)
         *
         * @param string $string
         * @param int $pid
         * @param string $ns
         * @return int
         */
        public static function getUidByArticleTitle($title,$pid,$ns) {
                $select = 'uid';
                $where = 'deleted = 0 AND hidden = 0 AND pid = '.self::s($pid).' AND title = '.self::s($title).' AND namespace = '.self::s($ns);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, 'tx_siwiki_articles',$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                return (int) $row['uid'];
                        }
                } else {
                        return false;
                }
        }





}

?>
