@auth @core_auth @javascript
Feature: Test if the login form provides the correct feedback
  In order to check if the login form provides correct feedback
  As a user
  I need to go on login page and see feedback on incorrect username or password.

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |

  Scenario: Check invalid login message
    Given I follow "Log in"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "incorrect"
    When I press "Log in"
    Then I should see "Invalid login, please try again"

  Scenario: Test login language selector
    Given remote langimport tests are enabled
    And the following "language packs" exist:
      | language |
      | nl       |
      | es       |
    And the following config values are set as admin:
      | langmenu | 1 |
    And I follow "Log in"
    And I open the action menu in "region-main" "region"
    # The line below contains the unicode character U+200E before and after the brackets, please be very careful editing this line.
    When I choose "Nederlands ‎(nl)‎" in the open action menu
    Then I should see "Gebruikersnaam"

  @_file_upload
  Scenario: Set logo for loginpage
    Given I log in as "admin"
    And I navigate to "Appearance > Logos" in site administration
    And I upload "course/tests/fixtures/image.jpg" file to "Logo" filemanager
    And I press "Save changes"
    And I log out
    And I follow "Log in"
    Then "//img[@id='logoimage']" "xpath_element" should exist

  Scenario: Add a custom welcome message
    Given the following config values are set as admin:
      | auth_instructions | Lorem ipsum dolor sit amet |
    And I follow "Log in"
    Then I should see "Lorem ipsum dolor sit amet"

  @javascript @accessibility
  Scenario: Show the maintenance mode message
    Given the following config values are set as admin:
      | maintenance_enabled | 1                     |
      | maintenance_message | Back online tomorrow  |
    And I follow "Log in"
    Then I should see "Back online tomorrow"
    And the page should meet accessibility standards with "best-practice" extra tests

  Scenario: User self registration
    Given the following config values are set as admin:
      | registerauth | Email-based self-registration |
    And I follow "Log in"
    Then I should see "Create new account"

  Scenario: Set OAuth providers
    Given I log in as "admin"
    And I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Enable" "link" in the "OAuth 2" "table_row"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I press "Google"
    And I set the field "Client ID" to "1234"
    And I set the field "Client secret" to "1234"
    And I press "Save changes"
    And I press "Facebook"
    And I set the field "Client ID" to "1234"
    And I set the field "Client secret" to "1234"
    And I press "Save changes"
    And I press "Microsoft"
    And I set the field "Client ID" to "1234"
    And I set the field "Client secret" to "1234"
    And I press "Save changes"
    And I log out
    And I follow "Log in"
    Then I should see "Google"
    And I should see "Facebook"
    And I should see "Microsoft"

  Scenario: Test the login page auto focus feature
    Given the following config values are set as admin:
      | loginpageautofocus | Enabled |
    And I follow "Log in"
    Then the focused element is "Username" "field"
    And I set the field "Username" to "admin"
    And I set the field "Password" to "admin"
    And I press "Log in"
    And I log out
    And I follow "Log in"
    Then the focused element is "Password" "field"

  Scenario: Test the login page focus after error feature
    Given I follow "Log in"
    And I set the field "Username" to "admin"
    And I set the field "Password" to "wrongpassword"
    And I press "Log in"
    And I press the tab key
    Then the focused element is "Username" "field"

  Scenario: Display the password visibility toggle icon
    Given the following config values are set as admin:
      | loginpasswordtoggle | 1 |
    When I follow "Log in"
    Then "Toggle sensitive" "button" should be visible
    And the following config values are set as admin:
      | loginpasswordtoggle | 0 |
    And I reload the page
    And "Toggle sensitive" "button" should not be visible

  Scenario: Display the password visibility toggle icon for small screens only
    Given the following config values are set as admin:
      | loginpasswordtoggle | 2 |
    When I follow "Log in"
    Then "Toggle sensitive" "button" should not be visible
    And I change the viewport size to "mobile"
    And "Toggle sensitive" "button" should be visible
