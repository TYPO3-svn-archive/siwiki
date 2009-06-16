<?php

/**
 * this is the ajax controller
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_controllers_ajax.php 1221 2009-06-16 09:34:37Z sisak $
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
