@core @core_user
Feature: Both first name and last name are always available for every user
  In order to easily identify and display users on Moodle pages
  As any user
  I need to rely on both first name and last name are always available

  Scenario: Attempting to self-register as a new user with empty names
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
    And I am on site homepage
    And I follow "Log in"
    And I click on "Create new account" "link"
    When I set the following fields to these values:
      | Username      | mrwhitespace        |
      | Password      | Gue$$m3ifY0uC&n     |
      | Email address | mrwhitespace@nas.ty |
      | Email (again) | mrwhitespace@nas.ty |
    And I set the field "First name" to " "
    And I set the field "Last name" to " "
    And I press "Create my new account"
    Then I should see "Missing given name"
    And I should see "Missing last name"

  Scenario: Attempting to change own names to whitespace
    Given the following "users" exist:
      | username | firstname | lastname | email       |
      | foobar   | Foo       | Bar      | foo@bar.com |
    And I log in as "foobar"
    # UI test covering "I open my profile in edit mode" -
    # This should be one of the very few places where we directly call these 2 steps to open the current users profile
    # in edit mode, the rest of the time you should use "I open my profile in edit mode" as it is faster.
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    # End UI test covering "I open my profile in edit mode"
    When I set the field "First name" to " "
    And I set the field "Last name" to " "
    And I click on "Cancel" "button"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "Foo"
    And I should see "Bar"
    When I set the field "First name" to " "
    And I set the field "Last name" to " "
    And I click on "Update profile" "button"
    Then I should see "Missing given name"
    And I should see "Missing last name"

  Scenario: Attempting to change someone else's names to whitespace
    Given the following "users" exist:
      | username | firstname | lastname | email       |
      | foobar   | Foo       | Bar      | foo@bar.com |
    And I am on the "foobar" "user > editing" page logged in as "admin"
    When I set the field "First name" to " "
    And I set the field "Last name" to " "
    And I click on "Cancel" "button"
    And I follow "Foo Bar"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "Foo"
    And I should see "Bar"
    When I set the field "First name" to " "
    And I set the field "Last name" to " "
    And I click on "Update profile" "button"
    Then I should see "Missing given name"
    And I should see "Missing last name"
