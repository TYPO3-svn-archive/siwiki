<?php
/**
 *
 * Namespaces for articles
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_namespace.php 1162 2009-02-16 12:59:12Z sisak $
 *
 */

class tx_siwiki_models_namespace  extends tx_lib_object {

        private $uid;
        private $pid;
        private $hidden;
        private $deleted;
        private $name;
        private $description;

        protected $table = 'tx_siwiki_namespaces';

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
         * Set the properties to those of the specified namespace
         *
         * @param int $uid
         * @param int $pid
         * @depreceated
         * @return bool
         */
        private function setNamespaceByUid($uid,$pid) {

                $select = '*';
                $where = 'pid = '.$this->s($pid).' AND deleted = 0 AND hidden = 0 AND uid = '.$this->s($uid);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$this->table,$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                foreach($row as $entry=>$value) {
                                        $this->$entry = $value;
                                }
                        }
                        return true;
                }
                else {
                        throw new exception('Could not get namespace with uid = '.$uid);
                }
        }

        /**
         * Get an array containing all namespaces of a certain pid
         * @static
         * @param int $pid
         * @return array
         */
        public static function getAllNamespaces($pid) {
                $ns = array();
                $fields = 'uid, name';
                $where = 'pid = '.self::s($pid).' AND deleted = 0 AND hidden = 0';
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields,'tx_siwiki_namespaces',$where);

                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                $ns[] = $row;
                        }
                }

                return $ns;
        }

        /**
         * Get the uid of an namespace by name
         * @static
         * @param string $name
         * @param int $pid
         * @return int
         */
        public static function getNamespaceByName($name,$pid) {


                $fields = 'uid';
                $where = 'pid = '.self::s($pid).' AND deleted = 0 AND hidden = 0 AND name = '.self::s($name);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields,'tx_siwiki_namespaces',$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                return (int) $row['uid'];
                        }
                }
                else {
                        throw new exception('Could not get namespace by name: '.$name);
                }

        }

        /**
         * Get the name of an namespace by uid
         * @static
         * @param int $uid
         * @param int $pid
         * @return string
         */
        public static function getNamespaceByUid($uid, $pid) {
                $fields = 'name';
                $where = 'pid = '.self::s($pid).' AND deleted = 0 AND hidden = 0 AND uid = '.self::s($uid);
                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields,'tx_siwiki_namespaces',$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                return (string) $row['name'];
                        }
                }
                else {
                        throw new exception ('Could not get namespace by uid: '.$uid);
                }
        }


}
?>
