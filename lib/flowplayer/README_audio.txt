Version history:

3.2.10
------
- #575 send the start event after begin
- #569 if the playlist has been reset but the audio has been already buffered, set the duration and start event.
- #582 fixes for metadata events dispatching in playlists and when replaying same audio item, cleanup duration updating once download has completed,
fixes for clearing the previous cover image display.
- #611 close the channel and sound on stream not found errors.

3.2.9
-----
- #501 fixes to dispatch start state correctly.
- #501 use the sound channel to listen for a complete event to finish correctly.

3.2.8
-----
Fixes:
- fixed to dispatch onStart only once
- the duration is now available in the clip when onStart is dispatched
- audio duration was estimated to be too long, issue #323
- duration not available in the onMetaData event, issue #278
- now dispatches error 200 when the mp3 URL does not respond, issue #334
- #428 regression issue calculating the initial duration, was returning too small for the duration tracker.
- #428 when fully downloaded ID3 is resent and update the duration.
- #475 if we have a clip duration set, dispatch start or else wait until the duration is estimated. Start required for beginning duration tracker.

3.2.3
-----
Fixes:
- Fixed to dispatch onStart when the mp3 file does not contain a id3 tag
- Now uses baseUrl if that is given

3.2.2
-----
Fixes:
- Now can download cover images from different domains without cross-domain security errors

3.2.1
-----
Changes:
- Supports cover images via a 'coverImage' configuration option
- now works with securestreaming plugin
Fixes:
- fix to work properly if accessing the ID3 tag fails because Flash security prevents it
- works better if the file does not have the ID3 tag

3.2.0
-----
- added a new plugin event "onDuration" that is dispatched whenever a new duration value is estimated and the
clip.duration value was changed. The new duration value is passed as event argument.

3.1.3
-----
- added timeProvider setter as required by the changed StreamProvider interface
- now checks the crossdomain.xml file to allow reading of the ID3 tag when this file is present in the domain
  hosting the audio file

3.1.2
-----
- compatible with the new ConnectionProvider and URLResolver API

3.1.1
-----
Fixes:
- calling closeBuffering() after the audio had finished caused an exception

3.1.0
-----
- compatibility with core 3.1 StreamProvider interface

3.0.4
-----
- fixed to stop audio when stop() is called

3.0.3
-----
- changed to recalculate the duration until the end of the file has been reached,
  this is needed to correctly estimate the duration of variable bitrate MP3's 

3.0.2
-----
- dispatches the LOAD event when initialized (needed for flowplayer 3.0.2 compatibility)
- fixed crashes of Mac  Safari when navigating out of a page that had a playing audio

3.0.1
-----
- First public beta release
