@core @core_user
Feature: Both first name and surname are always available for every user
  In order to easily identify and display users on Moodle pages
  As any user
  I need to rely on both first name and surname are always available

  Scenario: Attempting to self-register as a new user with empty names
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
    And I am on site homepage
    And I follow "Log in"
    And I press "Create new account"
    When I set the following fields to these values:
      | Username      | mrwhitespace        |
      | Password      | Gue$$m3ifY0uC&n     |
      | Email address | mrwhitespace@nas.ty |
      | Email (again) | mrwhitespace@nas.ty |
    And I set the field "First name" to " "
    And I set the field "Surname" to " "
    And I press "Create my new account"
    Then I should see "Missing given name"
    And I should see "Missing surname"

  Scenario: Attempting to change own names to whitespace
    Given the following "users" exist:
      | username | firstname | lastname | email       |
      | foobar   | Foo       | Bar      | foo@bar.com |
    And I log in as "foobar"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    When I set the field "First name" to " "
    And I set the field "Surname" to " "
    And I click on "Update profile" "button"
    Then I should see "Missing given name"
    And I should see "Missing surname"

  Scenario: Attempting to change someone else's names to whitespace
    Given the following "users" exist:
      | username | firstname | lastname | email       |
      | foobar   | Foo       | Bar      | foo@bar.com |
    And I log in as "admin"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I follow "Foo Bar"
    And I click on "Edit profile" "link" in the "region-main" "region"
    When I set the field "First name" to " "
    And I set the field "Surname" to " "
    And I click on "Update profile" "button"
    Then I should see "Missing given name"
    And I should see "Missing surname"
