@core @core_message @javascript
Feature: Manage notification preferences - Email
  In order to be notified of messages
  As a user
  I need to be able to update my messaging notification preferences

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    # Turn off the course welcome message, so we can easily test other messages.
    And the following config values are set as admin:
      | messaging                | 1 | core         |
      | sendcoursewelcomemessage | 0 | enrol_manual |

  Scenario: Disable email notifications for everybody
    Given I log in as "admin"
    When I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "0"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    Then I should not see "Notification preferences"
    And I should not see "Email"

  Scenario: Enable email notifications
#   Disable email default value
    Given the following "user preferences" exist:
      | user      | preference                                        | value |
      | student1  | message_provider_moodle_instantmessage_enabled    | none  |
    When I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    Then I should see "Notification preferences"
    And I should see "Email"
    And the field "Email" matches value "0"
    And I set the field "Email" to "1"
    And I follow "Preferences" in the user menu
    And I click on "Message preferences" "link"
    And the field "Email" matches value "1"

  Scenario: Disable email notifications
    Given I log in as "admin"
    When I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    Then I should see "Notification preferences"
    And I should see "Email"
    And the field "Email" matches value "1"
    And I set the field "Email" to "0"
    And I follow "Preferences" in the user menu
    And I click on "Message preferences" "link"
    And the field "Email" matches value "0"

  Scenario: Disable email notifications for Assignment notifications
    Given I log in as "admin"
    When I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    Then the field "email" matches value "1"
    And I set the field "mod_assign_assign_notification_disable" to "0"
    And I press "Save changes"
    And the field "mod_assign_assign_notification_disable" matches value "0"
    And I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    And I should not see "Assignment notifications"

  Scenario: User can disable email notifications for Assignment notifications
    Given I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    And I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    And I should not see "Enabled" in the "Assignment notifications" "table_row"
    When I set the field "message_provider_mod_assign_assign_notification_email" to "0"
    And I reload the page
    Then the field "message_provider_mod_assign_assign_notification_email" matches value "0"
    And I should not see "Enabled" in the "Assignment notifications" "table_row"

  @accessibility
  Scenario: Lock email notifications for Forum providers
    Given I log in as "admin"
    When I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    Then the field "email" matches value "1"
    And I set the field "mod_forum_posts_enabled[email]" to "1"
    And I set the field "mod_forum_posts_locked[email]" to "1"
    And I set the field "mod_forum_digests_enabled[email]" to "0"
    And I set the field "mod_forum_digests_locked[email]" to "1"
    And I press "Save changes"
    And the field "mod_forum_posts_enabled[email]" matches value "1"
    And the field "mod_forum_posts_locked[email]" matches value "1"
    And the field "mod_forum_digests_enabled[email]" matches value "0"
    And the field "mod_forum_digests_locked[email]" matches value "1"
    And I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    And I should see "Locked on" in the "[data-preference-key=message_provider_mod_forum_posts]" "css_element"
    And I should see "Locked off" in the "[data-preference-key=message_provider_mod_forum_digests]" "css_element"
    And the page should meet accessibility standards with "best-practice" extra tests

  Scenario: User can disable notification preferences
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following config values are set as admin:
      | popup_provider_mod_assign_assign_notification_locked    | 0     | message |
      | message_provider_mod_assign_assign_notification_enabled | popup | message |
    And the following "user preferences" exist:
      | user      | preference                                                | value |
      | student1  | message_provider_mod_assign_assign_notification_enabled   | none  |
      | student2  | message_provider_mod_assign_assign_notification_enabled   | popup  |
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
      | Test assignment name  | student2  | I'm the student2 submission  |
    When I log in as "student1"
    # Confirm the popover is not showing any unread notifications.
    Then I should not see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    And I open the notification popover
    # Confirm the submission notification is NOT visible.
    And I should not see "Assignment submission confirmation" in the "#nav-notification-popover-container" "css_element"
    And I log in as "student2"
    # Confirm the popover is showing the unread notifications.
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    And I open the notification popover
    # Confirm the submission notification is visible.
    And I should see "Assignment submission confirmation" in the "#nav-notification-popover-container" "css_element"

  Scenario: User cannot disable forced notification preferences
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | popup_provider_mod_assign_assign_notification_locked    | 1     | message |
      | message_provider_mod_assign_assign_notification_enabled | popup | message |
    And the following "user preferences" exist:
      | user      | preference                                                | value |
      | student1  | message_provider_mod_assign_assign_notification_enabled   | none  |
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
    When I log in as "student1"
    # Confirm the popover is saying 1 unread notifications.
    Then I should see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    And I open the notification popover
    # Confirm the submission notification is visible.
    And I should see "Assignment submission confirmation" in the "#nav-notification-popover-container" "css_element"

  Scenario: User cannot disable disallowed notification preferences
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | popup_provider_mod_assign_assign_notification_locked    | 1     | message |
      | message_provider_mod_assign_assign_notification_enabled | none  | message |
    And the following "user preferences" exist:
      | user      | preference                                                | value  |
      | student1  | message_provider_mod_assign_assign_notification_enabled   | popup  |
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
    When I log in as "student1"
    # Confirm the popover is not showing any unread notifications.
    Then I should not see "1" in the "#nav-notification-popover-container [data-region='count-container']" "css_element"
    # Open the popover.
    And I open the notification popover
    # Confirm the submission notification is NOT visible.
    And I should not see "Assignment submission confirmation" in the "#nav-notification-popover-container" "css_element"

  Scenario: Toggle notification preferences hides/displays options
    Given I log in as "admin"
    When I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    Then I should see "Subscribed forum posts"
    And I navigate to "Messaging > Notification settings" in site administration
    And I click on "Subscribed forum posts" "checkbox"
    And I follow "Preferences" in the user menu
    And I click on "Notification preferences" "link" in the "#page-content" "css_element"
    And I should not see "Subscribed forum posts"
