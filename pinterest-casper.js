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
	casper.echo("Pinterest web scrapper v1.1");
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

casper.userAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X)');
casper.start('http://pinterest.com/'+user+'/pins/', function() {
    console.log("load pinterest web site");
    var html = this.getHTML();
    if (html.search('Oops')!=-1) {
        this.die('Oops message received : stop here.');
    }
});

casper.thenEvaluate(function() {

    var pTimerCounter = 1;
    var pLastCount = 0;
    var pPins = new Array();
    var pPinsIndexes = new Array();
    window.done = 0;
    window.data = new Array();

    var pTimer = window.setInterval(function() {
        
        console.log('loop()');
        var pUrls = $('.pinImageWrapper');
        var pDescriptions = $('.pinImg');
        var pBoard = $('.creditTitle');
    
        pLength = parseInt($('.padItems').css('height').replace('px', ''));
        console.log('pUrls length : '+ pLength + ' pLastCount : ' + pLastCount);
        if (pLength == pLastCount) {
            window.clearInterval(pTimer);
            window.done = 1;
    } else {           
           console.log('pUrls length : ' + pUrls.length);
       for ( var i=0 ; i < pUrls.length ; i++) { 
                var pPin = {};
                pPin['pin_page'] = 'http://www.pinterest.com'+$(pUrls[i]).attr('href');
                pPin['board'] = $(pBoard[i]).text();
                pPin['description'] = $(pDescriptions[i]).attr('alt');
                pPin['pin_thumbnail'] = $(pDescriptions[i]).attr('src');
                // search if pin already pushed
                
                if (!(pPin['pin_page'] in pPinsIndexes)) {
                    window.data.push(pPin); 
                    pPinsIndexes[pPin['pin_page']] = '';
                }
           }

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
