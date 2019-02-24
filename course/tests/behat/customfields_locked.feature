@core @core_course @core_customfield
Feature: Fields locked control where they are displayed
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

  Scenario: Display course custom fields on homepage
    When I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Text field" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
      | Locked     | No         |
