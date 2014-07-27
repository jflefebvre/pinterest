<?php

// pinterest webscrapper - PHP script
// Use casperjs data to webscrap original pins to folder
// then generate thumbnails for Wookmark plugin
// @author  Jean-François Lefebvre (hello@e-volution.be)
// @Date    13/12/2013
// @version 0.9

require_once('PinterestHelper.php');

$pinterestJson = __DIR__ . '/pinterest.json';

if (!file_exists($pinterestJson) || filesize($pinterestJson)===0) {
    echo 'pinterest.json is missing or empty.'.PHP_EOL;
    echo 'Please run casperjs pinterest-casper.js script before to call the webscraper.'.PHP_EOL;
    die();
}

class L {
    public static function log($str) {
        error_log($str);
    }
}

$pinDataDir = __DIR__.'/pins/';

$pins_data = file_get_contents($pinterestJson);
$pins_data = json_decode($pins_data);

$pins = array();

try {

    $dbh = new PDO('sqlite:pinterest.db'); // success

    if ($dbh) {
        
        //$dbh->query('DROP TABLE pinterest_');

        $dbh->query('CREATE  TABLE IF NOT EXISTS pinterest_ (
                          id INTEGER PRIMARY KEY AUTOINCREMENT,
                          pin_image_name VARCHAR(50) NOT NULL ,
                          pinned INT NOT NULL ,
                          board VARCHAR(50) NULL ,
                          description TEXT NULL 
                          )');
        $dbh->query('CREATE UNIQUE INDEX index_pin_image_name on pinterest_ (pin_image_name)');

        foreach ($pins_data as $pin) {

            $pinDescription = $pin->description;
            $pinBoard = $pin->board;
            $pinThumbnail = $pin->pin_thumbnail;
            $pinPage = $pin->pin_page;

            $pinImageNameExplode = explode('/', $pinThumbnail); 
            $pinImageName = end($pinImageNameExplode); 

            // check if pin is already in the database
            $found = false;
            $stmt = $dbh->prepare("SELECT * FROM pinterest_ where pin_image_name like ?");
            if ($stmt) {
                $param = array('%'.$pinImageName.'%');
                
                $stmt->execute($param);
                $result = $stmt->fetchColumn();
                
                if ($result) {
                    $found = true;
                } 
            }

            // retrieve informations about the new pin and save the original pin image only if image not yet retrieved
            if (!$found) {
            	$pinInfo = PinterestHelper::getPinInfo($pinPage);
                if (!empty($pinInfo)) {
                    $pinFullPath = $pinInfo["src"];
                    $pinExplode = explode('/', $pinFullPath);
                    $pinName = end($pinExplode);
                    $pinFilename = $pinDataDir . $pinName;
                    if (!file_exists($pinFilename)) file_put_contents($pinFilename, file_get_contents($pinFullPath));

                    $pin = array();
                    $pin['image_name'] = $pinName;
                    $pin['pinned'] = $pinInfo['pinned'];
                    $pin['board'] = $pinBoard;
                    $pin['description'] = $pinDescription;
                    $pins[] = $pin;
            
                    $sql = "insert into pinterest_ (pin_image_name, pinned, board, description) values (:pin_image_name, :pinned, :board, :description)";
                    $stmt = $dbh->prepare($sql);
                    if ($stmt) {
                        $stmt->bindParam(':pin_image_name', $pin['image_name'], PDO::PARAM_STR, 50);
                        $stmt->bindParam(':pinned', $pin['pinned'], PDO::PARAM_INT);
                        $stmt->bindParam(':board', $pin['board'], PDO::PARAM_STR, 50);
                        $stmt->bindParam(':description', $pin['description'], PDO::PARAM_STR);
                        $res = $stmt->execute();
                        L::log('new pin : ' . $pin['image_name'] . ' stored');
                    } else {
                        L::log('error with ' . $pin['image_name']);
                    }
                }
            }
        }

    }
}

catch (PDOException $ex) {
    L::log('PDOException : ' . $ex->getMessage());
}

// Generate the thumbnails
PinterestHelper::generateThumbnails('pins');

echo 'DONE !' . PHP_EOL;