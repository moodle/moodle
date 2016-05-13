Description of FlowPlayer import

Source code available at: https://github.com/flowplayer/flash

Our changes:
* the handling of flash content now it's done by PHP, checking url parameter to avoid security issues.
* we do not load the flow player if flash not present - this is much better
  for accessibility and flash incompatible devices - they may play mp3 or flv directly
* no splashscreens

skodak