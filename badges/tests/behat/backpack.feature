@core @core_badges @_file_upload
Feature: Backpack badges
  Test the settings to add/update a backpack for a site and user.
  I need to verify display backpack in the my profile

  Background:
    Given the following "badge external backpacks" exist:
      | backpackapiurl                               | backpackweburl           | apiversion | sortorder |
      | https://dc.imsglobal.org/obchost/ims/ob/v2p1 | https://dc.imsglobal.org | 2.1        | 2         |
      | https://test.com/                            | https://test.com/        | 2          | 3         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the field "Badge issuer name" to "Test Badge Site"
    And I set the field "Badge issuer email address" to "testuser@example.com"
    And I log out

  @javascript
  Scenario: If external backpack connection is disabled, backpack settings should not be displayed
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 0                        |
    And I press "Save changes"
    When I navigate to "Badges" in site administration
    Then I should not see "Manage backpacks"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
    And I press "Save changes"
    And I am on homepage
    And I navigate to "Badges" in site administration
    And I should see "Manage backpacks"

  @javascript
  Scenario: Verify backback settings
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
    And I press "Save changes"
    And I navigate to "Badges > Manage backpacks" in site administration
    And I click on "Move up" "link" in the "https://dc.imsglobal.org" "table_row"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name          | Test badge verify backpack |
      | Version       | v1                         |
      | Language      | English                    |
      | Description   | Test badge description     |
      | Image author  | http://author.example.com  |
      | Image caption | Test caption image         |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    And I log out
    When I am on homepage
    And I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    Then I should see "https://dc.imsglobal.org"
    And I should see "Not connected"

  @javascript
  Scenario: User has been connected backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
    And I press "Save changes"
    And I navigate to "Badges > Manage backpacks" in site administration
    And I click on "Move up" "link" in the "https://dc.imsglobal.org" "table_row"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name           | Test badge verify backpack |
      | Version        | v1                         |
      | Language       | English                    |
      | Description    | Test badge description     |
      | Image author   | http://author.example.com  |
      | Image caption  | Test caption image         |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    And I log out
    And the following "setup backpack connected" exist:
      | user     | externalbackpack         |
      | student1 | https://dc.imsglobal.org |
    When I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    Then I should see "Connected"
    And I follow "Preferences" in the user menu
    And I follow "Manage badges"
    And I should see "Test badge verify backpack"
    And "Add to backpack" "link" should exist

  @javascript
  Scenario: Add a new site backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "backpackapiurl" to "http://backpackapiurl.cat"
    And I set the field "backpackweburl" to "aaa"
    And I press "Save changes"
    And I should see "Invalid URL"
    And I set the field "backpackweburl" to "http://backpackweburl.cat"
    And I press "Save changes"
    Then I should see "http://backpackweburl.cat"
    And "Delete" "icon" should exist in the "http://backpackweburl.cat" "table_row"
    And "Edit settings" "icon" should exist in the "http://backpackweburl.cat" "table_row"

  @javascript
  Scenario: Remove a site backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I click on "Delete" "link" in the "https://dc.imsglobal.org" "table_row"
    And I should see "Delete site backpack 'https://dc.imsglobal.org'?"
    And I click on "Delete" "button" in the "Delete site backpack" "dialogue"
    Then I should see "The site backpack has been deleted."
    And I should not see "https://dc.imsglobal.org"
    And "Delete" "button" should not be visible

  @javascript
  Scenario: Move up and down site backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    And "Move up" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    And "Move down" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    When I click on "Move up" "link" in the "https://dc.imsglobal.org" "table_row"
    Then "Move up" "icon" should not exist in the "https://dc.imsglobal.org" "table_row"
    And "Move down" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    And I click on "Move down" "link" in the "https://dc.imsglobal.org" "table_row"
    And I click on "Move down" "link" in the "https://dc.imsglobal.org" "table_row"
    And "Move up" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    And "Move down" "icon" should not exist in the "https://dc.imsglobal.org" "table_row"

  @javascript
  Scenario: Add a new site backpack with authentication details checkbox
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "backpackapiurl" to "http://backpackapiurl.cat"
    And I set the field "backpackweburl" to "http://backpackweburl.cat"
    And I set the field "apiversion" to "2.1"
    Then "Include authentication details with the backpack" "checkbox" should not be visible
    And I should not see "Badge issuer email address"
    And I should not see "Badge issuer password"
    And I set the field "apiversion" to "1"
    And "Include authentication details with the backpack" "checkbox" should be visible
    And I click on "includeauthdetails" "checkbox"
    And I should see "Badge issuer email address"
    And I should not see "Badge issuer password"
    And I set the field "apiversion" to "2"
    And "Include authentication details with the backpack" "checkbox" should be visible
    And I should see "Badge issuer email address"
    And I should see "Badge issuer password"
    And I set the field "backpackemail" to "test@test.com"
    And I set the field "password" to "123456"
    And I press "Save changes"
    And I click on "Edit" "link" in the "http://backpackweburl.cat" "table_row"
    And the field "Include authentication details with the backpack" matches value "1"
    And I click on "includeauthdetails" "checkbox"
    And I press "Save changes"
    And I click on "Edit" "link" in the "http://backpackweburl.cat" "table_row"
    And the field "Include authentication details with the backpack" matches value "0"
    And I click on "includeauthdetails" "checkbox"
    And I should not see "test@test.com"
    And I log out

  @javascript
  Scenario: View backpack form as a student
    Given I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    When I set the field "externalbackpackid" to "https://dc.imsglobal.org"
    Then I should not see "Email address"
    And I should not see "Password"
    And I set the field "externalbackpackid" to "https://test.com/"
    And I should see "Email address"
    And I should see "Password"
