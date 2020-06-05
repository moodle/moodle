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
    And I should see "Creative Commons" in the "cc" "table_row"
    And I should see "Creative Commons - NoDerivs" in the "cc-nd" "table_row"
    And I should see "Creative Commons - No Commercial NoDerivs" in the "cc-nc-nd" "table_row"
    And I should see "Creative Commons - No Commercial" in the "cc-nc" "table_row"
    And I should see "Creative Commons - No Commercial ShareAlike" in the "cc-nc-sa" "table_row"
    And I should see "Creative Commons - ShareAlike" in the "cc-sa" "table_row"

  Scenario: I should be able to enable and disable licences
    Given I log in as "admin"
    And I navigate to "Licence > Licence settings" in site administration
    When I set the field "Default site licence" to "Public domain"
    And I press "Save changes"
    And I navigate to "Licence > Licence manager" in site administration
    Then "This is the site default licence" "icon" should exist in the "public" "table_row"
    And "Enable licence" "icon" should not exist in the "public" "table_row"
    And "This is the site default licence" "icon" should not exist in the "cc" "table_row"
    And I navigate to "Licence > Licence settings" in site administration
    And I set the field "Default site licence" to "Creative Commons"
    And I press "Save changes"
    And I navigate to "Licence > Licence manager" in site administration
    And "This is the site default licence" "icon" should exist in the "cc" "table_row"
    And "Enable licence" "icon" should not exist in the "cc" "table_row"
    And "This is the site default licence" "icon" should not exist in the "public" "table_row"
