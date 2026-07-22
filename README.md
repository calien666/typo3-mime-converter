[![Latest Stable Version](https://poser.pugx.org/calien/mime-converter/v/stable.svg)](https://packagist.org/packages/calien/mime-converter)
[![PHP Version Require](http://poser.pugx.org/calien/mime-converter/require/php)](https://packagist.org/packages/calien/mime-converter)
[![TYPO3 14.3](https://img.shields.io/badge/TYPO3-14.3-green.svg?style=flat-square)](https://get.typo3.org/version/14)
[![TYPO3 13.4](https://img.shields.io/badge/TYPO3-13.4-green.svg?style=flat-square)](https://get.typo3.org/version/13)

[![License](http://poser.pugx.org/calien/mime-converter/license)](https://packagist.org/packages/calien/mime-converter)
[![Total Downloads](https://poser.pugx.org/calien/mime-converter/downloads.svg)](https://packagist.org/packages/calien/mime-converter)
[![Monthly Downloads](https://poser.pugx.org/calien/mime-converter/d/monthly)](https://packagist.org/packages/calien/mime-converter)

# TYPO3 extension `mime_converter`

This extension provides automatic mime correction on uploading a file
inside TYPO3.

## Features

* Automatic image conversion
* Keeps EXIF data
* Extendable with own providers

## Requirements

Due to limitations in GraphicsMagick this extension works only with
ImageMagick. Ensure the ImageMagick command line tools (`convert` /
`identify`) are installed on your server. The PHP `imagick` extension is
no longer required — conversion runs through the ImageMagick binary.

If ImageMagick is not available (for example on a GraphicsMagick-only
setup, which does not ship a standalone `convert` / `identify`), the
extension stays inactive: uploads are stored unchanged instead of being
converted, and no file is falsely reported as broken.

## Installation

Install with your favour:

* [TER](https://extensions.typo3.org/extension/mime_converter/)
* Extension Manager
* composer

We prefer composer installation:

```bash
composer req calien/mime-converter
```

|                  | URL                                                        |
|------------------|------------------------------------------------------------|
| **Repository:**  | https://github.com/calien666/mime-converter                |
| **Read online:** | https://docs.typo3.org/p/calien/mime-converter/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/mime_converter/     |
