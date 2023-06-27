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
      | student1  | message_provider_moodle_instantmessage_loggedin   | none  |
      | student1  | message_provider_moodle_instantmessage_loggedoff  | email |

  @javascript
  Scenario: As an admin I can view and edit message preferences for a user
    Given I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "Student 1" "link" in the "Student 1" "table_row"
    And I click on "Preferences" "link" in the "#region-main-box" "css_element"
    And I click on "Message preferences" "link" in the "#region-main-box" "css_element"
    And I click on "//label[@data-state='loggedoff']" "xpath_element"
    And I log out
    And I log in as "student1"
    And I follow "Preferences" in the user menu
    And I click on "Message preferences" "link"
    And the field "Email" matches value "0"
