# moodle-block_slider

## Description:
**Slider block**

With this block You can create slideshows with images.

It should work with all bootstrap based themes.

You can choose between two types of slider library, control effects and settings.

## Installation:
Install using Moodle backend panel as described on http://docs.moodle.org/en/Installing_plugins

Download, extract, and upload the "slider" folder into moodle/blocks/

## Supported Moodle versions:
I have tested plugin on clean install of Moodle 3.1 - 3.8

## Version history:

### 0.4.0
* when in responsive mode on BX Slider images have now 100% width
* optimized css for responsive display
* new feature - display image description
* new feature - allow to hide captions until mouse hover over slider
* fixed issue with no slider when using SlideJS
* small fixes in code

### 0.3.3
* minor fixes in privacy subsystem implementation

### 0.3.2
* added information with filter_slider code above slides table, when filter_slider is enabled
    https://github.com/limsko/moodle-block_slider/issues/14
* fixed issues
    https://github.com/limsko/moodle-block_slider/issues/12
    https://github.com/limsko/moodle-block_slider/issues/13
* fixed lack of course link in navbar when managing slides inside course
    
### 0.3.1
* fixed missing prev button graphics on bxslider

### 0.3.0
* added new slider js library - BX Slider
https://github.com/stevenwanderski/bxslider-4
* now you can choose between two slider libraries
* display slide titles (captions) using BX Slider
* compatible with filter_slider - https://github.com/limsko/moodle-filter_slider

### 0.2.2
* fixed error during image updating
* removed img-thumbnail from slider image class

### 0.2.1
* fixed bug #11 - Auto-play running when disabled under configuration
* fixed bug #10 - Pagination button stay visible when disabled under configuration
* small improvements

### 0.2.0
* multiple instances on single page can be added
* each slide is configurable
* each slide has optional: title, desc, href
* added support for Moodle 3.6.x, 3.7.x, 3.8.x
* bugfixes

### 0.1.4
* fixed polish translation
* added help for setting width and height

### 0.1.3
* plugin is supported by Moodle 3.1, 3.2, 3.3, 3.4, 3.5
* now using AMD format Javascript Modules

### 0.1.2
* added support for Moodle 3.0
* now allowed multiple instances of block

### 0.1.1
* fixed wrong risks in db/access
* fixed PHP notice when trying to get not yet set config property
* deleted unnecessary functions from code
* used moodle_url::make_file_url() to get file list instead of SQL
* removed font-awesome - using Moodle core theme icons to navigate forward/backward
* added option to disable auto-play
* tested and working on Moodle 2.9

### 0.1.0
* First release




