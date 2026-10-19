@mod @mod_glossary
Feature: Show glossary entry
  In order to see the details of a glossary entry
  As a user
  I should be able to view the entry details on one page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name            | intro                 | displayformat  | course | idnumber |
      | glossary | Underused words | Say these more often. | fullwithauthor | C1     | g1       |
    And the following "mod_glossary > categories" exist:
      | glossary | name  |
      | g1       | nouns |
    And the following "mod_glossary > entries" exist:
      | glossary | concept | definition    | user     | categories |
      | g1       | clamor  | A loud noise. | teacher1 | nouns      |

  Scenario: View glossary entry details
    Given I am on the "Underused words" "glossary activity" page logged in as teacher1
    When I click on "Entry link: clamor" "link"
    Then the page title should contain "clamor | Underused words"
    And "clamor" "heading" should exist
    And I should see "A loud noise."

  Scenario: Entries from another glossary are not shown in entry view
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher2 | C2     | editingteacher |
    And the following "activities" exist:
      | activity | name            | course | idnumber |
      | glossary | Secret glossary | C2     | g2       |
    And the following "mod_glossary > entries" exist:
      | glossary | concept           | definition             | user     |
      | g2       | Confidential Term | Top secret definition. | teacher2 |
    When I am on the "Underused words > Confidential Term" "mod_glossary > Entry" page logged in as teacher1
    Then I should not see "Top secret definition."
    And I should see "No entries found in this section"
    And I am on the "Underused words > clamor" "mod_glossary > Entry" page
    And I should see "A loud noise."
    And I am on the "Secret glossary > Confidential Term" "mod_glossary > Entry" page logged in as teacher2
    And I should see "Top secret definition."

  Scenario: Entries exported to the main glossary are shown in entry view of their source glossary
    Given the following "activities" exist:
      | activity | name          | course | idnumber | mainglossary |
      | glossary | Main glossary | C1     | g3       | 1            |
    And the following "mod_glossary > entries" exist:
      | glossary | sourceglossary | concept   | definition                 | user     |
      | g3       | g1             | cacophony | A harsh mixture of sounds. | teacher1 |
    When I am on the "Underused words > cacophony" "mod_glossary > Entry" page logged in as teacher1
    Then I should see "A harsh mixture of sounds."
    And I should see "Exported entry"
