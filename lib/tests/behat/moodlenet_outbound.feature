@core
Feature: MoodleNet outbound send activity
  In order to send activity to MoodleNet server
  As a teacher
  I need to be able package the activity and share to MoodleNet

  Background:
    Given I log in as "admin"
    And a MoodleNet mock server is configured
    And the following config values are set as admin:
      | enablesharingtomoodlenet | 1 |
    And I navigate to "Server > OAuth 2 services" in site administration
    And I press "MoodleNet"
    And I should see "Create new service: MoodleNet"
    And I change the MoodleNet field "Service base URL" to mock server
    And I press "Save changes"
    And I navigate to "MoodleNet > MoodleNet outbound settings" in site administration
    And I set the field "Auth 2 service" to "MoodleNet"
    And I press "Save changes"
    And the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | manager1 | C1     | manager        |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name              | intro             |
      | assign   | C1     | assign1  | Test Assignment 1 | Test Assignment 1 |

  Scenario: Share to MoodleNet menu only be available for teachers and managers
    Given I am on the "Test Assignment 1" "assign activity" page logged in as student1
    Then "Share to MoodleNet" "link" should not exist in current page administration
    And I am on the "Test Assignment 1" "assign activity" page logged in as teacher1
    And "Share to MoodleNet" "link" should exist in current page administration
    And I am on the "Test Assignment 1" "assign activity" page logged in as manager1
    And "Share to MoodleNet" "link" should exist in current page administration

  Scenario: Share to MoodleNet menu only be available for user that has capability only
    Given the following "permission overrides" exist:
      | capability                    | permission | role           | contextlevel | reference |
      | moodle/moodlenet:shareactivity | Prohibit   | editingteacher | Course       | C1        |
    When I am on the "Test Assignment 1" "assign activity" page logged in as teacher1
    Then "Share to MoodleNet" "link" should not exist in current page administration
    And I am on the "Test Assignment 1" "assign activity" page logged in as manager1
    And "Share to MoodleNet" "link" should exist in current page administration
    And the following "permission overrides" exist:
      | capability                    | permission | role    | contextlevel | reference |
      | moodle/moodlenet:shareactivity | Prohibit   | manager | Course       | C1        |
    And I am on the "Test Assignment 1" "assign activity" page logged in as manager1
    And "Share to MoodleNet" "link" should not exist in current page administration

  @javascript
  Scenario: User can share activity to MoodleNet
    Given I am on the "Test Assignment 1" "assign activity" page logged in as teacher1
    When I navigate to "Share to MoodleNet" in current page administration
    Then I should see "Assignment" in the "Share to MoodleNet" "dialogue"
    And I should see "Test Assignment 1" in the "Share to MoodleNet" "dialogue"
    And I should see "You are sharing this to MoodleNet as a resource" in the "Share to MoodleNet" "dialogue"
    And I click on "Share" "button" in the "Share to MoodleNet" "dialogue"
    And I switch to "moodlenet_auth" window
    And I press "Allow" and switch to main window
    And I should see "Saved to MoodleNet drafts"
    And "Go to MoodleNet drafts" "link" should exist in the "Share to MoodleNet" "dialogue"
