pinterest
=========

This project allows to
- Web scrap your pinterest data
- Displays your pins very elegantly with WookMark jQuery plugin and lightbox.

I assume that you have the following packages installed in your path.
- casperjs
- imagemagick (convert command) to generate the thumbnails

Cookbook

Run the following scripts to prepare the data
    
    casperjs pinterest-casper.js <user_name>

It will generate a json file with all your pins data. 
    
    php pinterest-webscraper.php

It will import original pins in pins folder and will generate the thumbnails for WookMark

That's all !

Feel free to drop me a line if you like this project or have some suggestions. (lefebvre.jf@gmail.com)

