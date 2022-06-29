@core_message @message_popup @javascript
Feature: Notification popover unread notifications
  In order to be kept informed
  As a user
  I am notified about relevant events in Moodle

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    # This should generate some notifications
    And the following "notifications" exist:
      | subject  | userfrom | userto   | timecreated | timeread   |
      | Test 01  | student2 | student1 | 1654587996  | null       |
      | Test 02  | student2 | student1 | 1654587997  | null       |

  Scenario: Notification popover shows correct unread count
    Given I log in as "student1"
    # Confirm the popover is saying 1 unread notifications.
    And I should see "2" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    When I open the notification popover
    # Confirm the notifications are visible.
    Then I should see "Test 01" in the "#nav-notification-popover-container" "css_element"
    And I should see "Test 02" in the "#nav-notification-popover-container" "css_element"

  @_bug_phantomjs
  Scenario: Clicking a notification marks it as read
    Given I log in as "student1"
    # Open the notifications.
    When I open the notification popover
    And I follow "Test 01"
    And I open the notification popover
    And I follow "Test 02"

    # Confirm the count element is hidden (i.e. there are no unread notifications).
    Then "[data-region='count-container']" "css_element" in the "#nav-notification-popover-container" "css_element" should not be visible

  Scenario: Mark all notifications as read
    Given I log in as "student1"
    When I open the notification popover
    And I click on "Mark all as read" "link" in the "#nav-notification-popover-container" "css_element"
    # Refresh the page to make sure we send a new request for the unread count.
    And I reload the page
    # Confirm the count element is hidden (i.e. there are no unread notifications).
    Then "[data-region='count-container']" "css_element" in the "#nav-notification-popover-container" "css_element" should not be visible
