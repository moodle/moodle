# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

The latest release of this plugin is v8.8.0 (4 nov. 2024).

## v8.8.1 - 4 nov. 2024
- fix: LaTeX formulas not being rendered with PHP Render Type.

## v8.8.0 - 7 oct. 2024
- test: Improve test coverage for 4.5
- feat: Add compatibility with Moodle 4.5.

## v8.7.1 - 27 aug. 2024
- test: Improve and add test coverage.
- refactor: Moodle test page. #KB-43810
- fix: Fetch notification when load setting page. #KB-44423
  
## v8.7.0 - 23 apr. 2024
- feat: Add compatibility with Moodle 4.4.

## v8.6.3 - 26 mar. 2024
- chore: Update filter dependency

## v8.6.2 - 13 dec. 2023
- chore: Update Telemeter to 1.1.3

## v8.6.1 - 10 oct. 2023
- fix: revert to old Viewer

## v8.6.0 - 9th oct. 2023
- feat: Update devkit & telemeter
- feat: Add compatibility with Moodle 4.3

## v8.3.0 - 12th jun. 2023
- fix: Improve data reporting
- fix: Some issues with PHP 8.2

## v8.2.6 - 24th apr. 2023
- feat: Compatibility with Moodle 4.2

## v8.1.0 - 29th nov. 2023
- fix: Added support for PHP 8.1
- fix: Improve data reporting

## v8.0.1 - 23rd nov. 2022
- fix: Flaky tests for Moodle 4.1

## v8.0.0 - 20th oct. 2022
- fix: update mathtype and chemtype image icons #KB-22667

## v7.30.0 - 1st jul. 2022
 - fix: Use branch stable as a failsafe in CI
 - ci: add moodle 4 on the ci workflow matrix
 - ci: add cd workflow

## v7.29.0 - 20th jun. 2022
- fix(ci): moodle code checker warning and errors #19424.
- Change links to make them have UTMs #KB-25028.
- Make links open in a new tab #KB-25519

## v7.27.1 - 9th nov. 2021
- Fix "missing ['privacy:metadata']" from @christina-roperto contribution #86
- Improve the "MathType Moodle Plugins Suite" software development cycle.
  - Use 'ubuntu-latest' for the Moodle Plugin CI workflow.
  - Add 'on:schedule' trigger property to run the tests every morning.
  - Add 'on:workflow_dispatch' trigger property to run test on demand.
  - Improve code comments to match internal code guidelines.

## v7.27.0 - 22nd of july 2021
- Bump 'MathType Web Integration JavaScript SDK' to 1.6.0.
- Upgrade path for mathtype-html-integration-devkit.
  A new command 'npm run update-mathtype' get the latest
  version from all Third Party Libraries, automatically.

## v7.26.1 - 18th of june 2021
- Feature: 'Migrate MathType plugins suite from TravisCI to Github Actions'.


## v7.26.0 - 3th of may 2021
- [KB-7506]: Fix & upgrade TravisCI job configuration.
  - Support for PHP7_3 and PHP7_4.
  - Support for Moodle3_10 and Moodle3_11.
  - Enable `fast_finish` option.
  - Don't use the `blackboard-open-source` distro until it's fixed.
- Add support for local rebuild and lint Moodle tasks.
- Update project documentation by improving the main `README` file.
- Start using `CHANGES` file as changelog.
