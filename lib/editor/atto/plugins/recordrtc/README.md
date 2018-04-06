# RecordRTC Atto plugin for Moodle

[![Scrutinizer Code
Quality](https://scrutinizer-ci.com/g/blindsidenetworks/moodle-atto_recordrtc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/blindsidenetworks/moodle-atto_recordrtc/?branch=master)
[![Build
Status](https://scrutinizer-ci.com/g/blindsidenetworks/moodle-atto_recordrtc/badges/build.png?b=master)](https://scrutinizer-ci.com/g/blindsidenetworks/moodle-atto_recordrtc/build-status/master)

### Features

Add audio and video annotations to text, anywhere an Atto text editor is present. This plugin adds buttons for recording audio or video (with audio) to the editor's toolbar. Using WebRTC technologies, all recording is done instantly in the browser, using nothing but HTML5 and JavaScript (no Flash!). After recording, users can embed the annotation directly into the text they are currently editing. The recording will appear as an audio or video player in the published writing.

### Installation

There are currently two ways to install the plugin:

1. Installing via zip file:

   * Download the project's repository as a zip archive from GitHub: https://github.com/blindsidenetworks/moodle-atto_recordrtc/archive/master.zip
   * In Moodle, go to `Site administration` > `Plugins` > `Install plugins`
   * Under the `Install plugin from ZIP file` section, either select the above archive, or drag and drop it into the specified box on the page
   * Click the installation button


2. Installing manually (if the user does not have necessary permissions for installing the first way):

   * Navigate to `moodle_root_path/lib/editor/atto/plugins`, where `moodle_root_path` is the location where Moodle is installed (ex.: `/var/www/html/moodle`)
   * Execute `sudo git clone https://github.com/blindsidenetworks/moodle-atto_recordrtc.git recordrtc`
   * Log into a Moodle account with administration capabilities
   * A screen should appear asking the install the plugin, similar to above

Soon, there will also be the possibility to install easily via the Moodle Plugins Directory.

### Usage

To use the plugin, just click on one of the recording buttons (either the microphone or the video camera), and a popup will appear with a big "Start Recording" button. When clicked, the browser will probably ask for permission to use the webcam/microphone.

![Recording buttons](https://user-images.githubusercontent.com/2160185/28581382-0cfd2078-7130-11e7-8181-0d545287a154.png)  

After the recording starts, a timer will begin counting down, indicating how much time is left to record; when the timer hits 0, the recording will automatically stop (this will also happen if approaching the maximum upload size determined in the server settings).

![Recording started](https://user-images.githubusercontent.com/2160185/28581749-1ab016d4-7131-11e7-919f-d2756da5aec3.png)

When the recording is finished, the user can play it back to see/hear if it is what they want. To embed the file, the user must click "Attach Recording as Annotation". A dialog box will pop up asking the user what the link should appear as in the text editor. After that, the file gets embedded right where the cursor was in the text.

![Name the annotation](https://user-images.githubusercontent.com/2160185/28582017-fe3a64ea-7131-11e7-80ce-3b68bce23cb5.png)

![Annotation in editor](https://user-images.githubusercontent.com/2160185/28582039-0d9a45a4-7132-11e7-8d45-8400a0ef2dd8.png)

### Configuration

The plugin can be configured during the initial install, and later by navigating to `Site administration` > `Plugins` > `Text editors` > `Atto HTML editor` > `RecordRTC`. The administrator can:

* Allow the users to record only audio, only video, or both (changing the buttons that appear in the editor toolbar)
* Change the target bitrate of recorded audio
* Change the target bitrate of recorded video
* Set the recording time limit, to control maximum recording size

### Common problems

* **For developers**: If trying to update Bowser or Adapter.js dependencies for the project, it is necessary to replace the named definition at the top of the file with an anonymous one, like so (for Bowser):  

  *Old code*:
  ```
  !function (root, name, definition) {
    if (typeof module != 'undefined' && module.exports) module.exports = definition()
    else if (typeof define == 'function' && define.amd) define(name, definition)
    else root[name] = definition()
  }(this, 'bowser', function () {
  ```
  *New code*:
  ```
  define([], function() {
  ```

  Or so (for Adapter.js):  

  *Old code*
  ```
  (function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.adapter = f()}})(function(){
  ```
  *New code*
  ```
  define([], function() {
  ```
* The default maximum size of uploads in PHP is very small, it is recommended to set the `upload_max_filesize` setting to `40M` and the `post_max_size` setting to `50M` for a time limit of 2:00 to avoid getting an alert while recording
* The filesize of recorded video for Firefox will likely be twice that of other browsers, even with the same settings; this is expected as it uses a different writing library for recording video. The audio filesize should be similar across all browsers
