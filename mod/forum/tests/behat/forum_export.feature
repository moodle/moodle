@mod @mod_forum @javascript
Feature: Export forum
  In order to parse forum data for linguistic analysis
  As a teacher
  I need to export the forum data for select users

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
      | activity   | name         | type      | course | idnumber |
      | forum      | Test forum 1 | general   | C1     | 123      |

  Scenario: Teacher can export forum
    Given I am on the "Test forum 1" "forum activity" page logged in as teacher1
    And I navigate to "Export" in current page administration
    And I open the autocomplete suggestions list
    And I should see "Student 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Teacher 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should not see "Student 2" in the ".form-autocomplete-suggestions" "css_element"
    # This will fail if an exception is thrown. This is the best we can do without the ability to use the download. Hence, there is no "Then" step.
    And I click on "Export" "button"

  Scenario: Students cannot export forum by default
    Given I am on the "Test forum 1" "forum activity" page logged in as student1
    Then "Export" "link" should not exist in current page administration

  Scenario: User with the capability can export
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | mod/forum:exportforum       | Allow      | student        | Course       | C1        |
    When I am on the "Test forum 1" "forum activity" page logged in as student1
    And I navigate to "Export" in current page administration
    And I open the autocomplete suggestions list
    And I should see "Student 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Teacher 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should not see "Student 2" in the ".form-autocomplete-suggestions" "css_element"
    # This will fail if an exception is thrown. This is the best we can do without the ability to use the download. Hence, there is no "Then" step.
    And I click on "Export" "button"
    And I log out
