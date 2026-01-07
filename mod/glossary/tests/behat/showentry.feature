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
