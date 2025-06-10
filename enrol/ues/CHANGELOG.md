## v3.1.0
- Updated for Moodle 3.7 compatability

## v2.0.0

- Subplugins are now supported through events [#31][31]
  - Fake enrollment provider has [moved][ues_fake]
  - LSU enrollment provider has [moved][ues_lsu]
- jQuery dependency has been removed [db4dac][db4dac]
- Appropriately handles teacher drops [f2454d][f2454d]

[31]: https://github.com/lsuits/ues/pull/31
[db4dac]: https://github.com/lsuits/ues/commit/db4dacaaa9b0781f4a44f0e60a60e6e2bb14975c
[f2454d]: https://github.com/lsuits/ues/commit/f2454db932dab86402081af70c36cb31a27b10d7
[ues_fake]: https://github.com/lsuits/ues_fake_provider
[ues_lsu]: https://github.com/lsuits/ues_lsu_enrollment

## v1.1.6

- [LSU Provider][lsu] now process user data for simultaneous semesters [a3412d][a3412d]
- [LSU Provider][lsu] now pulls student audit information [a91cfe][a91cfe]
- Organize event firing for insertion notifications [bd46f2][bd46f2]
- Adds `not_in` dao dsl word [7414cf][7414cf]
- Adds join support for SQL builder [cff6b8][cff6b8]

[bd46f2]: https://github.com/lsuits/ues/commit/bd46f201bff12793c898458f038a06cecaaa6738
[a91cfe]: https://github.com/lsuits/ues/commit/a91cfe0c3a1546685b57e2c3e5eaed928c828f32
[cff6b8]: https://github.com/lsuits/ues/commit/cff6b86969e4dc36e48384efd47be0e155befdfb
[7414cf]: https://github.com/lsuits/ues/commit/7414cfc7ac918d92ea623b952845966bf449c504
[a3412d]: https://github.com/lsuits/ues/commit/a3412d5853617cddc211aa8a2d6855e4bca83839

## v1.1.5

- Fixes group DB trap on pending unenrollments [7abb00][7abb00]
- Fixes blind pending flag reset [deb369][deb369]

[7abb00]: https://github.com/lsuits/ues/commit/7abb0061d1bb1b94cb92ed7028d7d0dacf9c7695
[deb369]: https://github.com/lsuits/ues/commit/deb369738a77bc49e7552d391a3425ea0b7ba1aa

## v1.1.4

- [LSU provider][lsu] gets configurable service ids [#30][30]
- Ability to ignore semesters in processing [9dc458][9dc458]
- Force semester end (when grades are due) dates [1f835f][1f835f]
- Build a semester tree with `merge_section` on `ues_semester` [fd0d55][fd0d55]
- Fixes improper DB aliases on parameter building [9f0100][9f0100]
- `$plugin_name` is now in the correct scope [e2dc62][e2dc62]

[30]: https://github.com/lsuits/ues/issues/30
[fd0d55]: https://github.com/lsuits/ues/commit/fd0d559dd4f1b8835c41dcae355ce53da033f252
[9f0100]: https://github.com/lsuits/ues/commit/9f0100c5c1f19838d0a30fa2d797297accf4c693
[e2dc62]: https://github.com/lsuits/ues/commit/e2dc62eb747157755ba9f1aaff9a8cf1c755d8a3
[1f835f]: https://github.com/lsuits/ues/commit/1f835f7949e7360ec0c212c8f9e8d4ffc346a9b7
[9dc458]: https://github.com/lsuits/ues/commit/9dc458630791952a86fbb43178f831160ba449ad

## v1.1.3

- [lsu provider][lsu] section source handles LAW categories correctly [9612e7][9612e7]
- Publicize the `manifest_category` for outside modules [18a5c2][18a5c2]
- Allows user's default language creation [fed79a][fed79a]

[fed79a]: https://github.com/lsuits/ues/commit/fed79a2fa408e7e3130d0450cb6e5d0294887b43
[9612e7]: https://github.com/lsuits/ues/commit/9612e7a41d997b1f39de934f7b3961e087ec958a
[18a5c2]: https://github.com/lsuits/ues/commit/18a5c2e111ae64945a0b34c6c0a176162054a123

## v1.1.2

- [lsu provider][lsu] Properly handles LAW enrollments [#28][28]
- Saner `ues_section::from_course` default, and short circuit [e12c8de][e12c8de]

[28]: https://github.com/lsuits/ues/issues/28
[e12c8de]: https://github.com/lsuits/ues/commit/e12c8dec65c6e7b5c0dbc0500d2267d443af3926)

## v1.1.1

- Automatic error handler only runs if not currently running [#25](https://github.com/lsuits/ues/issues/25)
- Restrict course form fields
- Replace course edit form
- New extention injections points:
  - `ues_course_settings_navigation`: Allows plugins to interact with Settings block
  - `ues_course_edit_form`: Allows plugins to add custom fields to course form
  - `ues_course_edit_validation`: Allows plugins to validate submitted form
  - `course_updated`: Allows plugins to handle submitted data on course form

## v1.1.0

- Small DAO bug fixes and improvements [#24](https://github.com/lsuits/ues/issues/24)
- Section can pull Moodle group [f5ffbfb2][commit-1]
- Filter supports raw sql with `raw` word. [f5ffbfb2][commit-1]
- `ues_section::from_course` populates moodle course [0757305](https://github.com/lsuits/ues/commit/075730511fb6df52c407161ab3d9bc302549faf9)

[commit-1]: https://github.com/lsuits/ues/commit/f5ffbfb20bf74b681f41f145413fd3759e1c7184

## v1.0.0

- Course creation defaults adhere to system settings [#23](https://github.com/lsuits/ues/issues/23)

## v0.0.9 (Snapshot)

- Grade history recovering [#22](https://github.com/lsuits/ues/issues/22)

## v0.0.8 (Snapshot)

- Self passed to provider `postprocess` and optimized `delete_meta` [d87670ed](https://github.com/lsuits/ues/commit/d87670ed215ce162c4669d7863236b96e3fed26c)
- Added [lsu provider] sports info [#15](https://github.com/lsuits/ues/issues/15)
- Minor Bug fix in `get` for `ues_dao` base class [36e9c2e](https://github.com/lsuits/ues/commit/36e9c2e16add34217cb432b0803250ed3416d084)
- Reprocessing no longer steps on nightly [9e1b78e5](https://github.com/lsuits/ues/commit/9e1b78e576361b6ea23c1a3c2db495e3ff24a1bb)
- Added Limit / offset to retrieval [345502](https://github.com/lsuits/ues/commit/3455022849a14144cf78a48654511b76d31a72a2)
- Better reprocessing error reporting [#12](https://github.com/lsuits/ues/issues/12)
- Added the DAO DSL [a73b6cd](https://github.com/lsuits/ues/commit/a73b6cd14dc98c31c4aa5ee7abd5ba54ae57b2b0)
- Fixed an [lsu provider][lsu] bug for student data [62a0b83](https://github.com/lsuits/ues/commit/62a0b83d68d17cc9aad5834080cf7b4b100c0fe8)
- Fixed a bug in teacher demotion / promotion [#17](https://github.com/lsuits/ues/issues/17)
- Fixed a small bug in meta retrieval and reporting [a53b5fe](https://github.com/lsuits/ues/commit/a53b5fe5f1bc83c598c2b307cc55c11d0d0321a1)

## v0.0.7 (Snapshot)

- Added a setting for a grace period [f5a082](https://github.com/lsuits/ues/commit/f5a082fe3052ad26c54bb22e8b63544c9b046083)
- Fixed broken running notification [#13](https://github.com/lsuits/ues/issues/13)

## v0.0.6 (Snapshot)

- Supports idnumber restoring if the course no longer has one [#12](https://github.com/lsuits/ues/issues/12)
- Now emails admin if the cron failed and stopped running for a while [#11](https://github.com/lsuits/ues/issues/11)
- Fixed a bug that would fire release on all released members [abe8d9](https://github.com/lsuits/ues/commit/abe8d9d46e05f631b3ca97d9b8f6d145b02687c5)
- Fixed _Closure Serialization Exception_ in [lsu provider][lsu] [#10](https://github.com/lsuits/ues/issues/10)
- Enrollment order was causing an enrollment exception [0ed6bd](https://github.com/lsuits/ues/commit/0ed6bd2b68496ce6b29d969139ae562c5aa2982a)

## v0.0.5 (Snapshot)

- Emergency bump

## v0.0.4 (Snapshot)

- Made cron interval 12 hours instead of 24

## v0.0.3 (Snapshot)

- Removed the UES enrollment banner [#2](https://github.com/lsuits/ues/issues/2)
- Better _from_ field in email log [#3](https://github.com/lsuits/ues/issues/3)
- Does not send blank emails [#6](https://github.com/lsuits/ues/issues/6)
- Added sort field to DAO API [#7](https://github.com/lsuits/ues/issues/7)
- Fixed user creation [#8](https://github.com/lsuits/ues/issues/8)
- Email header when reprocessing errors

## v0.0.2 (Snapshot)

- Better Moodle DB to DAO support [#5](https://github.com/lsuits/ues/issues/5)

## v0.0.1 (Snapshot)

- Initial Release (see the [wiki](https://github.com/lsuits/ues/wiki) for more details)

[lsu]: https://github.com/lsuits/ues/tree/master/plugins/lsu
