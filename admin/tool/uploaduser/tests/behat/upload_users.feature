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
    And I navigate to "Upload users" node in "Site administration > Users > Accounts"
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
    And I follow "Browse list of users"
    And I should see "Tom Jones"
    And I should see "Trent Reznor"
    And I should see "reznor@example.com"
    And I am on site homepage
    And I follow "Maths"
    And I expand "Users" node
    And I follow "Groups"
    And I set the field "groups" to "Section 1 (1)"
    And the "members" select box should contain "Tom Jones"

  @javascript
  Scenario: Upload users with custom profile fields
    # Create user profile field.
    Given I log in as "admin"
    And I navigate to "User profile fields" node in "Site administration > Users > Accounts"
    And I set the field "datatype" to "Text area"
    And I set the following fields to these values:
      | Short name | superfield  |
      | Name       | Super field |
    And I click on "Save changes" "button"
    # Upload users.
    When I navigate to "Upload users" node in "Site administration > Users > Accounts"
    And I upload "lib/tests/fixtures/upload_users_profile.csv" file to "File" filemanager
    And I press "Upload users"
    And I press "Upload users"
    # Check that users were created and the superfield is filled.
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I follow "Tom Jones"
    And I should see "Super field"
    And I should see "The big guy"
    And I log out
