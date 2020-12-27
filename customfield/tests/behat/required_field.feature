@core @core_course @core_customfield
Feature: Requiredness The course custom fields can be mandatory or not
  In order to make users required to fill a custom field
  As a manager
  I can change the requiredness of the fields

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_course | course | 0      |
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: A required course custom field must be filled when editing course settings
    When I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
      | Required   | Yes        |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I press "Save and display"
    Then I should see "You must supply a value here"
    And I set the field "Test field" to "some value"
    And I press "Save and display"
    And I should not see "This field is required"
    And I log out

  Scenario: A course custom field that is not required may not be filled
    When I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
      | Required   | No         |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I press "Save and display"
    Then I should see "Course 1"
    And I should see "Topic 1"
