# tiny_fontcolor

## Changes

### V1.2

- Fix [Background color choosen via background colorpicker is saved as text color #24](https://github.com/bfh/moodle-tiny_fontcolor/issues/24)

### V1.1

- Support for Moodle 5.1
- In the CI drop all tests with outdated PHP versions. Add Moodle 5.1 to the pipeline.
- Move version history from README into CHANGES.
- Add workflow for autorelease into the Moodle plugin directory.

### V1.0

- New setting `usecssclassnames` that allow to have css clases instead of using color codes
  in the `style` attribute.
- Use a color picker for defining colors in the admin settings.
- Allow to define an alphachannel for the color codes in the admin settings.

### V0.10

- Adjust capability context level according to [MDL-84884](https://tracker.moodle.org/browse/MDL-84884)

### V0.9

- Adjustments for Moodle 5.0.
- Remove Github actions to sync master and main branch.

### V0.8

- Adjustments for Moodle 4.5

### V0.7

- Fix [HTML areas without files seem to fail with the tiny_fontcolor enabled #16](https://github.com/bfh/moodle-tiny_fontcolor/issues/16) and 
[TinyMCE fails when creating a qtype_multichoice question #17](https://github.com/bfh/moodle-tiny_fontcolor/issues/17). The introduced context menu items in 0.6 didn't show up under
some conditions (e.g. in mc questions for answers and feedback) when there was no previously
defined contextmenu.

### V0.6

- [Add quickbar support #14](https://github.com/bfh/moodle-tiny_fontcolor/issues/14)
by [Thomas Ludwig](https://github.com/tholudwig)

### V0.5

- Add support for Moodle 4.4 and PHP 8.3.
- Add json for a comprehensive color scheme (thanks to Joseph Rézeau).

### V0.4

- Add CI stack for Moodle 4.3

### V0.3

- Fix CI issue: (#1) HTML Validation info, line 10: Trailing slash on void elements has no effect and interacts badly with unquoted attribute values.
- [Fix behat by switching to trait class](https://github.com/bfh/moodle-tiny_fontcolor/pull/12)
by [Jason Platts](https://github.com/jason-platts)
- [Preparing for PHP 8.2 Support](https://github.com/bfh/moodle-tiny_fontcolor/pull/13)
by [Luca Bösch](https://github.com/lucaboesch)
- Lifted software maturity to stable

### V0.2.3

- Lift software maturity level to RC.
- Fix issue [The close button of the color picker can't be reach by keyboard](https://github.com/bfh/moodle-tiny_fontcolor/issues/10)

### V0.2.2

- Lift software maturity level to STABLE.
- Adapt CI to test against Moodle 4.2.
- Fix example JSON in mustache templates and make CI have templates checked.
- Fix issue [Probably, $string['helplinktext'] = 'Font colour'; is needed in the lang strings](https://github.com/bfh/moodle-tiny_fontcolor/issues/6).

### V0.2.1

- Add behat test for the admin settings page and reorganize tests.
- Remove function `str_contains` to be PHP7.x compliant.
- Change maturity of plugin to release candidate.
- Privacy Provider was added.

### V0.2.0

Initial release
