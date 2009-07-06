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

        public static function uploadImage($pid, $uid, $ajaxPageType, $uploadedImageMaxWidth, $relativePath) {
                $mimetype = Array("image/jpeg" => ".jpeg", 
                     "image/jpg" => ".jpeg", 
                     "image/x-jpeg" => ".jpeg",
                     "image/x-png" => ".png",
                     "image/svg+xml" => ".svg", 
                     "image/png" => ".png", 
                     "image/gif" => ".gif"); 
                                                                    
                $status = Array();                                  

                $uid = (int) $uid;
                $pid = (int) $pid;                                                    

                if(isset($_FILES['img'])){                          
                        $imgName = $_FILES['img']['name'];          
                        $imgType = $_FILES['img']['type'];
                        $imgTmpName = $_FILES['img']['tmp_name'];
                        $imgSize = $_FILES['img']['size'];

                        $relativePath = trim($relativePath);
                        if(substr($relativePath,0,1) == "/") $relativePath = substr_replace($relativePath,'',0,1);
                        if(substr($relativePath,strlen($relativePath)-1,1) !== "/") $relativePath .= $relativePath."/";
                        
                        $absolutePath = PATH_site;
                        $filename = $pid."_".$uid."_img".mt_rand(1000,9999).$mimetype[$imgType]; 
                        $file = $relativePath.$filename; 

		        $destination = $GLOBALS['TSFE']->id;  

                        if(array_key_exists($imgType,$mimetype)){
                                if(move_uploaded_file($imgTmpName,$absolutePath.$file)){

                                        $imageObjClassName = tx_div::makeInstanceClassName('tx_lib_image');
                                        $imageObj = new $imageObjClassName;
                                        $imageObj->maxWidth($uploadedImageMaxWidth);
                                        $imageObj->path($file); 
                                        
                                        $tmpImage = preg_replace("/.*src=\"(.[^\"]*)\".*/", "\\1", $imageObj->make());
                                        
                                        if(strpos($tmpImage,"temp/")){
                                                @unlink($absolutePath.$relativePath.$filename);
                                                @rename($absolutePath.$tmpImage,$absolutePath.$relativePath.$filename);
                                                @unlink($absolutePath.$tmpImage);
                                        }

                                        $url = "index.php?id=".$destination."&type=".$ajaxPageType."&siwiki[action]=getImage&siwiki[file]=".$filename; 
                                        $url = urlencode($url);

                                        $status[]["status"] = "UPLOADED";
                                        $status[]["image_url"] = $url;
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

        /**
         * Uploads a file to a specified folder and returns the filepath
         * @static
         * @param int $uploadedImageMaxWidth
         * @return String
         */
        public static function uploadFile($parameters,$pid, $relativePath) {
                $articleUid = (int) $parameters->get('uid');
                $pid = (int) $pid;
                $fileName = str_replace(' ','_',htmlspecialchars(trim($parameters->get('name'))));
                $fileDescription = htmlspecialchars($parameters->get('description'));
                
                $file = $_FILES['file'];
                $status = Array();
                if(isset($file)){
                        if($file['error']){
                                //TODO: Make error codes translatable
                                switch ($file['error']) {
                                         case UPLOAD_ERR_INI_SIZE:
                                                 $status[]["status"] = '%%%errorFileMax%%%';
                                         break;
                                         case UPLOAD_ERR_FORM_SIZE:
                                                 $status[]["status"] = '%%%errorFileMax%%%';
                                         break;
                                         case UPLOAD_ERR_PARTIAL:
                                                 $status[]["status"] = '%%%errorUploadPart%%%';
                                         break;
                                         case UPLOAD_ERR_NO_FILE:
                                                 $status[]["status"] = '%%%errorNoFile%%%';
                                         break;
                                         case UPLOAD_ERR_NO_TMP_DIR:
                                                 $status[]["status"] = '%%%errorUpload%%%';
                                         break;
                                         case UPLOAD_ERR_CANT_WRITE:
                                                 $status[]["status"] = '%%%errorUpload%%%';
                                         break;
                                         case UPLOAD_ERR_EXTENSION:
                                                 $status[]["status"] = '%%%errorUpload%%%';
                                         break;
                                         default: $status[]["status"] = 'Unknown upload error';
                                         break;
                                } 
                        } else { 
                                $absolutePath = PATH_site;
                                $relativePath = trim($relativePath);
                                if(substr($relativePath,0,1) == "/") $relativePath = substr_replace($relativePath,'',0,1);
                                if(substr($relativePath,strlen($relativePath)-1,1) !== "/") $relativePath .= $relativePath."/";
                                $prefix = 'siwikiArticle';
                                $path = $absolutePath.$relativePath.$prefix.$articleUid;

                                if(!@opendir($path)) {
                                        if(!@mkdir($path,0755)) $status[]["status"] = "%%%errorCreateFolder%%%";
                                } 
                                if($mimetype = self::checkMimetype($file)){
                                        $fileName = $fileName.$mimetype;

                                        if(file_exists($path.'/'.$fileName)){
                                                $status[]["status"] = "%%%errorFileExists%%%";
                                        } else {
                                            if(is_uploaded_file($file['tmp_name'])){
                                                if(move_uploaded_file($file['tmp_name'],$path.'/'.$fileName)){
                                                        $insertArray['pid'] = $pid;
                                                        $insertArray['tstamp'] = time();
                                                        $insertArray['user_uid'] = tx_siwiki_classes_misc::getUserId();
                                                        $insertArray['article_uid'] = $articleUid; 
                                                        $insertArray['file_name'] = $fileName;
                                                        $insertArray['file_description'] = $fileDescription;
                                                        $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_siwiki_files',$insertArray);
                                                        $status[]["status"] = "UPLOADED";
                                                } else {
                                                        $status[]["status"] = "%%%errorUpload%%%";
                                                }
                                            }
                                        }
                                } else {
                                        $status[]["status"] = "%%%errorMimetype%%%";
                                }
                        }
                } else {
                        $status[]["status"] = "%%%errorUpload%%%";
                }
                return $status;
        }

        /**
         * Check if the mimetype is allowed
         * TODO: make mimetypes configurable
         * @static
         * @param array $file
         * @return string
         */
        public static function checkMimetype($file){
                $mimetypes = self::getMimetypes();
                if(array_key_exists($file['type'],$mimetypes)){
                        return $mimetypes[$file['type']];
                } else {
                        return false;       
                }
        }

        public static function getMimetypes(){
                $mimetypes = Array("image/jpeg" => ".jpeg", 
                                  "image/jpg" => ".jpeg", 
                                  "image/x-jpeg" => ".jpeg",
                                  "image/x-png" => ".png",
                                  "image/svg+xml" => ".svg", 
                                  "image/png" => ".png", 
                                  "application/pdf" => ".pdf", 
                                  "image/gif" => ".gif",  
                                  "text/plain" => ".txt",
                                  "application/x-shockwave-flash" => ".swf",
                                  "video/mpeg" => ".mpe",
                                  "video/mpeg" => ".mpeg",
                                  "audio/mpeg" => ".mpg",
                                  "video/mpeg" => ".mpg",
                                  "audio/mpeg" => ".mpga",
                                  "application/excel" => ".xls",  
                                  "application/vnd.ms-excel" => ".xls",
                                  "application/x-excel" => ".xls",
                                  "application/x-msexcel" => ".xls",
                                  "application/excel" => ".xlt",
                                  "application/x-excel" => ".xlt",
                                  "application/pdf" => ".pdf", 
                                  "application/msword" => ".doc",
                                  "application/x-compressed" => ".zip",
                                  "application/x-zip-compressed" => ".zip",
                                  "application/zip" => ".zip",
                                  "application/x-zip-compressed" => ".zip",
                                  "application/mspowerpoint" => ".pps",
                                  "application/vnd.ms-powerpoint" => ".pps",
                                  "application/mspowerpoint" => ".ppt",
                                  "application/powerpoint" => ".ppt",
                                  "application/vnd.ms-powerpoint" => ".ppt",
                                  "application/x-mspowerpoint" => ".ppt");
                return $mimetypes;
        }
}
?>
