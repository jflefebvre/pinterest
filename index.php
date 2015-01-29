<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

/** 
  * @desc A PHP script displaying Pinterest pins of a user using WookMark jQuery plugin
  * 
  * @author Jean-François Lefebvre lefebvre.jf@gmail.com
  * @link https://github.com/jflefebvre/pinterest
  * 
*/

 $pdoSqliteDsn = 'sqlite:pinterest.db';
 $numberOfItemsByScroll = 20;

  /**
   * Helper function to build a link for jQuery Wookmark plugin + lightbox
   */
  function l($pin) {
    $d = array("width"=>0, "height"=>0);
    $filepath = __DIR__.'/pins/mini-' . $pin['pin_image_name'];
    if (file_exists($filepath)) {
	$image = new Imagick($filepath); 
    	$d = $image->getImageGeometry(); 
    }

    $link  = '<a href="pins/' . $pin['pin_image_name'] . '" rel="lightbox">';
    $link .= '<img src="pins/mini-' . $pin['pin_image_name'] . '" alt="' . $pin['description'] . ' - ' . $pin['board'] . '" width="' . $d['width'].'" height="' . $d['height'] . '">';
    $link .= '</a>';
    $link .= '<p class="info">' . $pin['description'] . '</p><p class="board">::' . $pin['board'] . '</p>';
    return $link;
  }


if (isset($_GET['p'])) {
      $page = $_GET['p'];
      $offset = ($page * $numberOfItemsByScroll)+1;
      try {

        $dbh = new PDO($pdoSqliteDsn); 

        if ($dbh) {
          $sth = $dbh->prepare("SELECT * FROM pinterest_ order by 1 desc limit " . $offset . "," . $numberOfItemsByScroll);
          $sth->execute();
          $pins = $sth->fetchAll();
            
          $content = '';
          foreach ($pins as $pin) {
              $content .= '<li class="item">' . l($pin) . '</li>';
          }
          echo $content;
        }

      }
      catch (PDOException $ex) {
        echo $ex->getMessage();
      }

      die();    
  }

?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Pinterest</title>
  <meta name="description" content="Showcase of my pins (iamjeff75) using Wookmark">
  <meta name="author" content="Jean-François Lefebvre">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/reset.css">  
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/colorbox.css">
  <link href='http://fonts.googleapis.com/css?family=Karla' rel='stylesheet' type='text/css'>
</head>
<body>

  <div id="container">
    <header>
      <h1><a id="Pinterest" href="/"></a></h1>
      <p>Hi ! I'm <a href="http://pinterest.com/iamjeff75/" target="_blank">iamjeff75</a> on Pinterest, here is all my inspirational pins retrieved with my pinterest web scrapping code.</p>
      <p>Feel free to fork the <a href="https://github.com/jflefebvre/pinterest" target="_blank">code available on GitHub</a>.</p> 
    </header>
    <div id="main" role="main">
      <div id="grid">
        <ul id="tiles">
<?php

        $pins = array();
        try {

            $dbh = new PDO($pdoSqliteDsn); 

            if ($dbh) {
                $sth = $dbh->prepare("SELECT * FROM pinterest_ order by 1 desc limit 0," . $numberOfItemsByScroll);
                $sth->execute();
                $pins = $sth->fetchAll();
            
                foreach ($pins as $pin) {
                  echo '<li class="item">' . l($pin) . '</li>';
                }

            }
        }
        catch (PDOException $ex) {
          echo $ex->getMessage();
        } 
?>
        </ul>
      </div>

    </div>
    <footer>

    </footer>
  </div>
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.imagesloaded.js"></script>
  <script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
  <script type="text/javascript" src="js/jquery.wookmark.min.js"></script>
  <script type="text/javascript">
    window.page=1;
    (function ($){
      $('#tiles').imagesLoaded(function() {
        var handler = null;

        // Prepare layout options.
        var options = {
          autoResize: true, // This will auto-update the layout when the browser window is resized.
          container: $('#grid'), // Optional, used for some extra CSS styling
          offset: 20, // Optional, the distance between grid items
          itemWidth: 200 // Optional, the width of a grid item
        };

        function applyLayout() {
          $('#tiles').imagesLoaded(function() {
            // Destroy the old handler
            if (handler.wookmarkInstance) {
              handler.wookmarkInstance.clear();
            }

            // Create a new layout handler.
            handler = $('#tiles li');
            handler.wookmark(options);
            $('a', handler).colorbox({
              rel: 'lightbox'
            });
          });
        }

        /**
         * When scrolled all the way to the bottom, add more tiles.
         */
        function onScroll(event) {
          // Check if we're within 100 pixels of the bottom edge of the broser window.
          var winHeight = window.innerHeight ? window.innerHeight : $(window).height(); // iphone fix
          var closeToBottom = ($(window).scrollTop() + winHeight > $(document).height() - 100);

          if (closeToBottom) {

            $.get( "index.php?p="+(++window.page), function( data ) {
              $('#tiles').append($(data));
              applyLayout();
            });

          }
        };

        // Capture scroll event.
        $(window).bind('scroll', onScroll);

        // Call the layout function.
        handler = $('#tiles li');
        handler.wookmark(options);
        $('a', handler).colorbox({
              rel: 'lightbox'
        });
      });
    })(jQuery);
  </script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-116372-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
