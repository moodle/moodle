# Changelog

All notable changes to this project will be documented in this file.

Note - All hash comments refer to the issue number. Eg. #169 refers to https://github.com/mdjnelson/moodle-mod_customcert/issues/169.

## [4.4.6] - 2025-06-07

### Added

- Added choice for code format (#668).
- Added events for adding and deleting certificate issues (#675).

## [4.4.5] - 2025-05-25

### Fixed

- Fixed enrol start date not showing (#410).
- Pass the user's mailformat preference when emailing certificates (#665).

### Added

- Added index to the 'code' column in 'customcert_issues' table (#666).

## [4.4.4] - 2025-02-23

### Fixed

- A SQL error in the issue certificate task on MSSQL and Oracle (#646).
- Issuing a certificate when there are no issues yet (#659).
- Issue in rearrange JS with Modal not closing (#648).

### Added

- ISO 8601 date format (#638).

## [4.4.3] - 2024-10-12

### Fixed

- A SQL error in the issue certificate task on Oracle (#646).
- Verification error when expiry date element is present (#647).

### Added

- Only fetch teachers during the email process when necessary, reducing the number of SQL queries if they are not included (#531). 
- Filter users before process to speed up certificate task (#634).

## [4.4.2] - 2024-09-28

### Fixed

- Mobile app: Stop using deprecated module-description.
- Fixed auto-linking filters moving text element positions if reference point is center (#629).

### Changed

- Mobile app: Update Mobile template to Ionic 7.
- Mobile app: Remove Ionic 3 template.

### Added

- Optimise email certificate task by reducing database reads/writes and introducing
  configurable settings for task efficiency (#531).
- New element `expiry` which when used will display the expiry date on the list of issued certificates 
  and the verification pages.<br />
  Any Custom Certificates that are using the `date` element and selected the expiry dates will
  automatically be upgraded to use this new element (#499).

## [4.4.1] - 2024-05-28

### Fixed

- Major issue with the pop-up window not working in the reposition element page (#483).
- Non-editing teachers being able to see the download certificate link for a user which took them to a blank page (#620).

## [4.4.0] - 2024-05-28

### Added

- Added 'Save and Continue' option when editing image elements (#613).
  This means you can add an image to the filemanager, click 'Save and Continue'
  and then select it in the drop-down avoiding renavigating to the edit element page.
- Added monologo image (#568).

## [4.2.5] - 2024-04-23

### Fixed

- Fixed issue when restoring `date`, `daterange`, `gradeitemname` and `grade` that have been linked to a manual grade item (#582).
- Removed unnecessary set_context() call causing a PHP notice to be shown (#443).
- Ensure we use $CFG->emailfromvia setting (#471).

### Changed

- The downloaded issue report can now be ordered by date sanely (#602).

### Added

- Added ability to download all certificates in the report as a teacher and bulk download all site certificates as an administrator (#205).

## [4.2.4] - 2024-02-08

### Fixed

- Do not make index unique (#601).

## [4.2.3] - 2024-01-31

### Fixed

- Stopped PHP notice caused by the email certificate task (#443).
- Fixed undefined external_format_error in the mobile app (#565).
- Fixed being unable to reposition the course field element if it is empty (#579).
- Fixed incorrect name of mustache variable in email_certificate_html.mustache (#574).
- Fixed passing incorrect course module id value to \mod_customcert\output\email_certificate (#574).
- Delete the pages after deleting the elements otherwise it was breaking in element_deleted::create_from_element() (#571).
- Do not also show the 'My certificates' profile link when the user can not view the content of that page (#585).
- Added missing foreign key relationship for 'userid' in the 'customcert_issues' table (#537).
- Handle missing gradeitems as gracefully as possible, so we don't break the email task (#592).
- Fixed logic breaking the generation of the QR code URL (#545).
- Do not allow non-editing teachers to manage the certificate (#515).
- Ensure the 'verifyany' column length is valid on all sites (#597).
- Fixed events being triggered incorrectly (#570).

### Added

- Added the unique index 'userid-customcertid' to the 'customcert_issues' table (#537).
- Added events on the reposition element page (#599).

## [4.2.2] - 2023-06-08

### Fixed

- Fix TCPDF error when viewing an example PDF from the manage templates page (#558).
- Fix images not displaying on the reposition element page (#562).

### Added

- Added new events (#518).
  - An event for when an element is created.
  - An event for when an element is updated.
  - An event for when an element is deleted.
  - An event for when a page is created.
  - An event for when a page is updated.
  - An event for when a page is deleted.
  - An event for when a template is created. 
  - An event for when a template is updated.
  - An event for when a template is deleted.

## [4.2.1] - 2023-05-30

### Fixed

- Fix course settings error on single activity format (#544).
- Remove debugging message caused by the user field element listing the Skype field (#478).
- Fix deprecated usage of rendering primary buttons (#555).
- Fix usage of deprecated `cron_setup_user` function (#547).
- Fix broken webservice functions used by the mobile app.

## [4.0.3] - 2023-02-07

### Added

- You can now optionally force the language of a certificate (#532).

## [4.0.2] - 2023-01-26

### Fixed

- Fix problem repositioning elements (#500).

## [4.0.1] - 2022-11-07

### Fixed

- Fix problem repositioning elements (#513).
- Fixed title and description shown twice (#521).

## [3.11.2] - 2022-11-07

### Fixed
- Fix places not using the multi-language filter (#433).
- Fix user IDs in the issue table not being mapped during restore (#449).
- Fix emails displaying HTML entities encoded (#457).
- Fix error message when we have custom profile fields (#465).
- Respect multiple languages in manage template page title (#467).
- Add field exist check for alignment field in upgrade script to prevent upgrades from dying.
- Stop using deprecated pipe coreToLocaleString.

### Changed
- User breadcrumbs on the my_certificates.php page changes when a course is specified (#469).

### Added
- You can now choose the course short or full name to display (#415).
- You can now select the alignment for all text elements (#121).
- Ability to add a relative date (#389).

## [3.11.1] - 2021-06-13

### Fixed
- Usage of deprecated functions (#423)

## [3.10.1] - 2021-06-13

### Added
- Usage of github actions (#407).
- The ability to show the description on the course page (#406).
- The ability to choose how to deliver the certificate (#401).

### Fixed
- Managers are now able to download their students' certificates (#412).
- Users being able to view the certificate before the required time set (#403).
- Fixed the issue with displaying PDF when debugging is ON (#420).
- Using incorrect context when sending emails (#402).
- Use `cron_setup_user` when sending emails (#414).

## [3.8.5] - 2020-11-26

### Added

- Added ability to select outcomes in the Grade element (#329).
- The Grade Item Name element now works with all grade items, whereas before it was just activities (#346).
- Added enrolment start and end dates to the date element (#328).
- Added username to userfield form element (#390).

### Changed

- Removed unnecessary and confusing 'exampledata' string.
- Do not email those who can manage the certificate (#376).
- Do not force the PDF to be downloaded, instead send the file inline to the browser (#153).
- Updated the 'emailstudents_help', 'emailteachers_help' and 'emailothers_help' strings to warn users about prematurely emailing the certificate (#276).
- Do not email out certificates that contain no elements (#276).

### Fixed

- Certificates now get marked as viewed via the mobile app (#342).
- Custom fields not displaying properly (#359).
- Fix repositioning elements page when resizing the browser (#343).
- Prevent error when duplicate issues exist when using the code element (#363).
- Implemented get_objectid_mapping for the course_module_viewed.php event to avoid warning (#374).
- Fixed exception being thrown when loading a template that has an image element but no image selected (#369).
- Fixed issue with PDF being generated without a name (#333).

## [3.8.4] - 2020-03-12

### Added

- Added extra Behat steps for new elements (#309).

### Changed

- When copying a site template the site images are also copied to the course context and then those copied images are used.
  Before, the elements would simply point to the site images. However, this meant when performing a backup/restore the
  images were not stored in the backup file (#298).

### Fixed

- Fixed the displaying of names of a custom user field (#326).
- Do not allow '0' as a value for width or height in QR code (#321).

## [3.8.3] - 2020-03-09

### Fixed

- Fixed foreign key violation (#331).

## [3.8.2] - 2019-12-16

### Added

- Added subplugins.json file (#312).
- Re-added 'code' column to user report (#264).
- Add 'userfullname' variable for email subject (#316).

### Fixed

- Do not fail if multiple certificate issues (#304) and (#295).

## [3.7.1] - 2019-06-17

### Added

- Added new custom course field element (#274).
- Added ability to specify the current date for date related elements (#289).

### Changed

- String improvements for the 'Date range' element.

### Fixed

- Use negative numbers for constants in the 'Date range' element. The reason being that we may have a module
  that has an id matching one of these positive values. Sites which are using the 'Date range' element (sites
  which are **not** using this element do **not** have to do anything) will need to re-edit each element, select
  the date item again and save. An upgrade step was not created because it is impossible to tell if the site does
  actually want the constant or if they actually want the date for the module.

## [3.6.2] - 2019-05-28

### Changed

- Always send emails from the 'noreplyuser' (#165).

### Added

- Added QR code element (#146).
- Added Date range element (#185).
- Added the number of certificates issued above the report (#266).
- Added new capability to control who can be issued a certificate (#270).

### Fixed

- Failures when running unit tests for multiple activities (#282).
- Check that a certificate is valid before downloading on 'My certificates' page (#269).

## [3.6.1] - 2018-12-31

### Changed

- Make it clear what element values are just an example when previewing the PDF (#144).

### Fixed

- Missing implementation for privacy provider (#260).
- Use course module context when calling format_string/text (#200).
- Exception being thrown when adding the 'teachername' element to site template (#261).

## [3.5.5] - 2018-12-20
### Added

- GDPR: Add support for removal of users from a context (see MDL-62560) (#252).
- Images can be made transparent (#186).
- Set default values of activity instance settings (#180).
- Allow element plugins to control if they can be added to a certificate (#225).
- Allow element plugins to have their own admin settings (#213).
- Added plaintext language variants for email bodies (#231).
- Added possibility to selectively disable activity instance settings (#179).

### Changed

- Allow verification of deleted users (#159).
- The 'element' field in the 'customcert_elements' table has been changed from a Text field to varchar(255) (#241).
- The 'Completion date' option in the 'date' element is only displayed when completion is enabled (#160).
- Instead of assuming 2 decimal points for percentages, we now make use of the decimal value setting, which the
  function `grade_format_gradevalue` does by default if no decimal value is passed.

### Fixed

- Issue with scales not displaying correctly (#242).
- The report now respects the setting 'Show user identity' (#224).
- Removed incorrect course reset logic (#223).
- Description strings referring to the wrong setting (#254).

## [3.5.4] - 2018-07-13
### Fixed

- Use custom fonts if present (#211).
- Fix broken SQL on Oracle in the email certificate task (#187).
- Fixed exception when clicking 'Add page' when template has not been saved (#154).
- Only email teachers who are enrolled within the course (#176).
- Only display teachers who are enrolled within the course in the dropdown (#171).

### Changed

- Multiple UX improvements to both the browser and mobile views (#207).
  - One big change here is combining the report and activity view page into one.
- Allow short dates with leading zeros (#210).

## [3.5.3] - 2018-06-26
### Fixed

- Respect filters in the 'My certificates' and 'Verify certificate' pages (#197).
- Fixed reference to 'mod/certificate' capability.

### Changed

- Multiple UX improvements to both the browser and mobile views (#203).

## [3.5.2] - 2018-06-07
### Fixed

- Hotfix to prevent misalignment of 'text' elements after last release (#196).

## [3.5.1] - 2018-06-06
### Added
- Mobile app support (#70).
```
    This allows students to view the activity and download
    their certificate. It also allows teachers to view the
    list of issued certificates, with the ability to revoke
    any.

    This is for the soon-to-be released Moodle Mobile v3.5.0
    (not to be confused with your Moodle site version) and
    will not work on Mobile versions earlier than this.

    If you are running a Moodle site on version 3.4 or below
    you will need to install the local_mobile plugin in order
    for this to work.

    If you are running a Moodle site on version 3.0 or below
    then you will need to upgrade.
```
- More font sizes (#148).
- Added new download icon.
```
    This was done because the core 'import' icon was mapped
    to the Font Awesome icon 'fa-level-up' which did not look
    appropriate. So, a new icon was added and that was mapped
    to the 'fa-download' icon.
```
### Fixed
- No longer display the 'action' column and user picture URL when downloading the user report (#192).
- Elements no longer ignore filters (#170).

## [3.4.1] - 2018-05-17
### Added
- GDPR Compliance (#189).

### Fixed
- Race condition on certificate issues in scheduled task (#173).
- Ensure we backup the 'verifyany' setting (#169).
- Fixed encoding content links used by restore (#166).

## [3.3.9] - 2017-11-13
### Added
- Added capability ```mod/customcert:verifyallcertificates``` that provides a user with the ability to verify any certificate
  on the site by simply visiting the ```mod/customcert/verify_certificate.php``` page, rather than having to go to the
  verification link for each certificate.
- Added site setting ```customcert/verifyallcertificates``` which when enabled allows any person (including users not logged in)
  to be able to verify any certificate on the site, rather than having to go to the verification link for each certificate.
  However, this only applies to certificates where ```Allow anyone to verify a certificate``` has been set to ```Yes``` in the
  certificate settings.
- You can now display the grade and date of all grade items, not just the course and course activities.
- Text has been added above the ```My certificates``` list to explain that it contains certificates that have been issued to
  avoid confusion as to why certificates may not be appearing.

### Changed
- The course full name is now used in emails.

### Fixed
- Added missing string used in course reset.

## [3.3.8] - 2017-09-04
### Added
- New digital signature element (uses existing functionality in the TCPDF library).
- Ability to duplicate site templates via the manage templates page.
- Ability to delete issued certificates for individual users on the course report page.

### Changed
- Removed usage of magic getter and abuse of ```$this->element```. The variable ```$this->element``` will still be
  accessible by any third-party element plugins, though this is discouraged and the appropriate ```get_xxx()```
  method should be used instead. Using ```$this->element``` in ```definition_after_data()``` will no longer work.
  Please explicitly set the value of any custom fields you have in the form.

### Fixed
- Added missing ```confirm_sesskey()``` checks.
- Minor bug fixes.

## [3.3.7] - 2017-08-11
### Added
- Added much needed Behat test coverage.

### Changed
- Minor language string changes.
- Made changes to the UI when editing a certificate.
  - Moved the 'Add element' submit button below the list of elements.
  - Added icon next to the 'Delete page' link.
  - Changed the 'Add page' button to a link, added an icon and moved it's location to the right.
  - Do not make all submit buttons primary. MDL-59740 needs to be applied to your Moodle install in order to notice the change.

### Fixed
- Issue where the date an activity was graded was not displaying at all.

## [3.3.6] - 2017-08-05
### Changed
- Renamed the column 'size' in the table 'customcert_elements' to 'fontsize' due to 'size' being a reserved word in Oracle.
