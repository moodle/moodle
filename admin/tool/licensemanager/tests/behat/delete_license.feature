@tool @tool_licensemanager
Feature: Delete custom licenses
    In order to manage custom licenses
    As an admin
    I need to be able to delete custom licenses but not standard Moodle licenses

  @javascript
  Scenario: I can delete a custom license
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Create licence" "link"
    And I set the following fields to these values:
      | shortname       | MIT                                 |
      | fullname        | MIT Licence                         |
      | source          | https://opensource.org/licenses/MIT |
      | Licence version | ##1 March 2019##                    |
    And I press "Save changes"
    And I click on "Delete" "icon" in the "MIT" "table_row"
    When I click on "Yes" "button" in the "Delete licence" "dialogue"
    Then I should not see "MIT Licence" in the "manage-licenses" "table"

  Scenario: I cannot delete a standard license
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    Then I should see "Licence not specified" in the "unknown" "table_row"
    And I should not see "Delete" in the "unknown" "table_row"

  @javascript @_file_upload
  Scenario: I cannot delete a custom license in use
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Create licence" "link"
    And I set the following fields to these values:
      | shortname       | Test licence                        |
      | fullname        | Test licence                        |
      | source          | https://opensource.org/licenses/MIT |
      | Licence version | ##1 March 2019##                    |
    And I press "Save changes"
    And I follow "Private files" in the user menu
    And I upload "lib/tests/fixtures/gd-logo.png" file to "Files" filemanager
    And I click on "gd-logo.png" "link"
    And I set the field "Choose licence" to "Test licence"
    And I press "Update"
    And I press "Save changes"
    And I am on site homepage
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Delete" "icon" in the "Test licence" "table_row"
    When I click on "Yes" "button" in the "Delete licence" "dialogue"
    Then I should see "Cannot delete a licence which is currently assigned to one or more files"
