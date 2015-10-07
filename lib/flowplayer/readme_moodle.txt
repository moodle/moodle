Description of FlowPlayer import

Our changes:
* the handling of flash content now it's done by PHP, checking url parameter to avoid security issues.
* we do not load the flow player if flash not present - this is much better
  for accessibility and flash incompatible devices - they may play mp3 or flv directly
* no splashscreens

TODO:
* switch to git repo once flowplayer decides to use better version control system

skodak