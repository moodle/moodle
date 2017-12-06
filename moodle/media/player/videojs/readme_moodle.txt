VideoJS 5.18.4
--------------
https://github.com/videojs/video.js

Instructions to import VideoJS player into Moodle:

1. Download the latest release from https://github.com/videojs/video.js/releases in a separate directory
2. copy 'dist/video.js' into 'amd/src/video-lazy.js'
3. copy 'dist/font/' into 'fonts/' folder
4. copy 'dist/video-js.css' into 'styles.css'
   Replace
     url("font/VideoJS.eot?#iefix")
   with
     url([[font:media_videojs|VideoJS.eot]])
   Search for other relative URLs in this file.
   Add stylelint-disable in the beginning.
   Add "Modifications of player made by Moodle" to the end of the styles file.
   Check status of:
   https://github.com/videojs/video.js/issues/2777
6. copy 'LICENSE', 'dist/video-js.swf' and 'dist/lang/' into 'videojs/' subfolder

Import plugins:

1. Copy https://github.com/videojs/videojs-youtube/blob/master/dist/Youtube.js into 'amd/src/Youtube-lazy.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video-lazy']
