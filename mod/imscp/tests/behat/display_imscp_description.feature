@mod @mod_imscp
Feature: Display the IMS content package description in the IMSCP and optionally in the course
  In order to display the the IMS content package description description in the course
  As a teacher
  I need to enable the 'Display description on course page' setting.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript @_file_upload
  Scenario: Description is displayed in the IMS content package
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "IMS content package" to section "1"
    And I set the following fields to these values:
      | Name | Test IMS content package |
      | Description | Test IMS content package description |
    And I upload "mod/imscp/tests/packages/singlescobasic.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    When I am on the "Test IMS content package" "imscp activity" page
    Then I should see "Test IMS content package description"

  @javascript @_file_upload
  Scenario: Show IMS description in the course homepage
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "IMS content package" to section "1"
    And I set the following fields to these values:
      | Name | Test IMS content package |
      | Description | Test IMS content package description |
    And I upload "mod/imscp/tests/packages/singlescobasic.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    When I am on the "Test IMS content package" "imscp activity editing" page
    And the following fields match these values:
      | Display description on course page | |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should see "Test IMS content package description"

  @javascript @_file_upload
  Scenario: Hide IMS description in the course homepage
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "IMS content package" to section "1"
    And I set the following fields to these values:
      | Name | Test IMS content package |
      | Description | Test IMS content package description |
    And I upload "mod/imscp/tests/packages/singlescobasic.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    When I am on the "Test IMS content package" "imscp activity editing" page
    And the following fields match these values:
      | Display description on course page | |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should not see "Test IMS content package description"
