@core @core_message
Feature: To be able to see and save user message preferences as admin
  As an admin
  I need to be able to view and edit message preferences for other users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@emample.com |
    And the following "user preferences" exist:
      | user      | preference                                        | value |
      | student1  | message_provider_moodle_instantmessage_enabled    | email |

  @javascript
  Scenario: As an admin I can view and edit message preferences for a user
    Given I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    And I am on the "student1" "user > profile" page
    And I click on "Preferences" "link" in the "#region-main-box" "css_element"
    And I click on "Message preferences" "link" in the "#region-main-box" "css_element"
    And I should not see "Enabled" in the "Email" "table_row"
    And I click on "//div[@class='preference-state']//input" "xpath_element"
    And I log out
    And I log in as "student1"
    And I follow "Preferences" in the user menu
    And I click on "Message preferences" "link"
    And the field "Email" matches value "0"

  Scenario: Only active plugins have corresponding columns on the User preferences notification settings
    # Needed for Mobile column to appear on User preferences notification settings.
    Given the following config values are set as admin:
      | airnotifieraccesskey | test123 |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    When I click on "Notification preferences" "link" in the "#page-content" "css_element"
    # By default, web/popup is enabled. mobile/airnotifier is enabled using an earlier step while email is disabled.
    Then "[data-processor-name='popup']" "css_element" should exist
    And "[data-processor-name='email']" "css_element" should not exist
    And "[data-processor-name='airnotifier']" "css_element" should exist

  @javascript
  Scenario: An admin can set the default notification preferences
    Given I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    And I set the following fields to these values:
      | mod_assign_assign_notification_enabled[popup] | 1 |
      | mod_assign_assign_notification_enabled[email] | 0 |
      | mod_feedback_submission_enabled[popup]        | 0 |
      | mod_feedback_submission_locked[popup]         | 1 |
      | mod_feedback_submission_enabled[email]        | 1 |
      | mod_feedback_submission_locked[email]         | 1 |
      | mod_feedback_message_disable                  | 0 |
    And I log in as "student1"
    And I follow "Preferences" in the user menu
    When I click on "Notification preferences" "link" in the "#page-content" "css_element"
    # Assignment web notification is enabled, email is disabled.
    Then the field "message_provider_mod_assign_assign_notification_popup" matches value "1"
    And the field "message_provider_mod_assign_assign_notification_email" matches value "0"
    # Corresponding checkboxes can be updated.
    And I click on "message_provider_mod_assign_assign_notification_popup" "checkbox"
    And I click on "message_provider_mod_assign_assign_notification_email" "checkbox"
    And the field "message_provider_mod_assign_assign_notification_popup" matches value "0"
    And the field "message_provider_mod_assign_assign_notification_email" matches value "1"
    # Feedback submission notifications are locked off for web and locked on for email.
    And "Feedback notifications" row "Web" column of "preference-table" table should contain "Locked off"
    And "Feedback notifications" row "Requires configuration Email" column of "preference-table" table should contain "Locked on"
    # Feedback reminder is disabled.
    And I should not see "Feedback reminder"
