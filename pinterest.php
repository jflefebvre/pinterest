<?php

/** 
  * @desc this class provides Pinterest web scrapping functionalities
  * 
  * @author Jean-François Lefebvre lefebvre.jf@gmail.com
  * @link https://github.com/jflefebvre/pinterest
  * 
*/

    class Pinterest {
        
        function __construct() {

        }

        /* Clean pins folder */
        private function rrmdir($path) {
            
            $files = glob($path.'/*');
            
	    if (empty($files))
                return null;

            return is_file($path) ? @unlink($path) : array_map(array($this, 'rrmdir'), $files);
        }

        /**
         * Retrieve pinterest big images for a specific user and save the list of image in pinterest.json file
         * By default, the images are stored on pinterest cache server, but the second parameter allows to
         * download and store locally the images.
         * 
         * @param user          pinterest use name
         * @param storeLocally  store pins locally (on the filesystem)
         *  
         */
        public function getPins($user, $storeLocally = false) {

            $pagenum = 1;
            $content = "";
            $pinterestDir = "";

            if ($storeLocally) {
                // remove all existing pins
                $pinterestDir = __DIR__ . '/pins/';
                $this->rrmdir($pinterestDir);   
            }
      
            // the last page of content contains the string noResults 
            while (strpos($content, "noResults") == FALSE) {
                $content .= file_get_contents("http://pinterest.com/$user/pins/?lazy=1&page=$pagenum");
                $pagenum++;
            }
            preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $content, $matches);
            $matches = $matches[0];

            $pins = array();
            foreach ($matches as $pin) {

                $tmp = strpos($pin, "550x");
                if ($tmp !== FALSE) {

                    if ($storeLocally) {
                        // download and store pin
                        // $pinterestDir = __DIR__;
                        $imageName = basename($pin);
                        $imageFullPath = $pinterestDir . $imageName;
                        if (!file_exists($imageFullPath)) {
                            file_put_contents($imageFullPath, file_get_contents($pin));
                        }
                        
                        $pin = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
                        $pin = substr($pin,0, strrpos($pin, '/')+1);
                        $pin .= 'pins/';
                        $pin .= $imageName;
                    }

                    $pins[] = $pin;
                }
            }

            sort($pins);
            $pins = array_unique($pins);        
        
            return $pins;
        }

    } 
