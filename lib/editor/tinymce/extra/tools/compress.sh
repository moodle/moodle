#!/bin/bash
java -jar yuicompressor.jar --line-break 1000 ../../tiny_mce/3.3.8/tiny_mce_src.js -o ../../tiny_mce/3.3.8/tiny_mce.js
java -jar yuicompressor.jar --line-break 1000 ../../tiny_mce/3.3.8/Popup.js -o ../../tiny_mce/3.3.8/tiny_mce_popup.js