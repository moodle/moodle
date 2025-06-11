@block @block_glossary_random @javascript
Feature: Add the glossary random block when main feature is disabled
    In order to add the glossary random block to my course
    As a teacher
    It should be added to courses only if the glossary module is enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | glossary_random | Course       | C1        | course-view-*   | site-post     |

  Scenario: The glossary random block is displayed even when glossary module is disabled
    When I log in as "admin"
    And I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I toggle the "Disable Glossary" admin switch "off"
    And I am on "Course 1" course homepage with editing mode on
    Then "Random glossary entry" "block" should exist

  Scenario: The glossary random block can be removed even when glossary module is disabled
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I open the "Random glossary entry" blocks action menu
    And I click on "Delete Random glossary entry block" "link" in the "Random glossary entry" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Cancel" "button" in the "Delete block?" "dialogue"
    And "Random glossary entry" "block" should exist
    When I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I toggle the "Disable Glossary" admin switch "off"
    And I am on "Course 1" course homepage with editing mode on
    And I open the "Random glossary entry" blocks action menu
    And I click on "Delete Random glossary entry block" "link" in the "Random glossary entry" "block"
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    Then "Random glossary entry" "block" should not exist
