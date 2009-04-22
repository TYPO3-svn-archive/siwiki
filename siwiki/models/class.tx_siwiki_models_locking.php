<?php

/**
 * Locking
 *
 * Static class for locking, checking and unlocking an article
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_locking.php 1162 2009-02-16 12:59:12Z sisak $
 *
 */
class tx_siwiki_models_locking extends tx_lib_object {


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
         * Lock an article
         *
         * @param int $uid
         * @param int $user
         */
        public static function setLocking($uid,$user) {

                $fields_value['uid'] = $uid;
                $fields_value['crdate'] = $fields_value['tstamp'] = time();
                $fields_value['user'] = $user;

                $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_siwiki_articles_locking',$fields_value);

        }

        /**
         * Update lock
         *
         * @param int $uid
         */
        public static function updateLocking($uid) {
                $fields_values['tstamp'] = time();
                $where = 'uid = '.self::s($uid);
                
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_siwiki_articles_locking',$where, $fields_values);
        }

        /**
         * Check if the article is locked (and validly locked)
         *
         * If the user is the same we remove the locking and the user can start again
         *
         * @param int $uid
         * @param int $interval The number of minutes an article stays locked if it didn't get unlocked (browser crash)
         * @param int $user
         * @return bool
         */
        public static function isLocked($uid, $interval,$user) {

                $select = 'tstamp,user';
                $from = 'tx_siwiki_articles_locking';
                $where = 'uid = '.self::s($uid);

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                if($row['user'] == $user) {
                                        tx_siwiki_models_locking::removeLocking($uid);
                                        return false;
                                }
                                else {
                                        if($row['tstamp'] < (time()-($interval*60))) {
                                                tx_siwiki_models_locking::removeLocking($uid);
                                                return false;
                                        }
                                        else {
                                                return true;
                                        }
                                }
                        }
                } else {
                        return false;
                }
        }


        /**
         * Unlock the article
         *
         * @param int $uid
         */
        public static function removeLocking($uid) {
                $where = 'uid = '.self::s($uid);

                $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_siwiki_articles_locking',$where);
        }

        public static function isLockedBy($uid) {
                $select = 'firstname, lastname';
                $from = 'tx_siwiki_articles_locking as l, fe_users as f';
                $where = 'f.uid = l.user AND l.uid = '.self::s($uid);

                $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);
                if($query) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
                                return $row['firstname'].' '.$row['lastname'];
                        }
                }
        }

}

?>
