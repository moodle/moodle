@block @block_glossary_random @javascript @addablocklink
Feature: Add the glossary random block when main feature is enabled
  In order to add the glossary random block to my course
  As a teacher
  It should be added to courses only if the glossary module is enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And I am on the "C1" "course" page logged in as "admin"

  Scenario: The glossary random block can be added when glossary module is enabled
    Given I turn editing mode on
    When I click on "Add a block" "link"
    Then I should see "Random glossary entry"

  Scenario: The glossary random block cannot be added when glossary module is disabled
    Given I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I click on "Hide" "icon" in the "Glossary" "table_row"
    And I am on "Course 1" course homepage with editing mode on
    When I click on "Add a block" "link"
    Then I should not see "Random glossary entry"
