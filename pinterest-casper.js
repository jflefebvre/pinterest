// pinterest webscrapper - casperjs script
// @author  Jean-François Lefebvre (hello@e-volution.be)
// @Date    13/12/2013
// @version 0.9

var start = new Date().getTime(); // store date to calculate time of execution

var casper = require('casper').create({
    logLevel: "debug",              // Only "info" level messages will be logged
    verbose: true                   // log messages will be printed out to the console
});

if (casper.cli.args.length!=1) {
	casper.echo("Pinterest web scrapper v0.9");
	casper.echo("pinterest account is missing");
	casper.echo("Example : casperjs pinterest-casper.js iamjeff75");
	casper.exit();	
}

var user = casper.cli.args[0];

var utils = require('utils');
var fs = require('fs'); 
var content = '';

casper.on('remote.message', function(msg) {
    // this.echo('remote message caught: ' + msg);
});

casper.start('http://pinterest.com/'+user+'/pins/', function() {
    console.log("load pinterest web site");
});

casper.thenEvaluate(function() {

    var pTimerCounter = 1;
    var pLastCount = 0;
    var pPins = new Array();
    window.done = 0;
    window.data = {sucess:true};

    var pTimer = window.setInterval(function() {
        
        var pUrls = $('.pinImageWrapper');
        var pLength = pUrls.length;
        var pDescriptions = $('.pinImg');
        var pBoard = $('.creditTitle');
    
        console.log('pUrls length : '+ pLength + ' pLastCount : ' + pLastCount);
        if (pLength == pLastCount) {
    
           window.done = 1;
           
           for(var i=0 ; i<pUrls.length ;i++) { 
                var pPin = {};
                pPin['href'] = 'http://www.pinterest.com'+$(pUrls[i]).attr('href');
                pPin['board'] = $(pBoard[i]).text();
                pPin['description'] = $(pDescriptions[i]).attr('alt');
                pPin['pin_page'] = $(pDescriptions[i]).attr('src');

                pPins.push(pPin); 
           }

           window.data = pPins;
           window.clearInterval(pTimer);

        } else {
            pLastCount = pLength;    
            window.document.body.scrollTop = document.body.scrollHeight; 
            pTimerCounter++;
        }

    }, 2000);

});

casper.waitFor(function() {
    return this.getGlobal('done') === 1;
}, function() {
    var jsonFormatData = JSON.stringify(this.getGlobal('data')); 
    fs.write('pinterest.json', jsonFormatData); 
    this.echo('Data saved in pinterest.json');

    var end = new Date().getTime();
    var time = end - start;
    this.echo('Data retrieved in : ' + time);

    this.exit();
}, function timeout() { 

}, 300000); // timeout of 5 minutes of execution

casper.run();
