@core @core_message
Feature: Notification preferences show correct processor state
  As a user
  I need to see which notification processors are available and configured for me
  So that I understand which notification channels I can use

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | phone2       |
      | student1 | Student   | 1        | student1@example.com |              |
      | student2 | Student   | 2        | student2@example.com | +61000000000 |
      | manager1 | Manager   | 1        | manager1@example.com | +61000000001 |
    And the following "system role assigns" exist:
      | user     | course               | role    |
      | manager1 | Acceptance test site | manager |
    And I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "sms" to "1"
    And I press "Save changes"
    And I set the following fields to these values:
      | mod_assign_assign_notification_enabled[sms] | 1 |
      | mod_assign_assign_notification_locked[sms]  | 0 |
    And I log out

  @javascript
  Scenario: Processor without configuration form shows disabled state when user is not configured
    # When is_user_configured() returns false for a processor that has no settings form,
    # the processor column header should be dimmed and the notification toggles should be disabled.
    # This is tested using the SMS processor (which has no config form) for a user without a mobile
    # phone number (phone2), causing is_user_configured() to return false.
    Given I log in as "student1"
    And I follow "Preferences" in the user menu
    When I click on "Notification preferences" "link" in the "#page-content" "css_element"
    Then "span.dimmed_text" "css_element" should exist in the "[data-processor-name='sms']" "css_element"
    And the "message_provider_mod_assign_assign_notification_sms" "checkbox" should be disabled

  @javascript
  Scenario: Processor without configuration form shows enabled state when user is configured
    # When is_user_configured() returns true, the processor column header should not be dimmed
    # and the notification toggles should be enabled.
    # This is tested using the SMS processor for a user with a mobile phone number (phone2) set.
    Given I log in as "student2"
    And I follow "Preferences" in the user menu
    When I click on "Notification preferences" "link" in the "#page-content" "css_element"
    Then "span.dimmed_text" "css_element" should not exist in the "[data-processor-name='sms']" "css_element"
    And the "message_provider_mod_assign_assign_notification_sms" "checkbox" should be enabled

  @javascript
  Scenario: Processor state reflects the edited user, not the editing manager, when a manager edits another user's preferences
    # A manager (who has a configured mobile phone number) edits the notification preferences of
    # student1 (who has no phone2). is_user_configured() must be evaluated for student1, not for
    # the logged in manager, otherwise the SMS processor would incorrectly appear enabled just
    # because the manager happens to have a phone number configured.
    Given I am on the "student1" "user > profile" page logged in as "manager1"
    And I click on "Preferences" "link" in the ".profile_tree" "css_element"
    When I click on "Notification preferences" "link" in the "#page-content" "css_element"
    Then "span.dimmed_text" "css_element" should exist in the "[data-processor-name='sms']" "css_element"
    And the "message_provider_mod_assign_assign_notification_sms" "checkbox" should be disabled
