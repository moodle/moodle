VideoJS 5.11.8
--------------
https://github.com/videojs/video.js

Instructions to import VideoJS player into Moodle:

0. Checkout and build videojs source in a separate directory
1. copy 'build/temp/video.js' into 'amd/src/video.js'
2. copy 'build/temp/font/' into 'fonts/' folder
3. copy contens of 'images/' folder into 'pix/' folder
4. copy 'build/temp/video-js.css' into 'styles.css'
   Replace
     url("font/VideoJS.eot?#iefix")
   with
     url([[font:media_videojs|VideoJS.eot]])
   Search for other relative URLs in this file.
   Add stylelint-disable in the beginning.
   Add "Modifications of player made by Moodle" to the end of the styles file.
5. copy 'LICENSE' and 'lang/' into 'videojs/' subfolder
6. search source code of video.js for the path to video-js.swf,
   download it and store in this folder

Import plugins:

1. Checkout from https://github.com/videojs/videojs-youtube and build in a separate directory
2. copy 'dist/Youtube.js' into 'amd/src/Youtube.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video']
