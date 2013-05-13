# Magento Store Locator
A store locator extension for Magento.

## Installation. 
This extension can be installed a few different ways:

### Using modman
        
    modman clone git://github.com/andrewkett/Digibrews_Locator.git

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
                "url": "git@github.com:andrewkett/Digibrews_Locator.git"
            },
            {
                "type": "git",
                "url": "git://github.com/phayes/geoPHP.git"
            }
        ],
        "require": {
            "andrewkett/magebrews_locator" : "dev-master"
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

[https://github.com/andrewkett/Digibrews_Locator/archive/master.zip](https://github.com/andrewkett/Digibrews_Locator/archive/master.zip)

You will also need to add the [geoPHP](https://github.com/phayes/geoPHP) library to the lib directory

## Configuration

After the extension is installed you will need to add a [google maps API key](https://developers.google.com/maps/documentation/javascript/tutorial#api_key) in the Locator Settings configuration tab.


## Licence
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

## Copyright
(c) 2013 Andrew Kett
