@mod @mod_qbassign @javascript
Feature: Manage qbassignment submission web notifications
  In order to receive qbassignment submission notifications
  As a teacher
  I need to be able to turn on web notifications for qbassignment submission

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity                            | qbassign   |
      | course                              | C1       |
      | name                                | qbassign 1 |
      | qbassignsubmission_onlinetex_enabled | 1        |
      | qbassignsubmission_file_enabled       | 0        |
      | submissiondrafts                    | 0        |
      | sendnotifications                   | 1        |
    And the following "mod_qbassign > submissions" exist:
      | qbassign   | user     | onlinetex                  |
      | qbassign 1 | student1 | I'm the student1 submission |

  Scenario: Teacher can choose to receive qbassignment notification submissions
    Given I log in as "teacher1"
    When I open the notification popover
    Then I should see "You have no notifications"
    # Update qbassignment submission to generate a notification
    And I am on the "qbassign 1" "qbassign activity" page logged in as student1
    And the following "user preferences" exist:
      | user      | preference                                                | value |
      | teacher1  | message_provider_mod_qbassign_qbassign_notification_enabled   | popup |
    # This should generate a notification
    And I press "Edit submission"
    And I set the field "Online text" to "updated"
    And I press "Save changes"
    # Confirm that teacher received qbassignment submission notification
    And I log in as "teacher1"
    And I open the notification popover
    Then I should see "Student 1 has updated their submission for qbassignment qbassign 1"
