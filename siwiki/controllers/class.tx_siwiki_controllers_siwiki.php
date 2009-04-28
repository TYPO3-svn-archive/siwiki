<?php

/**
 * this is the main controller to display the wiki
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_controllers_siwiki.php 1201 2009-04-28 10:00:45Z sisak $
 *
 */ 

class tx_siwiki_controllers_siwiki extends tx_lib_controller {

        private $username;

        var $defaultAction = 'displayAction';

        /**
         *  wrap all content with a general div container
         *  > weird solution 
         *  > todo: try to figure out a better way
         */
        function wrapTranslatedContent($translatedContent) {
                return '<div id="tx_siwiki">'.$translatedContent."</div>";
        }

	/**
	 * Shows a wiki page from tx_siwiki_articles_cache
	 *
	 * @return	String
	 */
        function displayAction() {
                //if no uid is set, use the rootpage from configurations
                $uid = $this->parameters->get('uid');
                $uid = intval($uid ? $uid : $this->configurations->get('rootpage'));
                $this->parameters->set('uid', $uid);

                $ns = $this->parameters->get('namespace');
                $ns = intval($ns ? $ns : $this->configurations->get('defaultNamespace'));
                $this->parameters->set('namespace', $ns);

                try {
                        $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleCache');
                        $model = new $modelClassName($this);
                        $model->load($uid,$this->configurations->get('storageFolder'));
                }
                catch(Exception $e){
                        try {
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                                $model = new $modelClassName($this);
                                $model->load($uid,$this->configurations->get('storageFolder'));

                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleCache');
                                $modelArticleCache = new $modelClassName($this,$this->parameters);
                                $modelArticleCache->insert($uid,$this->configurations->get('storageFolder'));

                        }
                        catch(Exception $e){
                                if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                $model->append(array('siwikiException' => $e->getMessage()));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $model);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                        }
                }

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this,$model); 
                $view->castElements('tx_siwiki_views_siwiki');
                $view->render($this->configurations->get('siwikiTemplate'));

                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                $translator = new $translatorClassName($this,$view);

                return $this->wrapTranslatedContent($translator->translateContent());
        }

	/**
	 * Edit view for an article
	 *
	 * @return	String
	 */
        function editAction() {
                $this->username = tx_siwiki_classes_misc::getUsername();
                if($this->configurations->get('anonymous') || ($this->username != 'anonymous' && $this->username != '')){ 
                        // Is the article locked?
                        if(tx_siwiki_models_locking::isLocked($this->parameters->get('uid'), $this->configurations->get('timeAnArticleRemainsLocked'),tx_siwiki_classes_misc::getUserID())) {
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                                $model = new $modelClassName($this);
                                $model->append(array('siwikiException' => 'This article is currently locked by '.tx_siwiki_models_locking::isLockedBy($this->parameters->get('uid')).'.'));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $model);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));

                        } else {
                                // Remove old locking
                                tx_siwiki_models_locking::removeLocking($this->parameters->get('uid'));

                                // Begin locking
                                tx_siwiki_models_locking::setLocking($this->parameters->get('uid'), tx_siwiki_classes_misc::getUserId());

                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                                $model = new $modelClassName($this);
                                $model->load($this->parameters->get('uid'), $this->configurations->get('storageFolder'));

                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this,$model); 
                                $view->castElements('tx_siwiki_views_siwiki');
                                $view->render($this->configurations->get('editTemplate'));

                                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                                $translator = new $translatorClassName($this,$view);

                                return $this->wrapTranslatedContent($translator->translateContent());
                        } // end if
                } else {
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                                $model = new $modelClassName($this);
                                $model->append(array('siwikiException' => 'sorry, you don\'t have access!'));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $model);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                }
        }

  	/**
         * Saves the old version to tx_siwiki_versions. 
         * Updates the article in tx_siwiki_article and tx_siwiki_articleCache
	 */
        function saveAction() {
                                
                $this->username = tx_siwiki_classes_misc::getUsername();
                if($this->configurations->get('anonymous') || ($this->username != 'anonymous' && $this->username != '')){ 
                        // End locking
                        tx_siwiki_models_locking::removeLocking($this->parameters->get('uid'));

                                if($this->parameters->get('articleHash') != md5($this->parameters->get('article'))){
                                
                                //save old version 
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleVersion');
                                $modelArticleVersion = new $modelClassName($this,$this->parameters);
                                try{
                                        $modelArticleVersion->save($this->parameters->get('uid'), $this->configurations->get('storageFolder'), $this->parameters->get('comment'));
                                }
                                catch(Exception $e) {
                                        if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                        $modelArticleVersion->append(array('siwikiException' => $e->getMessage()));
                                        $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                        $view = new $viewClassName($this, $modelArticleVersion);
                                        $view->castElements('tx_siwiki_views_siwiki');
                                        return $view->render($this->configurations->get('errorTemplate'));
                                }

                                //creates new version 
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                                $modelArticle = new $modelClassName($this,$this->parameters);

                                try{
                                        $modelArticle->save($this->parameters->get('uid'), $this->configurations->get('storageFolder'));
                                }
                                catch(Exception $e) {
                                if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                        $modelArticle->append(array('siwikiException' => $e->getMessage()));
                                        $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                        $view = new $viewClassName($this, $modelArticle);
                                        $view->castElements('tx_siwiki_views_siwiki');
                                        return $view->render($this->configurations->get('errorTemplate'));
                                }

                                //creates cached article
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleCache');
                                $modelArticleCache = new $modelClassName($this,$this->parameters);
                                try {
                                        $modelArticleCache->save($this->parameters->get('uid'), $this->configurations->get('storageFolder'));
                                }
                                catch(Exception $e) {
                                        if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                        $modelArticleCache->append(array('siwikiException' => $e->getMessage()));
                                        $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                        $view = new $viewClassName($this, $modelArticleCache);
                                        $view->castElements('tx_siwiki_views_siwiki');
                                        return $view->render($this->configurations->get('errorTemplate'));
                                }
                                
                                //check if it is necessary to notify any users about the changes above
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_notification');
                                $notification = new $modelClassName($this);
                                $notification->getUsersToNotify($this->parameters->get('uid'),$this->configurations->get('storageFolder'));
                                $message = "Wiki update service";
                                foreach($notification as $entry){
                                        $status = mail($entry->get('email'),"Update in > ".$modelArticle->get('title'),$message);
                                }
                        }
                        
                        //redirect to avoid double entries
                        $linkClassName = tx_div::makeInstanceClassName('tx_lib_link');
                        $link = new $linkClassName();
                        $link->destination($this->getDestination());
                        $link->designator($this->getDesignator());
                        $link->parameters(array('namespace' => $this->parameters->get('namespace'),
                                                'uid' => $this->parameters->get('uid')));
                        $link->noHash();
                        $link->redirect();
                } else {
                                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                                $model = new $modelClassName($this);
                                $model->append(array('siwikiException' => 'sorry, you don\'t have access!'));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $model);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                }
        }


  	/**
         *  cancel edit
	 */
        function cancelAction() {
                // End locking
                tx_siwiki_models_locking::removeLocking($this->parameters->get('uid'));

                //redirect to avoid double entries
                $linkClassName = tx_div::makeInstanceClassName('tx_lib_link');
                $link = new $linkClassName();
                $link->destination($this->getDestination());
                $link->designator($this->getDesignator());
                $link->parameters(array('namespace' => $this->parameters->get('namespace'),
                                        'uid' => $this->parameters->get('uid')));
                $link->noHash();
                $link->redirect();
        }

        /**
         * Creates a view for a new article 
         *
         * @return      String
	 */
        function newAction() {

                $this->username = tx_siwiki_classes_misc::getUsername();
                if($this->configurations->get('anonymous') || ($this->username != 'anonymous' && $this->username != '')){ 

                        $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                        $modelArticle = new $modelClassName($this); 
                        $modelArticle->createNewArticle();

                        $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                        $view = new $viewClassName($this,$modelArticle); 
                        $view->castElements('tx_siwiki_views_siwiki');
                        $view->render($this->configurations->get('newTemplate'));

                        $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                        $translator = new $translatorClassName($this,$view);

                        return $this->wrapTranslatedContent($translator->translateContent());

                } else {
                                $modelclassname = tx_div::makeinstanceclassname('tx_siwiki_models_article');
                                $model = new $modelclassname($this);
                                $model->append(array('siwikiexception' => 'sorry, you don\'t have access!'));
                                $viewclassname = tx_div::makeinstanceclassname('tx_siwiki_views_siwiki');
                                $view = new $viewclassname($this, $model);
                                $view->castelements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                }

        }

         /**
         * Deletes article from tx_siwiki_articles and tx_siwiki_articles_cache
	 */
        function deleteAction() {
                $this->username = tx_siwiki_classes_misc::getUsername();
                if($this->configurations->get('anonymous') || ($this->username != 'anonymous' && $this->username != '')){ 

                        $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                        $modelArticle = new $modelClassName($this); 

                        try {
                                $modelArticle->delete($this->parameters->get('uid'),$this->configurations->get('storageFolder'));
                        }
                        catch(Exception $e) {
                                if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                $modelArticle->append(array('siwikiException' => $e->getMessage()));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $modelArticle);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                        }

                        $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleCache');
                        $modelArticleCache = new $modelClassName($this); 
                        try {
                                $modelArticleCache->delete($this->parameters->get('uid'),$this->configurations->get('storageFolder'),$this->parameters->get('title'),$this->parameters->get('namespace'));

                        }
                        catch(Exception $e) {
                                if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                $modelArticle->append(array('siwikiException' => $e->getMessage()));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $modelArticle);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                        }
                        
                        //redirect to root page
                        $linkClassName = tx_div::makeInstanceClassName('tx_lib_link');
                        $link = new $linkClassName();
                        $link->destination($this->getDestination());
                        $link->designator($this->getDesignator());
                        $link->parameters(array('namespace' => $this->configurations->get('defaultNamespace'),
                                                'uid' => $this->configurations->get('rootpage')));
                        $link->noHash();
                        $link->redirect();
                } else {
                                $modelclassname = tx_div::makeinstanceclassname('tx_siwiki_models_article');
                                $model = new $modelclassname($this);
                                $model->append(array('siwikiexception' => 'sorry, you don\'t have access!'));
                                $viewclassname = tx_div::makeinstanceclassname('tx_siwiki_views_siwiki');
                                $view = new $viewclassname($this, $model);
                                $view->castelements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                }

        }

        /**
         * Inserts a new article into tx_siwiki_articles and tx_siwiki_articles_cache
	 */
        function insertAction() {
                $this->username = tx_siwiki_classes_misc::getUsername();
                if($this->configurations->get('anonymous') || ($this->username != 'anonymous' && $this->username != '')){ 
                        $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                        $modelArticle = new $modelClassName($this,$this->parameters);
                        try {
                                $uid = $modelArticle->insert($this->configurations->get('storageFolder'));
                        }
                        catch(Exception $e) {
                                if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                $modelArticle->append(array('siwikiException' => $e->getMessage()));
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $modelArticle);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                        }
                        $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleCache');
                        $modelArticleCache = new $modelClassName($this,$this->parameters);
                        try {
                                $modelArticleCache->insert($uid,$this->configurations->get('storageFolder'));
                        }
                        catch(Exception $e) {
                                if($this->configurations->get('adminMail')) mail($this->configurations->get('adminMail'), 'exception triggered!', $e);
                                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                                $view = new $viewClassName($this, $modelArticle);
                                $view->castElements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                        }

                        //redirect to avoid double entries
                        $linkClassName = tx_div::makeInstanceClassName('tx_lib_link');
                        $link = new $linkClassName();
                        $link->destination($this->getDestination());
                        $link->designator($this->getDesignator());
                        $link->parameters(array('namespace' => $this->parameters->get('namespace'),
                                                'uid' => $uid));
                        $link->noHash();
                        $link->redirect();

                } else {
                                $modelclassname = tx_div::makeinstanceclassname('tx_siwiki_models_article');
                                $model = new $modelclassname($this);
                                $model->append(array('siwikiexception' => 'sorry, you don\'t have access!'));
                                $viewclassname = tx_div::makeinstanceclassname('tx_siwiki_views_siwiki');
                                $view = new $viewclassname($this, $model);
                                $view->castelements('tx_siwiki_views_siwiki');
                                return $view->render($this->configurations->get('errorTemplate'));
                }
        }

        function diffAction() {
                //if no uid is set, use the rootpage from configurations
                $uid = $this->parameters->get('uid');
                $uid = intval($uid ? $uid : $this->configurations->get('rootpage'));
                $this->parameters->set('uid', $uid);

                $ns = $this->parameters->get('namespace');
                $ns = intval($ns ? $ns : $this->configurations->get('defaultNamespace'));
                $this->parameters->set('namespace', $ns);

                $modelArticleClassName = tx_div::makeInstanceClassName('tx_siwiki_models_article');
                $modelArticle = new $modelArticleClassName($this);
                $modelArticle->load($uid,$this->configurations->get('storageFolder'));

                $modelArticleVersionClassName = tx_div::makeInstanceClassName('tx_siwiki_models_articleVersion');
                $modelArticleVersion = new $modelArticleVersionClassName($this);
                $modelArticleVersion->load($uid,$this->configurations->get('storageFolder'), $this->parameters->get('oldVersion'));

                $modelDiffClassName = tx_div::makeInstanceClassName('tx_siwiki_models_diff');
                $modelDiff = new $modelDiffClassName($this);
                $modelDiff->createDiff($modelArticle,$modelArticleVersion);

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this, $modelDiff);
                $view->castElements('tx_siwiki_views_siwiki');
                $view->render($this->configurations->get('diffTemplate'));

                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                $translator = new $translatorClassName($this,$view);

                return $this->wrapTranslatedContent($translator->translateContent());
        }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/siwiki/controllers/class.tx_siwiki_controllers_siwiki.php']) {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/siwiki/controllers/class.tx_siwiki_controllers_siwiki.php']);
}
?>
