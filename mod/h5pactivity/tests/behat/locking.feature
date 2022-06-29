@mod @mod_h5pactivity @core_h5p
Feature: Add H5P activity context locking
  In order to let users access a H5P attempts
  As a user
  I need to access attempts reports even if no more users can submit attempts

  Background:
    Given the following config values are set as admin:
      | contextlocking | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    # This test is only about reporting, we don't need to specify any valid H5P file for it.
    And the following "activity" exists:
      | activity | h5pactivity          |
      | name     | H5P package          |
      | intro    | Test H5P description |
      | course   | C1                   |
      | idnumber | h5ppackage           |
    And the following "mod_h5pactivity > attempt" exists:
      | user            | student1    |
      | h5pactivity     | H5P package |
      | attempt         | 1           |
      | interactiontype | compound    |
      | rawscore        | 2           |
      | maxscore        | 2           |
      | duration        | 4           |
      | completion      | 1           |
      | success         | 1           |

  Scenario: Access participants report on a freeze context
    Given the "h5ppackage" "Activity module" is context frozen
    And I am on the "H5P package" "h5pactivity activity" page logged in as admin
    When I navigate to "Attempts report" in current page administration
    Then I should see "Student 1"
    And I should see "View user attempts (1)" in the "Student 1" "table_row"
    And I should see "Student 2"
    And I should not see "Teacher 1"

  Scenario: Access own attempts on a freeze context
    Given the "h5ppackage" "Activity module" is context frozen
    And I am on the "H5P package" "h5pactivity activity" page logged in as student1
    When I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Attempt #1: Student 1"
    And I should see "This attempt is completed"

  Scenario: Access participants report without any user with submit capability
    Given the following "permission overrides" exist:
      | capability             | permission | role    | contextlevel | reference |
      | mod/h5pactivity:submit | Prohibit   | student | System       |           |
    And I am on the "H5P package" "h5pactivity activity" page logged in as admin
    When I navigate to "Attempts report" in current page administration
    Then I should see "Student 1"
    And I should see "View user attempts (1)" in the "Student 1" "table_row"
    And I should see "Student 2"
    And I should not see "Teacher 1"

  Scenario: Access participant report to list students with submit capability but no view one
    Given the following "permission overrides" exist:
      | capability           | permission | role    | contextlevel | reference |
      | mod/h5pactivity:view | Prohibit   | student | System       |           |
    And I am on the "H5P package" "h5pactivity activity" page logged in as admin
    When I navigate to "Attempts report" in current page administration
    Then I should see "No participants to display"

  Scenario: Access participant report but with no users with view or submit capability
    Given the following "permission overrides" exist:
      | capability             | permission | role    | contextlevel | reference |
      | mod/h5pactivity:submit | Prohibit   | student | System       |           |
      | mod/h5pactivity:view   | Prohibit   | student | System       |           |
    And I am on the "H5P package" "h5pactivity activity" page logged in as admin
    When I navigate to "Attempts report" in current page administration
    Then I should see "No participants to display"

  Scenario: Access participant report in a hidden activity
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Hide" "link" in the "H5P package" activity
    When I am on the "H5P package" "h5pactivity activity" page
    And I navigate to "Attempts report" in current page administration
    Then I should see "Student 1"
    And I should see "View user attempts (1)"
    And I should see "Student 2"
    And I should not see "Teacher 1"
