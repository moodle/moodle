@core @core_course @core_customfield @javascript
Feature: The visibility of fields control where they are displayed
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
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
      | Visible to | Everyone   |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Test field | testcontent |
    And I press "Save and display"
    And I am on site homepage
    Then I should see "Test field: testcontent"

  Scenario: Do not display course custom fields on homepage
    When I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field  |
      | Short name | testfield   |
      | Visible to | Nobody      |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Test field | testcontent |
    And I press "Save and display"
    And I am on site homepage
    And I should not see "Test field: testcontent"

  Scenario: Display course custom fields on homepage only to course editors
    When I log in as "admin"
    And I navigate to "Courses > Default settings > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field     |
      | Short name | testfield      |
      | Visible to | Teachers       |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Test field | testcontent |
    And I press "Save and display"
    When I am on site homepage
    And I should see "Test field: testcontent"
    And I log out
    When I log in as "student"
    When I am on site homepage
    And I should not see "Test field: testcontent"
