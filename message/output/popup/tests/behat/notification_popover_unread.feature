@core_message @message_popup @javascript
Feature: Notification popover unread notifications
  In order to be kept informed
  As a user
  I am notified about relevant events in Moodle

  Background:
    # This will make sure popup notifications are enabled and create
    # two assignment notifications. One for the student submitting their
    # assignment and another for the teacher grading it.
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    # Make sure the popup notifications are enabled for assignments.
    And the following config values are set as admin:
      | popup_provider_mod_assign_assign_notification_locked    | 0     | message |
      | message_provider_mod_assign_assign_notification_enabled | popup | message |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity                            | assign               |
      | course                              | C1                   |
      | name                                | Test assignment name |
      | assignsubmission_onlinetext_enabled | 1                    |
      | assignsubmission_file_enabled       | 0                    |
      | submissiondrafts                    | 0                    |
    # This should generate a notification.
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

  Scenario: Notification popover shows correct unread count
    When I log in as "student1"
    # Confirm the popover is saying 1 unread notifications.
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    And I open the notification popover
    # Confirm the submission notification is visible.
    And I should see "You have submitted your assignment submission for Test assignment name" in the "#nav-notification-popover-container" "css_element"

  @_bug_phantomjs
  Scenario: Clicking a notification marks it as read
    When I log in as "student1"
    # Open the popover.
    And I open the notification popover
    # Click on the submission notification.
    And I follow "You have submitted your assignment submission for Test assignment name"
    # Confirm the count element is hidden (i.e. there are no unread notifications).
    Then "[data-region='count-container']" "css_element" in the "#nav-notification-popover-container" "css_element" should not be visible

  Scenario: Mark all notifications as read
    When I log in as "student1"
    # Open the popover.
    And I open the notification popover
    # Click the mark all as read button.
    And I click on "Mark all as read" "link" in the "#nav-notification-popover-container" "css_element"
    # Refresh the page to make sure we send a new request for the unread count.
    And I reload the page
    # Confirm the count element is hidden (i.e. there are no unread notifications).
    Then "[data-region='count-container']" "css_element" in the "#nav-notification-popover-container" "css_element" should not be visible
