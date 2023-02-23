@core @core_user
Feature: Notification shown when user edit profile or preferences
  In order to show notification
  As a user
  I press update profile button after make some changes in edit profile page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | unicorn  | Unicorn   | 1        | unicorn@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | unicorn | C1     | student |

  @javascript
  Scenario: Change own profile and has notification shown
    Given I log in as "unicorn"
    And I open my profile in edit mode
    And I should see "Unicorn"
    And I should see "1"
    Then I set the field "Last name" to "Lil"
    And I click on "Update profile" "button"
    And I should see "Changes saved"
    And I press "Dismiss this notification"
    And I should not see "Changes saved"
    And I follow "Preferences" in the user menu
    And I follow "Preferred language"
    And I click on "Save changes" "button"
    And I should see "Changes saved"
    And I follow "Forum preferences"
    And I set the field "Use experimental nested discussion view" to "Yes"
    And I click on "Save changes" "button"
    And I should see "Changes saved"

  @javascript
  Scenario: Do not show notification when cancel profile change
    Given I log in as "unicorn"
    And I open my profile in edit mode
    And I should see "Unicorn"
    And I should see "1"
    Then I set the field "Last name" to "Lil"
    And I click on "Cancel" "button"
    And I should not see "Changes saved"

  @javascript
  Scenario: Show notification after admin edited profile of another user
    Given I am on the "unicorn" "user > editing" page logged in as "admin"
    And I expand all fieldsets
    Then I set the field "Last name" to "Lil"
    And I click on "Update profile" "button"
    And I should see "Changes saved"
