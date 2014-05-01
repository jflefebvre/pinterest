<?php

	require_once('simple_html_dom.php');

	function startsWith($haystack, $needle) {
	    return $needle === "" || strpos($haystack, $needle) === 0;
	}

	class PinterestHelper {

		public static function resetData($dataDir) {

			$files = glob($dataDir.'*.jpg'); 
			foreach($files as $file){ 
			  if(is_file($file))
			    unlink($file); 
			}
		}

		public static function calculateTimestamp($s) {
			
			if (null == $s) {
				return time();
			}

			$info = str_replace(array('Uploaded', 'Pinned', 'Repinned', 'ago', '&bull;'), '', $s);
			$info = trim($info);
			$tokens = explode(' ', $info);

			$pinTimestamp = time();
			$oneDay = 60*60*24;
			$oneWeek = $oneDay*7;
			$oneYear = $oneDay*365;

			switch ($tokens[1]) {
				case 'day':
				case 'days':
					$pinTimestamp -= ($tokens[0]*$oneDay);
				break;

				case 'week':
				case 'weeks':
					$pinTimestamp -= ($tokens[0]*$oneWeek);
				break;

				case 'year':
				case 'years':
					$pinTimestamp -= ($tokens[0]*$oneYear);
				break;
			}

			return $pinTimestamp;
		}

		public static function getPinInfo($pinUrl) {

			$pinInfo = array();

			if (@fopen($pinUrl, "r")) {
				// retry making file_get_contents when connection has failed
				$pinContent = false;
				while($pinContent === false) {
					//echo 'Try to connect to ' . $pinUrl . PHP_EOL;
					$pinContent = file_get_contents($pinUrl);
				}

				$html = str_get_html($pinContent);
				$pin = $html->find('.pinImage');
				$attr = $pin[0]->attr;

				$pinInfo['alt'] = $attr['alt'];
				$pinInfo['src'] = $attr['src'];
				$imageStyle = $attr['style'];
				$imageStyle = explode(';', $imageStyle);
				$pinInfo['width'] = (int)trim(str_replace('px','', explode(':', $imageStyle[0])[1]));
				$pinInfo['height'] = (int)trim(str_replace('px','', explode(':', $imageStyle[1])[1]));
				
				
				$timestamp = $html->find('.commentDescriptionTimeAgo');
				$pinInfo['pinned'] = self::calculateTimestamp($timestamp[0]->innertext);
		
			}

			return $pinInfo;
		}

		public static function generateThumbnails($src) {
			// remove all existing thumbnails
		    // exec("rm $src/mini-*"); 

		    // create thumbnails 
		    $dir = opendir($src); 
		    while(false !== ( $file = readdir($dir)) ) { 
		        if (( $file != '.' ) && ( $file != '..' )) {
		            $tmp = explode('.', $file); 
		            $ext = end($tmp);
				    if ($ext == 'jpg' && !startsWith($file, 'mini-') && (!file_exists($src."/"."mini-".$file))) {
				    	 echo " Generating thumbnail for $src/$file\n";
					     exec("convert $src/$file -background white -gravity center -resize 200x -quality 80 $src/mini-$file");
		   	    	}
		        } 
		    } 
		    closedir($dir); 
		}
	}
