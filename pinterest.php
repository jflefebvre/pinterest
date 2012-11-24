<?php

    class Pinterest {
        
        function __construct() {

        }


        public function getPins($user) {
            $pagenum = 1;
            $content = "";

            // if we found the string noResults in the content
            // it means that we have reached the last page
            while (strpos($content, "noResults") == FALSE) {
                $content .= file_get_contents("http://pinterest.com/$user/pins/?lazy=1&page=$pagenum");
                $pagenum++;
            }
            preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $content, $matches);
            $matches = $matches[0];

            $pins = array();
            foreach ($matches as $pin) {
                $tmp = strpos($pin, "upload");
                if ($tmp !== FALSE) {
                    $pins[] = str_replace('_b', '_f', $pin);
                }
            }

            sort($pins);
            $pins = array_unique($pins);        
        
            return $pins;
        }

    } // end class Pinterest