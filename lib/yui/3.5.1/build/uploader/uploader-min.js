/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("uploader",function(b){var a=b.config.win;if(a&&a.File&&a.FormData&&a.XMLHttpRequest){b.Uploader=b.UploaderHTML5;}else{if(b.SWFDetect.isFlashVersionAtLeast(10,0,45)){b.Uploader=b.UploaderFlash;}else{b.namespace("Uploader");b.Uploader.TYPE="none";}}},"3.5.1",{requires:["uploader-flash","uploader-html5"]});