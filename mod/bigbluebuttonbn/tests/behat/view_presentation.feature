@mod @mod_bigbluebuttonbn @javascript @_file_upload
Feature: Test visibility of presentation on activity page
  In order to ensure that presentation files are not visible to students when they shouldn't be
  As a teacher
  I set the visibility of presentation files in the BigBlueButtonBN activity
  Background:
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
      | uraverst | Uerry     | Uravers  | u.uravers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role     |
      | traverst | C1     | student  |
      | uraverst | C1     | teacher  |
    And the following config values are set as admin:
      | bigbluebuttonbn_preuploadpresentation_editable | 1 |

  Scenario Outline: Check that presentation file can only be viewed when teachers allow it
    Given the following "activity" exists:
      | course              | C1                  |
      | activity            | bigbluebuttonbn     |
      | name                | Room recordings     |
      | moderators          | role:teacher        |
      | showpresentation    | <value>             |
    And the following config values are set as admin:
      | config                    | value              | plugin     |
      | showpresentation_default  | <showfile_default> | mod_bigbluebuttonbn |
      | showpresentation_editable | <showfile_editable>| mod_bigbluebuttonbn |
    And I am on the "Room recordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I expand all fieldsets
    And I upload "mod/bigbluebuttonbn/tests/fixtures/bbpresentation.pptx" file to "Select files" filemanager
    And I press "Save and display"
    When I am on the "Room recordings" Activity page logged in as <user>
    Then I <existence> "Presentation file"
    And I <existence> "bbpresentation.pptx"

    Examples:
      | user     | value  | showfile_editable | showfile_default | existence      |
      | traverst | 1      | 1                 | 1                | should see     |
      | uraverst | 1      | 1                 | 1                | should see     |
      | traverst | 1      | 1                 | 0                | should see     |
      | uraverst | 1      | 1                 | 0                | should see     |
      | traverst | 0      | 0                 | 1                | should see     |
      | uraverst | 0      | 0                 | 1                | should see     |
      | traverst | 0      | 0                 | 0                | should not see |
      | uraverst | 0      | 0                 | 0                | should see     |
      | traverst | 0      | 1                 | 1                | should not see |
      | uraverst | 0      | 1                 | 1                | should see     |
