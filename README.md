# Magento Store Locator

[![Build Status](https://travis-ci.org/andrewkett/Ak_Locator.png?branch=master)](https://travis-ci.org/andrewkett/Ak_Locator) [![Code Coverage](https://scrutinizer-ci.com/g/andrewkett/Ak_Locator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/andrewkett/Ak_Locator/?branch=master) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/andrewkett/Ak_Locator/badges/quality-score.png?s=ed69380af3f8cae9103d253d27e7c193fbe02914)](https://scrutinizer-ci.com/g/andrewkett/Ak_Locator/) [![Dependency Status](https://www.versioneye.com/user/projects/5309b07fec1375bb1b000013/badge.png)](https://www.versioneye.com/user/projects/5309b07fec1375bb1b000013) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/657284eb-b4cc-4d69-b110-2eed060b653d/mini.png)](https://insight.sensiolabs.com/projects/657284eb-b4cc-4d69-b110-2eed060b653d)


This extension provides the ability to add physical stores to your Magento website, and for a customer to find your stores using a geolocation search.

## Features

* Locations built with Magentos EAV system
* Google Maps used for frontend display and search geocoding
* Configurable search settings
* Extensible code

## Installation.
This extension can be installed with both modman and composer, however composer is the recommended method of installation.

### Using composer

add a composer.json file to your root directory

```javascript
{
    "repositories": [
        {
          "type":"composer",
          "url":"http://packages.firegento.com"
        }
    ],
    "require": {
        "andrewkett/locator" : "@stable"
    },
    "extra":{
        "magento-root-dir":"./",
        "magento-force":"true"
    }
}
```
then

```
composer.phar install
```

### Download and install manually.

[https://github.com/andrewkett/Ak_Locator/archive/master.zip](https://github.com/andrewkett/Ak_Locator/archive/master.zip)

When installing manually, the [geoPHP](https://github.com/phayes/geoPHP) library must be added to the lib directory manually.

## Configuration

Once installed you will need to add a [google maps API key](https://developers.google.com/maps/documentation/javascript/tutorial#api_key) in the Locator Settings configuration tab.

## How to Contribute

- Fork and edit
- Test locally
- Submit pull request for consideration

## Licence
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

## Copyright
(c) 2015 Andrew Kett

Disclaimer: This extension is now reasonably stable and has been used in production websites, however it is provided "as is" and there are no guarantees that it will work for your site.
