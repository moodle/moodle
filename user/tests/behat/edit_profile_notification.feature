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
    And I follow "Profile" in the user menu
    When I click on "Edit profile" "link" in the "region-main" "region"
    And I should see "Unicorn"
    And I should see "1"
    Then I set the field "Surname" to "Lil"
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
    And I follow "Profile" in the user menu
    When I click on "Edit profile" "link" in the "region-main" "region"
    And I should see "Unicorn"
    And I should see "1"
    Then I set the field "Surname" to "Lil"
    And I click on "Cancel" "button"
    And I should not see "Changes saved"

  @javascript
  Scenario: Show notification after admin edited profile of another user
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    When I click on "Edit" "link" in the "Unicorn 1" "table_row"
    And I expand all fieldsets
    Then I set the field "Surname" to "Lil"
    And I click on "Update profile" "button"
    And I should see "Changes saved"
