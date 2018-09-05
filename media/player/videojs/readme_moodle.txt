VideoJS 6.3.2
-------------
https://github.com/videojs/video.js

Instructions to import VideoJS player into Moodle:

1. Download the latest release from https://github.com/videojs/video.js/releases
   (do not choose "Source code")
2. copy 'video.js' into 'amd/src/video-lazy.js'
3. copy 'font/' into 'fonts/' folder
4. copy 'video-js.css' into 'styles.css'
   Replace
     url("font/VideoJS.eot?#iefix")
   with
     url([[font:media_videojs|VideoJS.eot]])
   Search for other relative URLs in this file.
   Add stylelint-disable in the beginning.
   Add "Modifications of player made by Moodle" to the end of the styles file.
   Check status of:
   https://github.com/videojs/video.js/issues/2777
6. copy 'LICENSE' and 'lang/' into 'videojs/' subfolder

Import plugins:

1. Copy https://github.com/videojs/videojs-youtube/blob/master/dist/Youtube.js into 'amd/src/Youtube-lazy.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video-lazy']

2. Download the latest release from https://github.com/videojs/videojs-flash
   Run "npm install"
   Copy 'dist/videojs-flash.js' into 'amd/src/videojs-flash-lazy.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video-lazy']

3. Download https://github.com/videojs/video-js-swf/blob/master/dist/video-js.swf
   and place it into 'videojs/video-js.swf'