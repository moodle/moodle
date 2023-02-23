@block @block_glossary_random @javascript
Feature: Add the glossary random block when main feature is disabled
  In order to add the glossary random block to my course
  As a teacher
  It should be added to courses only if the glossary module is enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And I am on the "C1" "course" page logged in as "admin"

  Scenario: The glossary random block is displayed even when glossary module is disabled
    Given I turn editing mode on
    And I add the "Random glossary entry" block
    When I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I click on "Hide" "icon" in the "Glossary" "table_row"
    And I am on "Course 1" course homepage with editing mode on
    Then I should see "Random glossary entry"

  Scenario: The glossary random block can be removed even when glossary module is disabled
    Given I turn editing mode on
    And I add the "Random glossary entry" block
    And I open the "Random glossary entry" blocks action menu
    And I click on "Delete Random glossary entry block" "link" in the "Random glossary entry" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Cancel" "button" in the "Delete block?" "dialogue"
    And I should see "Random glossary entry"
    When I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I click on "Hide" "icon" in the "Glossary" "table_row"
    And I am on "Course 1" course homepage with editing mode on
    And I open the "Random glossary entry" blocks action menu
    And I click on "Delete Random glossary entry block" "link" in the "Random glossary entry" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    Then I should not see "Random glossary entry"
