# v3.0.1
- Changed style to conform to Moodle 3.1 standard
- Added `helpdesk_course` event using Event 2 standard

# v3.0.0

- Removed firing `helpdesk_course` and `helpdesk_user` in favor of internal process
- Moodle 3.1 compatibility

# v1.1.3

- update deprecated fn calls
- inject altname fields into sql
- Moodle 2.7 compatibility

# v1.1.2

- Remove smarty depenedency [diff](https://github.com/lsuits/helpdesk/compare/e2d8fa5903...467e5df453)
- Use Moodle Course search [#3][3]
- User search includes idnumber [#4][4]
- Prevent Course additions [#5][5] ([jcockrell][jcockrell])

[3]: https://github.com/lsuits/helpdesk/issues/3
[4]: https://github.com/lsuits/helpdesk/issues/4
[5]: https://github.com/lsuits/helpdesk/issues/5
[jcockrell]: https://github.com/jcockrell

# v1.1.1

- Namespace smarty template [c6c172][c6c172]
- Fixes query for postgresql [#1][1]
- Fires `helpdesk_course` and `helpdesk_user` [#2][2]

[1]: https://github.com/lsuits/helpdesk/issues/1
[2]: https://github.com/lsuits/helpdesk/issues/2
[c6c172]: https://github.com/lsuits/helpdesk/commit/c6c1724f98f8f55080e1894fa122f92b2cc4f764

# v1.1.0

- Update to latest quick_render

# v1.0.0

- initial release
