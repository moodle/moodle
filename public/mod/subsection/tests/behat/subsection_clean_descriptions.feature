@mod @mod_subsection
Feature: Subsection clean descriptions
  In order to manage subsection descriptions
  As an administrator
  I want to be able to delete or migrate subsection descriptions

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "activities" exist:
      | activity   | name        | course | idnumber    | section | summary                  |
      | subsection | Subsection1 | C1     | subsection1 | 1       | Test Subsection1 summary |
      | subsection | subsection3 | C1     | subsection3 | 1       |                          |
      | subsection | subsection2 | C2     | subsection2 | 1       | Test Subsection2 summary |

  @javascript
  Scenario: Migrate subsection descriptions
    Given I log in as "admin"
    And I navigate to "Plugins > Activity modules > Subsection" in site administration
    And I should see "This site has 2 subsection descriptions that are no longer visible to users."
    When I click on "Migrate descriptions" "link" in the "region-main" "region"
    And I should see "This will migrate 2 subsection descriptions to Text and Media areas." in the "Migrate all subsection descriptions?" "dialogue"
    And I click on "Migrate all descriptions" "button" in the "Migrate all subsection descriptions?" "dialogue"
    Then I should see "The migration task for all subsection descriptions has been created." in the "region-main" "region"
    And I should see "Subsection descriptions waiting to be migrated: 2" in the "region-main" "region"
    And I reload the page
    And I should see "The migration task for all subsection descriptions has been created." in the "region-main" "region"
    And I should see "Subsection descriptions waiting to be migrated: 2" in the "region-main" "region"
    And I run all adhoc tasks
    And I reload the page
    And I should not see "Subsection descriptions waiting to be migrated:" in the "region-main" "region"

  @javascript
  Scenario: Delete subsection descriptions
    Given I log in as "admin"
    And I navigate to "Plugins > Activity modules > Subsection" in site administration
    And I should see "This site has 2 subsection descriptions that are no longer visible to users."
    When I click on "Delete descriptions" "link" in the "region-main" "region"
    And I should see "This will permanently delete 2 subsection descriptions from the database." in the "Delete all subsection descriptions?" "dialogue"
    And I click on "Delete all descriptions" "button" in the "Delete all subsection descriptions?" "dialogue"
    Then I should see "2 subsection descriptions deleted." in the "region-main" "region"
    And I reload the page
    And I should not see "Subsection pages and descriptions are no longer supported in Moodle 5.2"
