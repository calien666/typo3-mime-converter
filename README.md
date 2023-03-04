[![Latest Stable Version](https://poser.pugx.org/web-vision/mime-converter/v/stable.svg)](https://packagist.org/packages/web-vision/mime-converter)
[![PHP Version Require](http://poser.pugx.org/web-vision/mime-converter/require/php)](https://packagist.org/packages/web-vision/mime-converter)
[![TYPO3 12.2](https://img.shields.io/badge/TYPO3-12.2-green.svg?style=flat-square)](https://get.typo3.org/version/12)
[![TYPO3 11.5](https://img.shields.io/badge/TYPO3-11.5-green.svg?style=flat-square)](https://get.typo3.org/version/11)

[![License](http://poser.pugx.org/web-vision/mime-converter/license)](https://packagist.org/packages/web-vision/mime-converter)
[![Total Downloads](https://poser.pugx.org/web-vision/mime-converter/downloads.svg)](https://packagist.org/packages/web-vision/mime-converter)
[![Monthly Downloads](https://poser.pugx.org/web-vision/mime-converter/d/monthly)](https://packagist.org/packages/web-vision/mime-converter)

# TYPO3 extension `mime_converter`

This extension provides automatic mime correction on uploading a file
inside TYPO3.

## Features

* Automatic image conversion
* Keeps EXIF data
* Extendable with own providers

## Requirements

Due to limitations in GraphicsMagick this extension works only with
ImageMagick. Ensure, ImageMagick and PECL extension imagick are
installed on your server.

## Installation

Install with your favour:

* [TER](https://extensions.typo3.org/extension/mime_converter/)
* Extension Manager
* composer

We prefer composer installation:
```bash
composer req web-vision/mime-converter
```

|                  | URL                                                            |
|------------------|----------------------------------------------------------------|
| **Repository:**  | https://github.com/calien666/mime-converter                    |
| **Read online:** | https://docs.typo3.org/p/web-vision/mime-converter/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/mime_converter/         |
