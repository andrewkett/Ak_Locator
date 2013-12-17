# Magento Store Locator
A store locator extension for Magento.

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/andrewkett/MageBrews_Locator/badges/quality-score.png?s=ca4c9eec21cd5f0d87679426306fbbd8a864b5ff)](https://scrutinizer-ci.com/g/andrewkett/MageBrews_Locator/) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/68c3fc50-74f1-4d50-a4b2-81db51fd13ca/mini.png)](https://insight.sensiolabs.com/projects/68c3fc50-74f1-4d50-a4b2-81db51fd13ca) [![Build Status](https://travis-ci.org/andrewkett/MageBrews_Locator.png?branch=master)](https://travis-ci.org/andrewkett/MageBrews_Locator)

## Installation. 
This extension can be installed a few different ways:

### Using modman
        
    modman clone git://github.com/andrewkett/MageBrews_Locator.git

### Using composer 
    
add a composer.json file to your root directory

    {
        "minimum-stability":"dev",
        "repositories": [
            {
              "type":"composer",
              "url":"http://packages.firegento.com"
            },
            {
                "type": "git",
                "url": "git@github.com:andrewkett/MageBrews_Locator.git"
            }
        ],
        "require": {
            "magebrews/locator" : "dev-master"
        },
        "extra":{
            "magento-root-dir":"./",
            "magento-force":"true"
        }
    }

then 

    composer.phar install

If you are using composer and your dependencies are not being installed to the lib directory, copy the [geoPHP](https://github.com/phayes/geoPHP) directory into your lib directory

### Download and install manually.

[https://github.com/andrewkett/MageBrews_Locator/archive/master.zip](https://github.com/andrewkett/MageBrews_Locator/archive/master.zip)

You will also need to add the [geoPHP](https://github.com/phayes/geoPHP) library to the lib directory

## Configuration

After the extension is installed you will need to add a [google maps API key](https://developers.google.com/maps/documentation/javascript/tutorial#api_key) in the Locator Settings configuration tab.


## Licence
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

## Copyright
(c) 2013 Andrew Kett

Disclaimer: This extension under active development and is not yet at a stable release. It is provided "as is", it may be buggy or completely break your website. 
