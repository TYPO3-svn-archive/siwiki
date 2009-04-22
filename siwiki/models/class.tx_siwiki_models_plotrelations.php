<?php
/**
 *
 * Plots relations of articles
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_plotrelations.php 1161 2009-02-13 14:44:20Z sisak $
 *
 */
class tx_siwiki_models_plotrelations extends tx_lib_object {

        private $filename;
        private $fileresource;
        private $namespaces = array();
        private $namespace = array();
        private $png;


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
         * Create a temporary file 
         */
        private function setTempFile() {
                $this->filename = tempnam('/tmp/', 'siwiki_relplot_');
                $this->fileresource = fopen($this->filename, 'a+');
        }

        /**
         * Append string to temporary file
         *
         * @param string $string
         */
        private function appendToFile($string) {
                fwrite($this->fileresource, $string.chr(10));
        }

        /**
         * Main function...
         *
         * Plots the actual file by getting all different stuff and appending it to file
         *
         * @param int $pid
         */
        public function plot($pid,$namespace='') {
                $this->setTempFile();

                // Config
                $fontName = ($this->controller->configurations->get('graphvizFontName'))? 'fontname="'.$this->controller->configurations->get('graphvizFontName').'"' : '';
                $fontSize = ($this->controller->configurations->get('graphvizFontSize'))? 'fontsize="'.$this->controller->configurations->get('graphvizFontSize').'"' : '';
                        

                $this->appendToFile('digraph siwiki_relations {');
                $this->appendToFile('graph [label="siwiki relations\ngenerated '.date('r').'" '.$fontName.' '.$fontSize.' ];');
                $this->appendToFile('node [ '.$fontName.' '.$fontSize.' ];');

                // Admin mode... display the whole wiki... this can be huge!
                if(empty($namespace)) {
                        $this->getNamespaces($pid);

                        foreach($this->namespaces as $namespace) {
                                $this->appendToFile('subgraph cluster'.$namespace['uid'].' { '.chr(10).'label = '.$this->quote($namespace['name']).';');
                                $this->getAllArticles($pid,$namespace['uid']);
                                $this->getAllFutureArticles($pid,$namespace['uid']);

                                $this->appendToFile('} // end cluster'.$namespace['uid']);

                                $this->getAllReferencesToArticles($pid);
                                $this->getAllReferencesToFutureArticles($pid);

                        }

                }

                // Standard Display Mode in panel... display only namespace informations!
                else {
                        $this->getNamespace($namespace);
                        $this->appendToFile('subgraph cluster'.$this->namespace['uid'].' { '.chr(10).'label = '.$this->quote($this->namespace['name']).';');
                        $this->getAllArticles($pid,$this->namespace['uid']);
                        $this->getAllFutureArticles($pid,$this->namespace['uid']);

                        $this->appendToFile('} // end cluster'.$this->namespace['uid']);

                        $this->getAllReferencesToArticlesInThisNamespace($pid,$this->namespace['uid']);
                        $this->getAllReferencesToFutureArticlesInThisNamespace($pid,$this->namespace['uid']);
                }


                $this->appendToFile('} // end digraph ');

                $this->plotWithGraphviz();

                $entry = new tx_lib_object(array('plotRelations' => $this->png));
                $this->append($entry);

                // Delete the temporary file
                unlink($this->filename);

        }

        /**
         * Get an array of all namespaces
         *
         * @param int $pid
         */
        private function getNamespaces($pid) {
                $select = 'uid, name';
                $from = 'tx_siwiki_namespaces';
                $where = 'pid = '.$this->s($pid);

                // $this->namespaces = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$this);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->namespaces[] = $row;
                }
        }

        /**
         * Get an array of one specific namespace
         *
         * @param int $uid
         */
        private function getNamespace($uid) {
                $select = 'uid,name';
                $from = 'tx_siwiki_namespaces';
                $where = 'uid = '.$this->s($uid);

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                if($query){
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                $this->namespace = $row;
                        }
                }
        }

        /**
         * Get all articles in $pid and $namespace
         *
         * @param int $pid
         * @param int $namespace
         */
        private function getAllArticles($pid,$namespace) {
                $this->appendToFile('// All articles:');
                $select = 'uid, title, creator, editor, version';
                $from = 'tx_siwiki_articles';
                $where = 'deleted = 0 AND hidden = 0 AND namespace = '.$this->s($namespace).' AND pid = '.$this->s($pid);

                //$results = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->appendToFile($this->quote($result['uid']).' [label="{<title>'.$result['title'].'|<creator>'.$result['creator'].'|<editor>'.$result['editor'].'|<version>'.$result['version'].'}", shape="Mrecord", color="green"];');
                }

        }

        /**
         * Get all future articles
         *
         * @param int $pid
         * @param int $namespace
         */
        private function getAllFutureArticles($pid, $namespace) {
                $this->appendToFile('// All future articles: ');
                $select = 'linked_title';
                $from = 'tx_siwiki_articles_references';
                $where = 'linked_uid = 0 AND linked_namespace = '.$this->s($namespace);

                //$results = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->appendToFile($this->quote($result['linked_title']).' [shape="box", color="red"];');
                }
        }

        /**
         * Get all references to existing articles
         *
         * @param int $pid
         */
        private function getAllReferencesToArticles($pid) {
                $this->appendToFile('// All references to existing articles');
                $select = 'linking_uid,linked_uid';
                $from = 'tx_siwiki_articles_references';
                $where = 'linked_uid != 0 AND linking_uid != 0';

                //$results = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->appendToFile($this->quote($result['linking_uid']).' -> '.$this->quote($result['linked_uid']).' [color="green"];');
                }
        }

        /**
         * Get all references to articles in this namespace
         *
         * @param int $pid
         * @param int $namespace
         */
        private function getAllReferencesToArticlesInThisNamespace($pid,$namespace) {
                $this->appendToFile('// All references to existing articles in this namespace');
                $select = 'r.linking_uid,r.linked_uid,a.title';
                $from = 'tx_siwiki_articles as a, tx_siwiki_articles_references as r';
                $where = 'linked_uid != 0 AND linking_uid != 0 AND linked_namespace = '.$this->s($namespace).' AND a.uid = r.linking_uid';

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->appendToFile($this->quote($result['title']).' -> '.$this->quote($result['linked_uid']).' [color="green"];');
                }
        }

        /**
         * Get all references to future articles in this namespace
         *
         * @param int $pid
         * @param int $namespace
         */
        private function getAllReferencesToFutureArticlesInThisNamespace($pid,$namespace) {
                $this->appendToFile('// All references to future articles in this namespace');
                $select = 'r.linking_uid,r.linked_uid,r.linked_title,a.title';
                $from = 'tx_siwiki_articles_references as r, tx_siwiki_articles as a';
                $where = 'linked_uid = 0 AND r.linking_uid = a.uid AND r.linked_namespace = '.$this->s($namespace);

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->appendToFile($this->quote($result['title']).' -> '.$this->quote($result['linked_title']).' [color="red"];');
                }
        }

        /**
         * Get all references to future articles
         *
         * @param int $pid
         */
        private function getAllReferencesToFutureArticles($pid) {
                $this->appendToFile('// All references to future articles');
                $select = 'linking_uid,linked_uid,linked_title';
                $from = 'tx_siwiki_articles_references';
                $where = 'linked_uid = 0';
                //$results = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                        $this->appendToFile($this->quote($result['linking_uid']).' -> '.$this->quote($result['linked_title']).' [color="red"];');
                }
        }

        /**
         * Plot with command line graphviz
         *
         */
        private function plotWithGraphviz() {
                $graphvizPath = $this->controller->configurations->get('graphvizPath');
                $graphvizBinary = $this->controller->configurations->get('graphvizBinary');
                if(! empty($graphvizPath) || empty($graphvizBinary)) {
                        $output = array();
                        ob_start();
                        passthru($graphvizPath.$graphvizBinary.' '.$this->filename.' -Tpng ', $returnCode);
                        if($returnCode) {
                                throw new Exception('Couldn\'t process relations file!');
                        }
                        else {
                                $this->png = base64_encode(ob_get_contents());
                        }
                        ob_end_clean();
                }
        }

        /**
         * Little helper
         *
         * @param string $string
         * @return string
         */
        private function quote($string) {
                return '"'.$string.'"';
        }


}
?>
