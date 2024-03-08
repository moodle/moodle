@core @core_grades @javascript
Feature: Configurable default grade export method

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: View default grade export method in gradebook
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "More > Export" in the course gradebook
    Then I should see "Export to OpenDocument spreadsheet"

  Scenario: Changing the default grade export method in gradebook
    Given the following config values are set as admin:
      | gradeexport_default | txt |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "More > Export" in the course gradebook
    Then I should see "Export to Plain text file"
