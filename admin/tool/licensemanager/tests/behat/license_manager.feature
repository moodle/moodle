@tool @tool_licensemanager
Feature: Licence manager
  In order to manage licences
  As an admin
  I need to be able to view and alter licence preferences in the licence manager.

  Scenario: I should be able to see the default Moodle licences.
    Given I log in as "admin"
    When I navigate to "Licence > Licence manager" in site administration
    Then I should see "Licence not specified" in the "unknown" "table_row"
    And I should see "All rights reserved" in the "allrightsreserved" "table_row"
    And I should see "Public domain" in the "public" "table_row"
    And I should see "Creative Commons - 4.0 International" in the "cc-4.0" "table_row"
    And I should see "Creative Commons - NoDerivatives 4.0 International" in the "cc-nd-4.0" "table_row"
    And I should see "Creative Commons - NonCommercial-NoDerivatives 4.0 International" in the "cc-nc-nd-4.0" "table_row"
    And I should see "Creative Commons - NonCommercial-ShareAlike 4.0 International" in the "cc-nc-sa-4.0" "table_row"
    And I should see "Creative Commons - ShareAlike 4.0 International" in the "cc-sa-4.0" "table_row"
    And I should see "Creative Commons - NonCommercial 4.0 International" in the "cc-nc-4.0" "table_row"

  Scenario: I should be able to enable and disable licences
    Given I log in as "admin"
    And I navigate to "Licence > Licence settings" in site administration
    When I set the field "Default site licence" to "Public domain"
    And I press "Save changes"
    And I navigate to "Licence > Licence manager" in site administration
    Then "This is the site default licence" "icon" should exist in the "public" "table_row"
    And "Enable licence" "icon" should not exist in the "public" "table_row"
    And "This is the site default licence" "icon" should not exist in the "cc-4.0" "table_row"
    And I navigate to "Licence > Licence settings" in site administration
    And I set the field "Default site licence" to "Creative Commons"
    And I press "Save changes"
    And I navigate to "Licence > Licence manager" in site administration
    And "This is the site default licence" "icon" should exist in the "cc-4.0" "table_row"
    And "Enable licence" "icon" should not exist in the "cc-4.0" "table_row"
    And "This is the site default licence" "icon" should not exist in the "public" "table_row"

  @javascript @_file_upload
  Scenario Outline: User licence preference is remembered depending of setting value
    Given the following config values are set as admin:
      | sitedefaultlicense      | cc-4.0                       |
      | rememberuserlicensepref | <rememberuserlicensepref> |
    And I log in as "admin"
    And I follow "Private files" in the user menu
    And I follow "Add..."
    And I follow "Upload a file"
    And the field with xpath "//select[@name='license']" matches value "Creative Commons - 4.0 International"
    And I click on "Close" "button" in the "File picker" "dialogue"
    When I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager as:
      | Save as | empty_copy.txt |
      | license | Public domain |
    And I press "Save changes"
    And I follow "Add..."
    Then the field with xpath "//select[@name='license']" matches value "<expectedlicence>"

    Examples:
      | rememberuserlicensepref | expectedlicence                      |
      | 0                       | Creative Commons - 4.0 International |
      | 1                       | Public domain                        |
