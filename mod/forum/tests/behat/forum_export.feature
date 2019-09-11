@mod @mod_forum @javascript
Feature: Export forum
  In order to export forum content
  As a teacher
  I need to add a forum activity and discussions

  Background: Add a forum and a discussion
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name         | intro                     | type      | course | idnumber |
      | forum      | Test forum 1 | Test forum 1 description  | general   | C1     | 123      |

  Scenario: Teacher can export forum
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Test forum 1"
    And I navigate to "Export" in current page administration
    And I open the autocomplete suggestions list
    And I should see "Student 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Teacher 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should not see "Student 2" in the ".form-autocomplete-suggestions" "css_element"
    And I click on "Export" "button"
    And I log out

  Scenario: Students cannot export forum by default
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test forum 1"
    Then "Export" "link" should not exist in current page administration
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
    And I override the system permissions of "Student" role with:
      | capability | permission |
      | mod/forum:exportforum | Allow |
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test forum 1"
    And I navigate to "Export" in current page administration
    And I click on "Export" "button"
