pinterest
=========

A PHP script displaying Pinterest pins of a user using WookMark jQuery plugin


I assume that you have the following packages installed in your path.
- casperjs
- imagemagick (convert command) to generate the thumbnails

Cookbook

- install pinterest
    
    casperjs pinterest-bot.js <user_name>
This will generate a json file with all your pins data
    
    php webscrap-data.php
This will extract original images of your pins and will create the thumnails for WookMark




