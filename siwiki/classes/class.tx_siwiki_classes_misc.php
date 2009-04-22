<?php
/**
 *
 * Some more or less useful functions...
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_classes_misc.php 1177 2009-04-09 12:29:29Z sisak $
 *
 */

class tx_siwiki_classes_misc {

        /**
         * Return the username of the current fe_user
         *
         * @return string
         */
        public static function getUsername() {
                $context = new tx_lib_context();
                $FE = $context->getFrontEnd();
                $userArr = $FE->fe_user->user;

                return (empty($userArr['username'])) ? 'anonymous' : $userArr['username'];
        }

        /**
         * Return the email-adress of the current fe_user
         *
         * @return string
         */
        public static function getEmail() {
                $context = new tx_lib_context();
                $FE = $context->getFrontEnd();
                $userArr = $FE->fe_user->user;

                return (empty($userArr['email'])) ? 'anonymous' : $userArr['email'];
        }

        /**
         * Return the firstname of the current fe_user
         *
         * @return string
         */
        public static function getFirstname() {
                $context = new tx_lib_context();
                $FE = $context->getFrontEnd();
                $userArr = $FE->fe_user->user;

                return (empty($userArr['firstname'])) ? 'anonymous' : $userArr['firstname'];
        }


        /**
         * Return the lastname of the current fe_user
         *
         * @return string
         */
        public static function getLastname() {
                $context = new tx_lib_context();
                $FE = $context->getFrontEnd();
                $userArr = $FE->fe_user->user;

                return (empty($userArr['lastname'])) ? 'anonymous' : $userArr['lastname'];
        }


        /**
         * Return the user id of the current fe_user
         *
         * @return int
         */
        public static function getUserId() {
                $context = new tx_lib_context();
                $FE = $context->getFrontEnd();
                $userArr = $FE->fe_user->user;

                return (empty($userArr['uid'])) ? '666666' : $userArr['uid'];
        }


        /**
         * Get date format
         *
         * @return string
         */
        public static function getDateFormatString() {
                if(isset($TYPO3_CONF_VARS['SYS']['ddmmyy']))
                        return $TYPO3_CONF_VARS['SYS']['ddmmyy'];
                else
                        return '%d. %B %Y (%R)';
        }


}

?>
