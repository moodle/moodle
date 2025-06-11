## v1.4.2
- Removed error_log line.
- Fixed bug for students not being able to send a message due to context freezing check.

## v1.4.1
- Added a new table for to send keep track of messages sent to all students. When cron runs this will take the current enrollments of the course and then send the message. This fixes the issue of sending emails to students who had dropped the course or enrolled after it was scheduled.
- Added a setting to allow certain roles access to parts when the course is frozen.
- Fixed other bugs and warnings.

## v1.4.0
- Added two DB fields for status of message (whether it sent or not) and the ids of those it failed to send to
- Added alternative name fields for moodle 2.6 compatibility issues, and changed get_context_instance methods to context::instance_by_id

## v1.3.2

- adds German translation contributed by [jlackner](https://github.com/lsuits/quickmail/issues/86)

## v1.3.1

- adds form element setType statements missed in the previous release

## v1.3.0

- updates for 2.4 and 2.5 Moodle API changes
- Swedish translation
- Hebrew translation
- configurable FERPA settings
- Securing email attachments
- Fixup local css for fixed width themes


## v1.2.9

- Fixes attachment path on Windows servers [#49][49]

[49]: https://github.com/lsuits/quickmail/issues/49

## v1.2.8

- Upgrade from 1.9 [a70a5d][a70a5d] ([meyersh][meyersh])
- Russian translation [7a5ccc][7a5ccc] (Sergey Zolotykhin)
- Backing up and restoring of quickmail hostory [#39][39]
- Safe email log restoration: [#45][45]
- Fixes broken delete link for admins [4166c8][4166c8]
- Improved attachment filearea [#40][40]
- Type needs to be included in pagination [3333e6][3333e6]
- Fix upgrade failure point [#41][41] ([mackensen][mackensen])
- Use correct user time in history and drafts [#42][42]

[meyersh]: https://github.com/meyersh
[a70a5d]: https://github.com/lsuits/quickmail/commit/a70a5da1a2c58237078292e8798493643bb38427
[7a5ccc]: https://github.com/lsuits/quickmail/commit/7a5cccdff8a1b9d9db7a0a2c3c8e3055a8519e75
[4166c8]: https://github.com/lsuits/quickmail/commit/4166c828d531e4ef2538fbae2f156c49bb627cdb
[3333e6]: https://github.com/lsuits/quickmail/commit/3333e643606947254b5cb1cdf5beeb33b7ea1bb7
[40]: https://github.com/lsuits/quickmail/issues/40
[41]: https://github.com/lsuits/quickmail/pull/41
[42]: https://github.com/lsuits/quickmail/issues/42
[39]: https://github.com/lsuits/quickmail/pull/39
[45]: https://github.com/lsuits/quickmail/issues/45

## v1.2.7

- Removed dprecated code [#35][35] ([mackensen][mackensen])
- Install fields were causing problems on install [#34][34] ([mackensen][mackensen])
- Javascript was blocking Cancel [#33][33]
- Block now uses icons correctly [213ed0][213ed0]
- Uses the correct zip in attachments [200fb2][200fb2]

[mackensen]: https://github.com/mackensen
[213ed0]: https://github.com/lsuits/quickmail/commit/213ed09b58a065608d81df83005dccd4f8b6714d
[200fb2]: https://github.com/lsuits/quickmail/commit/200fb2e07d01c052a398c799d11607eed3f5ac64
[33]: https://github.com/lsuits/quickmail/issues/33
[34]: https://github.com/lsuits/quickmail/issues/34
[35]: https://github.com/lsuits/quickmail/issues/35

## v1.2.6

- Now uses $CFG->tempdir for the temp directory [741a64][741a64]
- Update French translation [#32][32] ([luiggisanso][luiggisanso])

[741a64]: https://github.com/lsuits/quickmail/commit/741a64546344ba1fb639df251f15a8fc2b0c34b4
[32]: https://github.com/lsuits/quickmail/pull/32

## v1.2.5

- Receive copy default setting [#31][31]
- Empty Signature defaults [#30][30]
- Increase Subject line [45a80cf][45a80cf]

[31]: https://github.com/lsuits/quickmail/issues/31
[30]: https://github.com/lsuits/quickmail/issues/30
[45a80cf]: https://github.com/lsuits/quickmail/commit/45a80cff9ee0f565fe2bd93ea720bbd0ef5897b8

## v1.2.4

- Additional French translation [#28][28] ([luiggisanso][luiggisanso])
- Style fix for Firefox browsers [#29][29]

[28]: https://github.com/lsuits/quickmail/pull/28
[29]: https://github.com/lsuits/quickmail/issues/29
[luiggisanso]: https://github.com/luiggisanso

## v1.2.3

- Install script differed from upgrade script

## v1.2.2

- Emergency Fix for alternate emails [#25][25]
- Embed image permission too strong for thick app clients [#26][26]
- Delete signatures, optional signature content [#27][27]

[25]: https://github.com/lsuits/quickmail/issues/25
[26]: https://github.com/lsuits/quickmail/issues/26
[27]: https://github.com/lsuits/quickmail/issues/27

## v1.2.1

- Added French translation (Luiggi Sansonetti)

## v1.2.0

- Fixed student permissions [#19][19]
- Horizontal scrollbars for email table [#17][17]
- Ability to configure alternate email address [#16][16]
- Embed images and other content in email and signature [#21][21]
- Option to prepend shortname or idnumber in email subject [#22][22]

[22]: https://github.com/lsuits/quickmail/pull/22
[21]: https://github.com/lsuits/quickmail/pull/21
[16]: https://github.com/lsuits/quickmail/pull/16
[19]: https://github.com/lsuits/quickmail/pull/19
[17]: https://github.com/lsuits/quickmail/pull/17

## v1.1.2

- Fixed problematic anonymous function between PHP versions [#7][7], [#8][8] ([eSrem][eSrem] and [Icheb][Icheb])
- Abiding by Moodle config directory permission [#9][9] ([abias][abias])

[7]: https://github.com/lsuits/quickmail/issues/7
[8]: https://github.com/lsuits/quickmail/issues/8
[9]: https://github.com/lsuits/quickmail/issues/9

[eSrem]: https://github.com/eSrem
[Icheb]: https://github.com/Icheb
[abias]: https://github.com/abias

## v1.1.0 and v1.1.1

- Added LSU attributions ([adamzap][adamzap])
- Fleshed out README ([adamzap][adamzap])

[adamzap]: https://github.com/adamzap

## v1.0.1

- Styles and decoration ([rrusso][rrusso])

[rrusso]: https://github.com/rrusso

## v1.0.0

- Initial release
