<?php
/**
 *
 * The notification class, checking for updates to notify the users
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id$
 *
 */

class tx_siwiki_models_notification extends tx_lib_object {

        public function getUsersToNotify($article_uid, $pid){

                $select = 'n.user_uid, fe.email';
                $where = 'article_uid = \''.$article_uid.'\' AND n.user_uid = fe.uid AND n.pid=\''.$pid.'\'';
                $from = 'tx_siwiki_notifications as n, fe_users as fe';

                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where);

                if($rows){
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($rows)){
                                $entry = new tx_lib_object(Array('user_uid' => $row['user_uid'], 'email' => $row['email']));
                                $this->append($entry);
                        }
                }
        }
}
?>
