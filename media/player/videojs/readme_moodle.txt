VideoJS 8.10.0
--------------
https://github.com/videojs/video.js

Instructions to import VideoJS player into Moodle:

1. Download the latest release from https://github.com/videojs/video.js/releases
   (do not choose "Source code")
2. copy 'video.js' into 'amd/src/video-lazy.js'
3. copy 'font/' into 'fonts/' folder
4. copy 'video-js.css' into 'styles.css'
   Add /* stylelint-disable */ in the beginning.
   Maintain the css after  "/* Modifications of player made by Moodle: */" to the end of the styles file.
   Check status of:
   https://github.com/videojs/video.js/issues/2777
6. copy 'lang/' into 'videojs/' subfolder (so the result will be media/player/videojs/videojs/lang).

Import plugins:
YouTube Playback Technology for VideoJS 3.0.1
---------------------------------------------
https://github.com/videojs/videojs-youtube

Instructions to import YouTube Playback Technology into Moodle:
1. Copy https://github.com/videojs/videojs-youtube/blob/master/dist/Youtube.js into 'amd/src/Youtube-lazy.js'
   In the beginning of the js file replace
     define(['videojs']
   with
     define(['media_videojs/video-lazy']

Ogv.js Playback Technology for VideoJS 1.0.0
---------------------------------------------
https://github.com/HuongNV13/videojs-ogvjs

Instructions to import Ogv.js Playback Technology into Moodle:
1. Download the latest release from https://github.com/HuongNV13/videojs-ogvjs/releases
   (do not choose "Source code")

2. Copy Videojs-Ogvjs.amd.js into 'amd/src/videojs-ogvjs-lazy.js'
   In the beginning of the js file:

   Replace
     define(['video.js', 'ogv']
   with
     define(['media_videojs/video-lazy', './local/ogv/ogv']
