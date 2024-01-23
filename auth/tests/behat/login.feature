@core @core_auth
Feature: Authentication
  In order to validate my credentials in the system
  As a user
  I need to log into the system

  Scenario: Log in with the predefined admin user with Javascript disabled
    Given I log in as "admin"
    Then I should see "You are logged in as Admin User" in the "page-footer" "region"

  @javascript
  Scenario: Log in with the predefined admin user with Javascript enabled
    Given I log in as "admin"
    Then I should see "You are logged in as Admin User" in the "page-footer" "region"

  Scenario: Log in as an existing admin user filling the form
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@example.com |
    And I am on site homepage
    When I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "testuser"
    And I press "Log in"
    Then I should see "You are logged in as" in the "page-footer" "region"

  Scenario: Log in as an unexisting user filling the form
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@example.com |
    And I am on site homepage
    When I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "unexisting"
    And I press "Log in"
    Then I should see "Invalid login, please try again"

  Scenario: Log out using the Log out link
    Given I log in as "admin"
    When I click on "Log out" "link" in the "#page-footer" "css_element"
    Then I should see "You are not logged in" in the "page-footer" "region"

  @javascript @accessibility
  Scenario: Login page must be accessible
    When I am on site homepage
    # The following tests are all provided to ensure that the accessibility tests themselves are tested.
    # In normal tests only one of the following is required.
    Then the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests

    And I follow "Log in"
    And the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests

  @javascript @accessibility
  Scenario: The login page must have sufficient colour contrast
    Given the following config values are set as admin:
      | custommenuitems | -This is a custom item\|/customurl/ |
    When I am on site homepage
    Then the page should meet "wcag143" accessibility standards
    And the page should meet accessibility standards with "wcag143" extra tests

  Scenario: Alternate login URL can be bypassed
    Given the following config values are set as admin:
        | alternateloginurl | https://www.google.com/ |
    And I am on site homepage
    When I visit "/login/index.php?loginredirect=0"
    Then I should see "Log in to Acceptance test site"
