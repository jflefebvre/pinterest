<?php

/** 
  * @desc A PHP script displaying Pinterest pins of a user using WookMark jQuery plugin
  * 
  * @author Jean-François Lefebvre lefebvre.jf@gmail.com
  * @link https://github.com/jflefebvre/pinterest
  * 
*/

$storePinsLocally = true;
$pinterestUser = 'iamjeff75';

  if (isset($_GET['p'])) {
      $page = $_GET['p'];
      if (file_exists('pinterest.json')) {
        $content = file_get_contents('pinterest.json');
        $pins = unserialize($content);
        $offset = ($page * 10)+1;
        $pins = array_slice($pins, $offset, 10);

        $content = '';
        foreach ($pins as $pin) {
          $content .="<li><img src='$pin'/></li>";          
        }
        echo $content;
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
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="reset.css">  
  <link rel="stylesheet" href="style.css">
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

      <ul id="tiles">
<?php

require_once 'pinterest.php';

$pinterest = new Pinterest();
$pins = array();

if (file_exists('pinterest.json') && !isset($_GET['rebuild'])) {
  $content = file_get_contents('pinterest.json');
  $pins = unserialize($content);
} else {
  $pins = $pinterest->getPins($pinterestUser, $storePinsLocally);
  $data = serialize($pins);
  file_put_contents('pinterest.json', $data);
}

$pins = array_slice($pins, 0, 10);
foreach ($pins as $pin) {
	echo "<li><img src='$pin' /></li>";
}

?>
 <!-- End of grid blocks -->
      </ul>

    </div>
    <footer>

    </footer>
  </div>

  <!-- include jQuery -->
  <script src="jquery-1.7.1.min.js"></script>  
  <!-- Include the imagesLoaded plug-in -->
  <script src="jquery.imagesloaded.js"></script>  
  <!-- Include the plug-in -->
  <script src="jquery.wookmark.min.js"></script>  
  <!-- Once the images are loaded, initalize the Wookmark plug-in. -->
  

  <!-- Once the page is loaded, initalize the plug-in. -->
  <script type="text/javascript">
    var handler = null;
    
    var page = 1;

    // Prepare layout options.
    var options = {
      autoResize: true, // This will auto-update the layout when the browser window is resized.
      container: $('#main'), // Optional, used for some extra CSS styling
      offset: 5, // Optional, the distance between grid items
      itemWidth: 800 // Optional, the width of a grid item
    };
    
    /**
     * When scrolled all the way to the bottom, add more tiles.
     */
    function onScroll(event) {
      // Check if we're within 100 pixels of the bottom edge of the broser window.
      var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 100);
      if(closeToBottom) {        
        page++;
        var ajaxRunning = false;
        if (!ajaxRunning) {
          ajaxRunning = true;
          $.ajax({
            url: 'index.php?p='+page,
            success: function(data) {
              $('#tiles').append(data);            
              // Clear our previous layout handler.
              if(handler) handler.wookmarkClear();
              
              // Create a new layout handler.
              handler = $('#tiles li');
              handler.wookmark(options);
            }
          });
          ajaxRunning = false;
        }
      }
    };
  
    $(document).ready(new function() {
      // Capture scroll event.
      $(document).bind('scroll', onScroll);
      
      // Call the layout function.
      handler = $('#tiles li');
      handler.wookmark(options);
    });

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-116372-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

  </script>

</body>
</html>
