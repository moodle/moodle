# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [2021012000] - 2021-01-20
### Improved
- Migrated from Travis to GitHub Actions

### Fixed
- Mark new users as confirmed
- Correct variable name when assign agents to a user
- Fix an issue where the roleid list may be empty when removing users

### Added
- Added a new setting to select the authentication method (Fixes #7)
- Added a new setting to select the page size when querying the OneRoster host

## [2020120701] - 2020-12-07
### Fixed
- Fetching parent organisation

## [2020120700] - 2020-12-07
### Fixed
- Test Connection script should respect configured OAuth setting
- Corrected admin setting path for test connection script
- Fixed missing database table

### Changed
- Corrected php docblock for testconnection.php script
- Corrected typo on docblock

### Improved
- Handling of Organisation data to assist with bug in OR Reference Implementation

## [2020120301] - 2020-12-03
### Added
- Missing testconnection.php script access from the UI (#5)
- Added a Changelog.md to capture future changes
