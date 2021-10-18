VideoJS 7.10.0
--------------
https://github.com/videojs/video.js

Instructions to import VideoJS player into Moodle:

1. Download the latest release from https://github.com/videojs/video.js/releases
   (do not choose "Source code")
2. copy 'video.js' into 'amd/src/video-lazy.js'
   In the beginning of the js file replace
     define(['global/window', 'global/document']
   with
     define(['./window', './document']
3. copy 'font/' into 'fonts/' folder
4. copy 'video-js.css' into 'styles.css'
   Add /* stylelint-disable */ in the beginning.
   Maintain the css after  "/* Modifications of player made by Moodle: */" to the end of the styles file.
   Check status of:
   https://github.com/videojs/video.js/issues/2777
6. copy 'lang/' into 'videojs/' subfolder (so the result will be media/player/videojs/videojs/lang).

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

4. Download the latest release from https://github.com/HuongNV13/videojs-ogvjs/releases
   (do not choose "Source code")

5. Copy videojs-ogvjs.js into 'amd/src/videojs-ogvjs-lazy.js'
   In the beginning of the js file:

   Replace
     define(['video.js', 'OGVCompat', 'OGVLoader', 'OGVPlayer']
   with
     define(['media_videojs/video-lazy', './local/ogv/ogv']

   Replace
     function (videojs, OGVCompat, OGVLoader, OGVPlayer)
   with
     function (videojs, ogvBase)

   Replace
     var OGVCompat__default = /*#__PURE__*/_interopDefaultLegacy(OGVCompat);
     var OGVLoader__default = /*#__PURE__*/_interopDefaultLegacy(OGVLoader);
     var OGVPlayer__default = /*#__PURE__*/_interopDefaultLegacy(OGVPlayer);
   with
     var OGVCompat__default = /*#__PURE__*/_interopDefaultLegacy(ogvBase.OGVCompat);
     var OGVLoader__default = /*#__PURE__*/_interopDefaultLegacy(ogvBase.OGVLoader);
     var OGVPlayer__default = /*#__PURE__*/_interopDefaultLegacy(ogvBase.OGVPlayer);