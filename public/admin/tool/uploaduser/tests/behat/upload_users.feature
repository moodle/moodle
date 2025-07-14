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
    And I navigate to "Users > Accounts > Upload users" in site administration
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
    And I am on the "Maths" "groups" page
    And I set the field "groups" to "Section 1 (1)"
    And the "members" select box should contain "Tom Jones (jonest@example.com)"

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
    And I should see "Upload users preview"
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
    Given the following "custom profile fields" exist:
      | datatype | shortname  | name        |
      | text     | superfield | Super field |
    And I log in as "admin"
    # Upload users.
    When I navigate to "Users > Accounts > Upload users" in site administration
    And I upload "lib/tests/fixtures/upload_users_profile.csv" file to "File" filemanager
    And I press "Upload users"
    And I should see "Upload users preview"
    And I press "Upload users"
    # Check that users were created and the superfield is filled.
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Tom Jones"
    And I should see "Super field"
    And I should see "The big guy"
    And I log out

  @javascript
  Scenario: Upload users setting their email stop value
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_emailstop.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And the following should exist in the "uupreview" table:
      | CSV line | username | emailstop |
      | 2        | jbloggs  | 1         |
      | 3        | fbloggs  | 0         |
    And I press "Upload users"
    And I should see "Users created: 2"
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
    And I navigate to "Appearance > Advanced theme settings" in site administration
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
    # Boost check.
    And I am on the "jonest@example.com" "user > editing" page
    And I should see "Boost"
    # Classic check.
    And I am on the "reznor@example.com" "user > editing" page
    And I should see "Classic"

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
    And I click on "Close" "button" in the "Enrolment details" "dialogue"
    And I click on "Manual enrolments" "link" in the "Student Two" "table_row"
    Then I should see "2 January 2020" in the "Enrolment starts" "table_row"
    And I should see "12 January 2020" in the "Enrolment ends" "table_row"
    And I click on "Close" "button" in the "Enrolment details" "dialogue"
    And I log out

  @javascript
  Scenario: Upload users enrolling them on courses and assign category roles
    Given the following "courses" exist:
      | fullname | shortname |
      | management1 | management1 |
      | film1 | film1 |
    And the following "categories" exist:
      | name | idnumber |
      | MGMT | MGMT |
      | Film | Film |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_category.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I should see "Tom"
    And I should see "Jones"
    And I should see "Trent"
    And I should see "Reznor"
    And I should see "Aurora"
    And I should see "Jiang"
    And I should see "Federico"
    And I should see "Fellini"
    And I should see "Ivan"
    And I should see "Ivanov"
    And I should see "John"
    And I should see "Smith"
    And I should see "Warm"
    And I should see "Cool"
    And I should see "James"
    And I should see "Bond"
    And I should see "MGMT"
    And I should see "Film"
    And I should see "manager"
    And I should see "student"
    And I should see "coursecreator"
    And I should see "management1"
    And I should see "film1"
    And I press "Upload users"
    And I should see "Unknown category with category ID number \"Movie\""
    And I should see "Unknown course named \"movie1\""
    And I should see "Unknown role \"notcoursecreator\""
    And I should see "Could not assign role to user: missing role for category"
    And I press "Continue"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Tom Jones"
    And I should see "Trent Reznor"
    And I should see "reznor@example.com"
    And I am on the "management1" "enrolled users" page
    And I should see "Tom Jones"
    And I should see "Trent Reznor"
    And I should see "Aurora Jiang"
    And I should see "Student"
    And I am on the "film1" "enrolled users" page
    And I should see "Federico Fellini"
    And I should see "Student"
    And I am on site homepage
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "permissions" action for "MGMT" in management category listing
    And I set the field "Participants tertiary navigation" to "Assign roles"
    And I should see "Manager"
    And I should see "Tom Jones"
    And I should see "Trent Reznor"
    And I should see "Course creator"
    And I should see "Aurora Jiang"
    And I am on site homepage
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "permissions" action for "Film" in management category listing
    And I set the field "Participants tertiary navigation" to "Assign roles"
    And I should see "Course creator"
    And I should see "Federico Fellini"

  @javascript
  Scenario: Update existing users matching them on email
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | bilbob   | Blasbo    | Blabbins | bilbo@example.com |
      | frodob   | Frodeo    | Baspins  | frodo@example.com |
    And I log in as "admin"
    And I navigate to "Users > Accounts >Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_email_matching.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I set the following fields to these values:
      | Upload type  | Update existing users only |
      | Existing user details | Override with file |
      | Match on email address | Yes |
    And I press "Upload users"
    And I press "Continue"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Bilbo Baggins"
    And I should see "Frodo Baggins"

  @javascript
  Scenario: Update existing users matching them on email where one email address is associated with multiple users
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | bilbob   | Blasbo    | Blabbins | bilbo@example.com |
      | frodob   | Frodeo    | Baspins  | frodo@example.com |
      | fredob   | Fredoo    | Baspins  | frodo@example.com |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_email_matching.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I set the following fields to these values:
      | Upload type  | Update existing users only |
      | Existing user details | Override with file |
      | Match on email address | Yes |
    And I press "Upload users"
    And I should see "Multiple users with email frodo@example.com detected"
    And I press "Continue"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Bilbo Baggins"
    And I should not see "Frodo Baggins"

  @javascript
  Scenario: Create a new user when matching them on email where where the username already exists
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | bilbob   | Samwise   | Gamgee   | samwise@example.com |
      | frodob   | Frodeo    | Baspins  | frodo@example.com   |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/upload_users_email_matching.csv" file to "File" filemanager
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I set the following fields to these values:
      | Upload type            | Add new and update existing users |
      | Existing user details  | Override with file                |
      | Match on email address | Yes                               |
    And I press "Upload users"
    And I should see "User not added - username already exists under a different email"
    And I press "Continue"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Samwise Gamgee"
    And I should see "Frodo Baggins"
