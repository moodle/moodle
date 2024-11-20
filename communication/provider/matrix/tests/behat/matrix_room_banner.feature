@communication @communication_matrix
Feature: Display communication room status banner
  Show a banner depending on the room status
  As a teacher or admin

  Background:
    Given a Matrix mock server is configured
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname    | shortname   | category | selectedcommunication | communicationroomname |
      | Test course | Test course | 0        | communication_matrix  | matrixroom            |
    And the following "course enrolments" exist:
      | user     | course      | role           |
      | teacher1 | Test course | editingteacher |
      | student1 | Test course | student        |

  Scenario: I can see the room has been created and in a pending status
    When I am on the "Test course" "Course" page logged in as "teacher1"
    Then I should see "Your Matrix room will be ready soon." in the "page-content" "region"
    When I am on the "Test course" "Course" page logged in as "student1"
    # Not for students to see.
    Then I should not see "Your Matrix room will be ready soon." in the "page-content" "region"

  Scenario: I can see the room has been created and ready to access
    When I run all adhoc tasks
    And I am on the "Test course" "Course" page logged in as "teacher1"
    Then I should see "Your Matrix room is ready." in the "page-content" "region"
    # This is a one time message per user.
    When I reload the page
    Then I should not see "Your Matrix room is ready." in the "page-content" "region"
    # Not for students to see.
    When I am on the "Test course" "Course" page logged in as "student1"
    Then I should not see "Your Matrix room is ready." in the "page-content" "region"

  Scenario: Enabling or disabling the matrix plugin hides the banner accordingly
    Given I am on the "Test course" "Course" page logged in as "teacher1"
    Then I should see "Your Matrix room will be ready soon." in the "page-content" "region"
    When I log in as "admin"
    And I navigate to "Plugins > Communication > Manage communication providers" in site administration
    And I should see "Matrix"
    And I click on "Disable" "link" in the "Matrix" "table_row"
    And I am on the "Test course" "Course" page logged in as "teacher1"
    And I should not see "Your Matrix room will be ready soon." in the "page-content" "region"
    And I log in as "admin"
    And I navigate to "Plugins > Communication > Manage communication providers" in site administration
    And I should see "Matrix"
    And I click on "Enable" "link" in the "Matrix" "table_row"
    And I am on the "Test course" "Course" page logged in as "teacher1"
    Then I should see "Your Matrix room will be ready soon." in the "page-content" "region"
