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
      <p>Hi ! I'm <a href="http://pinterest.com/iamjeff75/" target="_blank">iamjeff75</a> on Pinterest, here is all my inspirational pins retrieved with my web scrapping code.</p>
    </header>
    <div id="main" role="main">

      <ul id="tiles">
<?php

require_once 'pinterest.php';

$pinterest = new Pinterest();
$pins = array();
if (file_exists('pinterest.json')) {
  $content = file_get_contents('pinterest.json');
  $pins = unserialize($content);
} else {
  $pins = $pinterest->getPins('iamjeff75');
  $data = serialize($pins);
  file_put_contents('pinterest.json', $data);
}

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
  <script type="text/javascript">
    $('#tiles').imagesLoaded(function() {
      // Prepare layout options.
      var options = {
        autoResize: true, // This will auto-update the layout when the browser window is resized.
        container: $('#main'), // Optional, used for some extra CSS styling
        offset: 2, // Optional, the distance between grid items
        itemWidth: 600 // Optional, the width of a grid item
      };
      
      // Get a reference to your grid items.
      var handler = $('#tiles li');
      
      // Call the layout function.
      handler.wookmark(options);
      
      // Capture clicks on grid items.
      handler.click(function(){
        // Randomize the height of the clicked item.
        var newHeight = $('img', this).height() + Math.round(Math.random()*300+30);
        $(this).css('height', newHeight+'px');
        
        // Update the layout.
        handler.wookmark();
      });
    });
  </script>
  
</body>
</html>
