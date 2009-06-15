<?php
/**
 *
 * The model used for ajax requests
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_ajax.php 1210 2009-06-10 09:51:24Z sisak $
 *
 */ 

class tx_siwiki_models_ajax extends tx_lib_object {

        /**
         * Get a fucking DBAL -> cheese
         * escape Strings to avoid mysql injections
         * @param mixed $input
         * @param String $table
         * @return String 
         */
        public function s($input,$table = 'cheese'){
                return $GLOBALS['TYPO3_DB']->fullQuoteStr($input,$table);
        }


        /**
         * Return the response to the request by appending it to model
         * 
         * @param string $request
         * @param int $pid
         */

        function ajaxRequest($request,$pid) {
                switch($request) {
                case 'getNamespaces':
                        $ns = tx_siwiki_models_namespace::getAllNamespaces($pid);
                        $entry = new tx_lib_object(array('response' => $ns));
                        $this->append($entry);
                        break;
                case 'getAllFilesByArticle':
                        $files = tx_siwiki_models_files::getAllFilesByArticle($pid, $this->controller->parameters->get('uid'));
                        $entry = new tx_lib_object(array('response' => $files));
                        $this->append($entry);
                        break;
                case 'imageUpload':
                        $file = tx_siwiki_models_upload::uploadImage($this->controller->configurations->get('uploadedImageMaxWidth'));
                        $entry = new tx_lib_object(array('response' => $file));
                        $this->append($entry);
                        break;
                case 'fileUpload':
                        $file = tx_siwiki_models_upload::uploadFile($this->controller->parameters,$pid);
                        $entry = new tx_lib_object(array('response' => $file));
                        $this->append($entry);
                        break;
                case 'autosave':
                        // Future 
                        // ...
                        // We assume the md5 has been checked and there are differences
                        
                        $insert['tstamp'] = time();
                        $insert['article'] = $this->controller->parameters->get('article');
                        $insert['user'] = tx_siwiki_classes_misc::getUsername();

                        $entry = new tx_lib_object(array('response' => $GLOBALS['TYPO3_DB']->INSERTquery('tx_siwiki_elevator',$insert)));
                        $this->append($entry);

                        $status = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_siwiki_elevator',$insert);
                        break;
                case 'ping':
                        $entry = new tx_lib_object(array('response' => 'pong'));
                        $this->append($entry);
                        break;
                case 'getSignature':
                        $sig = '<a href="mailto:'.tx_siwiki_classes_misc::getEmail().'">'.tx_siwiki_classes_misc::getFirstname().' '.tx_siwiki_classes_misc::getLastname().' ('.strftime('%d.%m.%Y %H:%M').')</a>';
                        $entry = new tx_lib_object(array('response' => $sig));
                        $this->append($entry);
                        break;

                case 'updateLocking':
                        tx_siwiki_models_locking::updateLocking($this->controller->parameters->get('uid'));
                        break;

                case 'getInfo':
                        $select = 's.crdate as "%%%crdate%%%",s.tstamp as "%%%tstamp%%%",s.version,c.name as cname,e.name as ename';
                        $from = 'tx_siwiki_articles as s, fe_users as c, fe_users as e';
                        $where = 's.uid = '.$this->s($this->controller->parameters->get('uid')).' AND c.username = creator AND e.username = editor';

                        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                        if($query) {
                                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                        $row['%%%tstamp%%%'] = strftime($this->controller->configurations->get('articleInfoDateString'),$row['%%%tstamp%%%']);
                                        $row['%%%crdate%%%'] = strftime($this->controller->configurations->get('articleInfoDateString'),$row['%%%crdate%%%']);
                                        $row['%%%editor%%%'] = $row['ename'];
                                        $row['%%%creator%%%'] = $row['cname'];
                                        unset($row['ename'],$row['cname']);
                                        $entry = new tx_lib_object(array('response' => $row));
                                        $this->append($entry);
                                }
                        }
                        break;

                case 'getNotification':
                        $select = 'article_uid, user_uid';
                        $from = 'tx_siwiki_notifications';
                        $where = 'article_uid = '.$this->s($this->controller->parameters->get('uid')).' 
                                AND user_uid =\''.(int) tx_siwiki_classes_misc::getUserId().'\'
                                AND pid = \''.$pid.'\'';

                        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                        if($query) {
                                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                        $entry = new tx_lib_object(array('response' => $row));
                                }
                        }
                        $this->append($entry);
                        break;

                case 'setNotification':
                        if($this->controller->parameters->get('mode') == 'false'){
                                $insert['pid'] = (int) $pid;
                                $insert['tstamp'] = time();
                                $insert['article_uid'] = $this->controller->parameters->get('uid');
                                $insert['user_uid'] = tx_siwiki_classes_misc::getUserId();

                                $status = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_siwiki_notifications',$insert);

                                $entry = new tx_lib_object(array('response' => $status));
                        } else if($this->controller->parameters->get('mode') == 'true'){
                                $where = 'article_uid=\''.$this->controller->parameters->get('uid').'\' 
                                        AND user_uid=\''.tx_siwiki_classes_misc::getUserId().'\'
                                        AND pid=\''.$pid.'\'';
                                $status = $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_siwiki_notifications',$where);

                                $entry = new tx_lib_object(array('response' => $status));
                        }
                        $this->append($entry);
                        break;

                case 'getSearchResult':
                        $select = 'CONCAT(a.title,"@",ns.name) as title, a.uid, a.namespace';
                        $from = 'tx_siwiki_articles as a, tx_siwiki_namespaces as ns';
                        $where = 'a.title like '.$this->s($this->controller->parameters->get('query').'%',$from).' 
                                        AND a.namespace = ns.uid 
                                        AND a.pid = '.$this->s($pid).' 
                                        AND ns.pid = '.$this->s($pid);

                        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);

                        $json = Array();
                        if($query) {
                                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                        $json['results'][] = $row;
                                }
                        }
                        $entry = new tx_lib_object(array('response' => $json));
                        $this->append($entry);
                        break;

                case 'getFullTextSearchResult':
                        $select = 'CONCAT(a.title,"@",ns.name) as title, a.uid, a.namespace';
                        $from = 'tx_siwiki_articles as a, tx_siwiki_namespaces as ns';
                        $where = 'a.title like '.$this->s($this->controller->parameters->get('query').'%',$from).' 
                                OR a.article like '.$this->s('%'.$this->controller->parameters->get('query').'%',$from).'
                                AND a.namespace = ns.uid 
                                AND a.pid = '.$this->s($pid).' 
                                AND ns.pid = '.$this->s($pid);

                        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);

                        $json = Array();
                        if($query) {
                                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                        $json['results'][] = $row;
                                }
                        }
                        $entry = new tx_lib_object(array('response' => $json));
                        $this->append($entry);
                        break;

                case 'initializeFilemanager':
                        $select = 'COUNT(uid) as number';
                        $from = 'tx_siwiki_files';
                        $where = 'article_uid = '.$this->s($this->controller->parameters->get('uid')).' 
                                  AND pid = \''.$pid.'\' AND deleted=0';

                        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                        if($query) {
                                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                        $entry = new tx_lib_object(array('response' => $row));
                                }
                        }
                        $this->append($entry);
                        break;
                default:
                        $entry = new tx_lib_object(array('response' => 'defaultPoop'));
                        $this->append($entry);
                        break;
                }
        }

}

?>
