@tool @tool_uploaduser @_file_upload
Feature: Upload users
  In order to add users to the system
  As an admin
  I need to upload files containing the users data

  @javascript
  Scenario: Upload users enrolling them on courses and groups
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Maths | math102 | 0 |
    And the following "groups" exist:
      | name | course | idnumber |
      | Section 1 | math102 | S1 |
      | Section 3 | math102 | S3 |
    And I log in as "admin"
    And I navigate to "Users > Accounts >Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I should see "Tom"
    And I should see "Jones"
    And I should see "verysecret"
    And I should see "jonest@example.com"
    And I should see "Reznor"
    And I should see "course1"
    And I should see "math102"
    And I should see "group1"
    And I should see "Section 1"
    And I press "Upload users"
    And I press "Continue"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Tom Jones"
    And I should see "Trent Reznor"
    And I should see "reznor@example.com"
    And I am on "Maths" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I set the field "groups" to "Section 1 (1)"
    And the "members" select box should contain "Tom Jones"

  @javascript
  Scenario: Upload users enrolling them on courses and groups applying defaults
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Maths | math102 | 0 |
    And the following "groups" exist:
      | name | course | idnumber |
      | Section 1 | math102 | S1 |
      | Section 3 | math102 | S3 |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users.csv" file to "File" filemanager
    And I press "Upload users"
    And I set the following fields to these values:
      | City/town  | Brighton   |
      | Department | Purchasing |
    And I press "Upload users"
    And I press "Continue"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Tom Jones"
    And I follow "Tom Jones"
    And I follow "Edit profile"
    And the field "City/town" matches value "Brighton"
    And the field "Department" matches value "Purchasing"

  @javascript
  Scenario: Upload users with custom profile fields
    # Create user profile field.
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I set the field "datatype" to "Text area"
    And I set the following fields to these values:
      | Short name | superfield  |
      | Name       | Super field |
    And I click on "Save changes" "button"
    # Upload users.
    When I navigate to "Users > Accounts > Upload users" in site administration
    And I upload "lib/tests/fixtures/upload_users_profile.csv" file to "File" filemanager
    And I press "Upload users"
    And I press "Upload users"
    # Check that users were created and the superfield is filled.
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Tom Jones"
    And I should see "Super field"
    And I should see "The big guy"
    And I log out

  @javascript
  Scenario: Upload users setting their user theme
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Maths    | math102   | 0        |
    # We need to do a bit of setup here.
    And I change window size to "large"
    And I log in as "admin"
    And I navigate to "Security > Site security settings" in site administration
    And I click on "Password policy" "checkbox"
    And I click on "Save changes" "button"
    And I navigate to "Appearance > Themes > Theme settings" in site administration
    And I click on "Allow user themes" "checkbox"
    And I click on "Save changes" "button"
    # Upload the users.
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_themes.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I should see "boost"
    And I should see "classic"
    And I should see "No theme is defined for this user."
    And I should see "Theme \"somefaketheme\" is not installed and will be ignored."
    And I press "Upload users"
    And I should see "Users created: 4"
    And I press "Continue"
    And I log out
    # Boost check.
    And I log in as "jonest"
    And I am on "Maths" course homepage
    And "Turn editing on" "button" should not exist
    And I log out
    # Classic check.
    And I log in as "reznor"
    And I am on "Maths" course homepage
    And "Turn editing on" "button" should exist

  @javascript
  Scenario: Upload users setting their user theme when allowuserthemes is false
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Maths    | math102   | 0        |
    # We need to do a bit of setup here.
    And I change window size to "large"
    And I log in as "admin"
    And I navigate to "Security > Site security settings" in site administration
    And I click on "Password policy" "checkbox"
    And I click on "Save changes" "button"
    # Upload the users.
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_themes.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I should see "boost"
    And I should see "classic"
    And I press "Upload users"
    And I should see "User themes are not enabled, so any included in the upload users file will be ignored."
    And I should see "Users created: 4"
    And I press "Continue"
    And I log out

  @javascript
  Scenario: Upload users setting their enrol date and period
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Maths    | math102   | 0        |
    # Upload the users.
    And I change window size to "large"
    And I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_enrol_date_period.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I press "Upload users"
    # Check user enrolment start date and period
    And I am on "Maths" course homepage
    Then I navigate to course participants
    And I click on "Manual enrolments" "link" in the "Student One" "table_row"
    Then I should see "1 January 2019" in the "Enrolment starts" "table_row"
    And I should not see "Enrolment ends"
    And I click on "Close" "button"
    And I click on "Manual enrolments" "link" in the "Student Two" "table_row"
    Then I should see "2 January 2020" in the "Enrolment starts" "table_row"
    And I should see "12 January 2020" in the "Enrolment ends" "table_row"
    And I click on "Close" "button"
    And I log out
