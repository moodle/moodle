@core @core_message @javascript
Feature: Manage notification preferences - Email
  In order to be notified of messages
  As a user
  I need to be able to update my messaging notification preferences

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following config values are set as admin:
      | messaging | 1 |

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
      | student1  | message_provider_moodle_instantmessage_loggedin   | none  |
      | student1  | message_provider_moodle_instantmessage_loggedoff  | none  |
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

  Scenario: Config jabber
    Given the following config values are set as admin:
      | jabberhost | jabber.org |
      | jabberusername | moodlemessageservice |
      | jabberpassword | password |
    When I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "jabber" to "1"
    And I press "Save changes"
    And I click on "Settings" "link" in the "Jabber" "table_row"
    And I navigate to "Messaging > Notification settings" in site administration
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    Then I should not see "Notification preferences"
    And I should not see "Jabber"
    And I open the notification popover
    And I follow "Notification preferences"
    And I click on "Jabber" "link"
    And I should see "Jabber ID"
    And I set the field "jabber_jabberid" to "student1@jabber.org"
    And I click on "Save changes" "button" in the ".modal-dialog" "css_element"
    And I am on homepage
    And I open messaging
    And I open messaging settings preferences
    And I should see "Notification preferences"
    And I should see "Jabber"
