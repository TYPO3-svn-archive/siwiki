<?php

/**
 * this is the ajax controller
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_controllers_ajax.php 1240 2009-07-06 13:47:07Z sisak $
 *
 */ 
class tx_siwiki_controllers_ajax extends tx_lib_controller {

        var $defaultAction = 'ajaxAction';

        function ajaxAction() {
                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_ajax');
                $model = new $modelClassName($this);
                $model->ajaxRequest($this->parameters->get('request'),$this->configurations->get('storagePid'));

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this, $model);
                $view->castElements('tx_siwiki_views_siwiki');
                $view->renderJSON($this->configurations->get('ajaxTemplate'));

                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                $translator = new $translatorClassName($this,$view);
                return $translator->translateContent();
        }

        function relationsAction() {
                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_plotrelations');
                $model = new $modelClassName($this);
                $model->plot($this->configurations->get('storagePid'),$this->parameters->get('namespace'));

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this,$model);
                $view->castElements('tx_siwiki_views_siwiki');
                return $view->renderPNG($this->configurations->get('pngTemplate'));
        }

        function tocAction() {
                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_toc');
                $model = new $modelClassName($this);
                $model->loadToc($this->configurations->get('storagePid'), $this->parameters->get('namespace'));

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this,$model);
                $view->castElements('tx_siwiki_views_siwiki');
                $view->renderToc($this->configurations->get('tocTemplate'));

                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                $translator = new $translatorClassName($this,$view);

                return $translator->translateContent();
        }

        function getTagsAction() {
                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_tags');
                $model = new $modelClassName($this);
                $model->getTags($this->configurations->get('storagePid'), $this->parameters->get('uid'), $this->parameters->get('namespace'));

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this,$model);
                $view->castElements('tx_siwiki_views_siwiki');
                $view->renderToc($this->configurations->get('tagTemplate'));

                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                $translator = new $translatorClassName($this,$view);

                return $translator->translateContent();
        }

        function getImageAction() {
                $file = htmlspecialchars($this->parameters->get('file'));
                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this);
                $entry = new tx_lib_object(array('file' => $file));
                $view->append($entry);
                $view->castElements('tx_siwiki_views_siwiki');
                return $view->render($this->configurations->get('imageTemplate'));
        }

        function deleteFileAction() {
                $modelClassName = tx_div::makeInstanceClassName('tx_siwiki_models_files');
                $model = new $modelClassName($this);
                $model->deleteFile($this->parameters, $this->configurations->get('storagePid'), $this->configurations->get('filemanagerUploadFolder'));

                $viewClassName = tx_div::makeInstanceClassName('tx_siwiki_views_siwiki');
                $view = new $viewClassName($this, $model);
                $view->castElements('tx_siwiki_views_siwiki');
                $view->renderJSON($this->configurations->get('ajaxTemplate'));

                $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
                $translator = new $translatorClassName($this,$view);
                return $translator->translateContent();
        }

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/siwiki/controllers/class.tx_siwiki_controllers_ajax.php']) {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/siwiki/controllers/class.tx_siwiki_controllers_ajax.php']);
}
?>
