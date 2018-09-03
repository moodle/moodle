@message @message_popup @javascript
Feature: Message popover unread messages
  In order to be kept informed
  As a user
  I am notified about unread messages from other users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And I log in as "student2"
    And I send "Test message" message to "Student 1" user
    And I log out

  Scenario: Message popover shows correct unread count
    When I log in as "student2"
    And I send "Test message 2" message to "Student 1" user
    And I log out
    And I log in as "student1"
    # Confirm the popover is saying 1 unread conversation.
    Then I should see "1" in the "#nav-message-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    And I open the message popover
    # Confirm the conversation is visible.
    And I should see "Test message 2" in the "#nav-message-popover-container" "css_element"
    # Confirm the count of unread messages in the conversation is correct.
    And I should see "2" in the "#nav-message-popover-container [data-region='unread-count']" "css_element"

  Scenario: Clicking a message marks it as read
    When I log in as "student1"
    # Open the popover.
    And I open the message popover
    # Click on the conversation.
    And I click on "[aria-label='View unread messages with Student 2']" "css_element" in the "#nav-message-popover-container" "css_element"
    # Confirm the count element is hidden (i.e. there are no unread messages).
    Then "[data-region='count-container']" "css_element" in the "#nav-message-popover-container" "css_element" should not be visible
    # Confirm the message was loaded in the messaging page.
    And I should see "Test message" in the "[data-region='message-text']" "css_element"

  Scenario: Mark all messages as read
    When I log in as "student1"
    # Open the popover.
    And I open the message popover
    # Click the mark all as read button.
    And I click on "[data-action='mark-all-read']" "css_element" in the "#nav-message-popover-container" "css_element"
    # Refresh the page to make sure we send a new request for the unread count.
    And I reload the page
    # Confirm the count element is hidden (i.e. there are no unread messages).
    Then "[data-region='count-container']" "css_element" in the "#nav-message-popover-container" "css_element" should not be visible
