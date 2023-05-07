![Moodle Plugin CI](https://github.com/kabalin/moodle-media_jwplayer/workflows/Moodle%20Plugin%20CI/badge.svg)

moodle-media_jwplayer (JW Player 8)
==================================

JW Player is the solution for seamless video playback across browsers and
media types. This media player plugin is brigning [all power of JW Player 8](https://www.jwplayer.com/html5-video-player/) into Moodle.

Using commercial version of JW Player requires paid subscription. Free trial
(non-commercial licensed) is available, which downgrades to limited functionality free edition after trial period is expired. Learn more at
[https://www.jwplayer.com/pricing/](https://www.jwplayer.com/pricing/). Open
source version of JW Player 8 is not supported yet.

Installation and configuration
------------

The player plugin installation is pretty strightforward. Plugin files need to be
placed in `./media/player/jwplayer` directory in Moodle, then you will need to go
through installation process as normal by loggining in as admin.

When the player plugin installation is completed, the plugin configuration
page will be displayed (alternatively you may access it via Site
Administration -> Plugins -> Media players). To start using
the player, library hosting method need to be choosen and configured (see below).

Once the player is configured, the final step would be to enable the player
on Manage media players page in Site Administration area and move it above
other players to give it a higher priority (or according to your preference).

### Cloud hosted library

It is recommended to use cloud-hosted player library if you need flexibility and
control over features and player layout through JWPlayer dashboard. You need
to specify library URL to use cloud-hosted method, which can be obtaned from
[Player Downloads & Keys](https://dashboard.jwplayer.com/#/players/downloads)
page of JWPlayer dashboard. In "Cloud Hosted Player Libraries" section of the
dashboard, select a player title from the dropdown menu and copy "Cloud Player
Library URL".

### Self-hosted library

You can use self-hosted method if you have Enterprise subscription. In this
case you need to download library from [Player Downloads &
Keys](https://dashboard.jwplayer.com/#/players/downloads) page of JWPlayer
dashboard, unpack it and place contents in `/media/player/jwplayer/jwplayer/`
directory in Moodle. This is where plugin will be looking for `jwplayer.js`
file when you choose self-hosted implementation in the plugin settings. Using
self-hosted methods requires a license key that also needs to be specified in
plugin settings.

### Open source edition

Open source version for non-commerical use of JWPlayer is [available on
GitHub](https://github.com/jwplayer/jwplayer). This is not supported yet by
this plugin, but code contributions are more than welcome
([#1](https://github.com/kabalin/moodle-media_jwplayer/issues/1)).

Usage
-----

Any media content added to Moodle through HTML editor (either using
the URL or media embedding dialog), as well as File/URL resource, will be
rendered and played using JW Player if the format is supported and enabled
in the plugin configuration. For more details on supported formats see
[Supported Video and Audio Formats Reference](https://support.jwplayer.com/articles/supported-video-and-audio-formats-reference)
on JW Player website<sup>*</sup>.

<sub><sup>*</sup> Although, it is not said explicitly, player has no issues playing `mov` and `ogg` video formats.</sub>

### Default player dimensions

The player default width and height is defined in "Common setting" area of
"Manage media players" page. Player size defined in JWPlayer dashboard for
cloud-hosted player is not used, as player settings provide flexibility to
define size and aspect ratio.

When playing audio files, player switches into Audio mode (only control bar is
shown) and default width is used.

### JW Player version

In cloud-hosted library mode, choosing specific player version is not
possible, the latest is always used.  However, in player configuration on the
Dashboard user can choose between Production and Beta release channels.

If self-hosted mode is used, choosing a different version is a matter of
downloading desired JW8 release from "Player Downloads & Keys" page and
replacing files in `./media/player/jwplayer/jwplayer` directory.

### Using in Moodle mobile app

It is currently not possible to use JWPlayer in Moodle mobile app. This media
plugin will gracefully fallback to html5 video/audio tag (even if media file
is provided as URL link) if you navigate to page containing video.

Advanced use
------------

### Global HTML attributes

[Global HTML
attributes](https://developer.mozilla.org/en/docs/Web/HTML/Global_attributes)
in the player link will be applied to the rendered player outer div tag. In
addition, attribures that start with _data-_ (but not _data-jwplayer-_) will
be applied to player's outer div tag.

### URL tag specific attributes

HTML attributes in the media file `<a>` tag that start with _data-jwplayer-_ prefix,
will be used as player configuration options. The possible options are:

_autostart, controls, height, mute, repeat, width, description, mediaid, subtitles_

For full description of each option, please refer to [configuration
reference](https://developer.jwplayer.com/jwplayer/docs/jw8-player-configuration-reference)
on JW Player website.

For example, `<a data-jwplayer-autostart="true"
href="https://some.stream.org/functional.webm">functional.webm</a>` will
make player start playing video automatically on page load.

#### Subtitles

You can use _data-jwplayer-subtitles_ attribute to add subtitles: `<a
href="https://some.stream.org/functional.mp4"
data-jwplayer-subtitles="English:
http://someurl.org/tracks/functional.txt"
data-jwplayer-description="some description">test subtitles</a>`

History and credits
-------------------

This fork is based on [JWPlayer 7 version of media
plugin](https://github.com/lucisgit/moodle-media_jwplayer), which is no longer
maintained (JWPlayer deprecated version 7 support on 15/10/20).

Upgrading plugin to version 8 has been funded by [Ecole hôtelière de Lausanne](https://www.ehl.edu/)
