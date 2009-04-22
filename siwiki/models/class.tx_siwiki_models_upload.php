<?php
/**
 *
 * Uploads for articles
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_models_upload.php 1072 2009-02-03 14:23:05Z alappe $
 *
 */

class tx_siwiki_models_upload  extends tx_lib_object {

        /**
         * Uploads an image file to a specified folder and returns the filepath
         * @static
         * @param int $uploadedImageMaxWidth
         * @return String
         */
        public static function uploadImage($uploadedImageMaxWidth) {

                $mimetype = Array("image/jpeg" => ".jpeg", 
                        "image/jpg" => ".jpeg", 
                        "image/x-jpeg" => ".jpeg",
                        "image/x-png" => ".png",
                        "image/svg+xml" => ".svg", 
                        "image/png" => ".png", 
                        "image/gif" => ".gif"); 
                $status = Array();

                if(isset($_FILES['img'])){
                        $imgName = $_FILES['img']['name'];
                        $imgType = $_FILES['img']['type'];
                        $imgTmpName = $_FILES['img']['tmp_name'];
                        $imgSize = $_FILES['img']['size'];

		       // $image = tx_div::makeInstance('tx_lib_image');
                       // $image->pathString = $imgTmpName; 
                        
                        $absolutePath = PATH_site;
                        $relativePath = 'fileadmin/user_upload/siwiki/';
                        $filename = $relativePath."img".mt_rand(1000,1000000).$mimetype[$imgType]; 

                        if(array_key_exists($imgType,$mimetype)){
                                if(move_uploaded_file($imgTmpName,$absolutePath.$filename)){
                                        @chmod($file,octdec('0660'));
                                        $imageObjClassName = tx_div::makeInstanceClassName('tx_lib_image');
                                        $imageObj = new $imageObjClassName;
                                        $imageObj->maxWidth($uploadedImageMaxWidth);
                                        $imageObj->path($filename);
                                        $status[]["status"] = "UPLOADED";
                                        $status[]["image_url"] = preg_replace("/.*src=\"(.[^\"]*)\".*/", "\\1", $imageObj->make());
                                } else {
                                        $status[]["status"] = "Could not upload image";
                                }
                        } else {
                                $status[]["status"] = "Mimetype is not allowed!";
                        }
                } else {
                        $status[]["status"] = "No file submitted!";
                }
                return $status;
        }
}
?>
