/* YUI 3.9.0 (build 5827) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("uploader",function(e,t){var n=e.config.win;n&&n.File&&n.FormData&&n.XMLHttpRequest?e.Uploader=e.UploaderHTML5:e.SWFDetect.isFlashVersionAtLeast(10,0,45)?e.Uploader=e.UploaderFlash:(e.namespace("Uploader"),e.Uploader.TYPE="none")},"3.9.0",{requires:["uploader-html5","uploader-flash"]});
