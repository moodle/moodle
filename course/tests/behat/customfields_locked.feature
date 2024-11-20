@core @core_course @core_customfield @javascript
Feature: Fields locked control who is able to edit it
  In order to display custom fields on course listing
  As a manager
  I can change the visibility of the fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Editing locked and not locked custom fields
    When I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field1 |
      | Short name | testfield1  |
      | Locked     | No          |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field2 |
      | Short name | testfield2  |
      | Locked     | Yes         |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Test field1 | testcontent1 |
      | Test field2 | testcontent2 |
    And I press "Save and display"
    And I am on site homepage
    Then I should see "Test field1: testcontent1"
    And I should see "Test field2: testcontent2"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "Test field1" matches value "testcontent1"
    And I should not see "Test field2"
    And I press "Save and display"
    And I am on site homepage
    And I should see "Test field1: testcontent1"
    And I should see "Test field2: testcontent2"
