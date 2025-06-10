## v1.1.8

- Updates deprecated context functions.
- adds GPL declarations
- adds setType statements missed in the upgrade to Moodle 2.5.

## v1.1.7

- Ships with jQuery [#43][43]
- Fixes course creation duplicates [#44][44]
- Split validity would cause side-effects [31886][31886]

[31886]: https://github.com/lsuits/cps/commit/3188698260a42872cbfe134c8c5e077ac9a1c451
[44]: https://github.com/lsuits/cps/issues/44
[43]: https://github.com/lsuits/cps/issues/43

## v1.1.6

- IE js fix [2ac17e][2ac17e]
- Crosslist reverse boolean [e259ed][e259ed]
- User preferences; name change [#42][42]
- Documentation, help links, icons, etc [#36][36]
- UES people field ordering [037b67][037b67]
- UES people field highlighting [8c6517][8c6517]
- Field cat setting if one exists [#37][37]
- Completion info added to Creation default settings [#38][38]

[2ac17e]: https://github.com/lsuits/cps/commit/2ac17e85e5e61d436648c93988532cb77d979a21
[e259ed]: https://github.com/lsuits/cps/commit/e259ed74b0e45bfc003392e1a18251b8c5633dc9
[037b67]: https://github.com/lsuits/cps/commit/037b670a0b2cf09c00bceea962573cefac9185ef
[8c6517]: https://github.com/lsuits/cps/commit/8c651765138d5c8255208fc1f88cdd89aafa90a0
[42]: https://github.com/lsuits/cps/pull/42
[37]: https://github.com/lsuits/cps/issues/37
[38]: https://github.com/lsuits/cps/issues/38
[36]: https://github.com/lsuits/cps/issues/36

## v1.1.5

- Team request link is correct [#34][34]
- Keypad ID, is now populated on use profile [#33][33]
- Material course creation uses correct format [#31][31]
- Instructor can override some course creation settings [#32][32]
- Added plugin icons, and action icons [e07512][e07512]

[34]: https://github.com/lsuits/cps/issues/34
[33]: https://github.com/lsuits/cps/issues/33
[32]: https://github.com/lsuits/cps/issues/32
[31]: https://github.com/lsuits/cps/issues/31
[e07512]: https://github.com/lsuits/cps/commit/e07512e7667106c7cf2e2c2cf9f78a5c4860fa15db

## v1.1.4

- $.ajaxError will report network issues [#30][30]
- Split and team teach avoid semester colission [#29][29]
- Cross-list semester fix [#28][28]
- Improperly formatting Degree cacndidates [40cdf4][40cdf4]
- Restore entire course config from previous course [5c2de0][5c2de0]

[28]: https://github.com/lsuits/cps/issues/28
[29]: https://github.com/lsuits/cps/issues/29
[30]: https://github.com/lsuits/cps/issues/30
[5c2de0]: https://github.com/lsuits/cps/commit/5c2de03372123aeac64d44e4a356163a512cf802
[40cdf4]: https://github.com/lsuits/cps/commit/40cdf4391a74905ff00e1fa45c064403bab09670

## v1.1.3

- Better simple restore handling [20cb74][20cb74]
- Material course creation creates course categories [#27][27]
- Material course language change [feb157][feb157]

[feb157]: https://github.com/lsuits/cps/commit/feb157d483dd30ada59624cddaf84cbc9a0c10cb
[20cb74]: https://github.com/lsuits/cps/commit/20cb748699460fa2bcae13b22c74d7954523493b
[27]: https://github.com/lsuits/cps/issues/27

## v1.1.2

- Removed explicit check for LAW semester on section process [#26][26]

[26]: https://github.com/lsuits/cps/issues/26

## v1.1.1

- Moodle profile fields for anonymous numbers [#25][25]
- Anonymous numbers are handled in Peoepl and Meta viewer [1b60c2][1b60c2]
- Added user profile link on User Data Viewer [3d99d4][3d99d4]

[25]: https://github.com/lsuits/cps/issues/25
[1b60c2]: https://github.com/lsuits/cps/commit/1b60c2982844753814e15923e9e6ce3a16bcc180
[3d99d4]: https://github.com/lsuits/cps/commit/3d99d4bb52b1d25135e28062e6addf932c7ffa42

## v1.1.0

- UES People Integration [488bbd3][488bbd3]
- Clean up internal form library [c0ad42c](https://github.com/lsuits/cps/commit/c0ad42cb01c1f4c8815a3ddde13f647cd778efcc)
- Add Styles [3636dc50](https://github.com/lsuits/cps/commit/3636dc50b134ef61558ff5c7cb6d2abe3f9d22a8)

[488bbd3]: https://github.com/lsuits/cps/commit/488bbd3dff5fc4c6c9cda94ccf25d068de9c24b0

## v1.0.0

- Public open source release

## v0.0.6 (Snapshot)

- Added sport filtering to the User Data Viewer [#20](https://github.com/lsuits/cps/issues/20)
- Refactors event handlers [#19](https://github.com/lsuits/cps/issues/19)
  - Add Simple Restore integration [#17](https://github.com/lsuits/cps/issues/17)
  - Add UES Meta Viewer integration [#18](https://github.com/lsuits/cps/issues/18)
- Makes use of UES DAO DSL [#16](https://github.com/lsuits/cps/issues/16)
- Same instructor promotion details are handled [#15](https://github.com/lsuits/cps/issues/15)

## v0.0.5 (Snapshot)

- On primary change, enrollment should be dropped [#14](https://github.com/lsuits/cps/issues/14)

## v0.0.4 (Snapshot)

- Clean instructor settings on Release [#9](https://github.com/lsuits/cps/issues/9)
- Team requests shells are now admin configurable [#10](https://github.com/lsuits/cps/issues/10)
- Some admin settings were ignored [#13](https://github.com/lsuits/cps/issues/13)

## v0.0.3 (Snapshot)

- Creation / enroll entries are now sorting correctly
- Creation / enroll application is immediate [#8](https://github.com/lsuits/cps/issues/8)

## v0.0.2 (Snapshot)

- Fixed typo's in Admin settings [#7](https://github.com/lsuits/cps/issues/7)
- Small bug fix when processing split entries

## v0.0.1 (Snapshot)

- Initial Release (see [wiki](https://github.com/lsuits/cps/wiki) for more details)
