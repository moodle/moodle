@mod @mod_glossary
Feature: Testing overview integration in mod_glossary
  In order to list all glossaries in a course
  As a user
  I need to be able to see the glossary overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Username  | 1        |
      | student2 | Username  | 2        |
      | teacher1 | Teacher   | T        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name                             | course | idnumber  | defaultapproval | allowcomments |
      | glossary | Glossary without defaultapproval | C1     | glossary1 | 0               | 1             |
      | glossary | Glossary without entries         | C1     | glossary2 | 0               | 0             |
      | glossary | Glossary with comments           | C1     | glossary3 | 1               | 1             |
    And the following "mod_glossary > entries" exist:
      | glossary  | user     | concept  | definition                                       | approved |
      | glossary1 | teacher1 | Dragon   | Large, winged, fire-breathing reptilian monster. | 1        |
      | glossary1 | student1 | Griffin  | Lion body, eagle head and wings.                 | 1        |
      | glossary1 | student1 | Minotaur | Half-human, half-bull, lived in labyrinth.       | 0        |
      | glossary1 | student1 | Hydra    | Many-headed serpent; regrows heads when cut.     | 0        |
      | glossary1 | student2 | Centaur  | Half-human, half-horse creature from Greek myth. | 0        |
      | glossary3 | student1 | Phoenix  | Mythical bird, regenerates from ashes.           | 1        |

  @javascript
  Scenario: Teacher can see the glossary relevant information in the glossary overview
    # Add a few comments.
    Given I am on the "Glossary with comments" "glossary activity" page logged in as student2
    And I click on "Comments (0)" "link"
    And I set the following fields to these values:
      | Comment        | My first comment |
    And I click on "Save comment" "link"
    And I set the following fields to these values:
      | Comment        | My second comment |
    And I click on "Save comment" "link"
    When I am on the "Course 1" "course > activities > glossary" page logged in as "teacher1"
    And I should not see "My Entries" in the "glossary_overview_collapsible" "region"
    Then the following should exist in the "Table listing all Glossary activities" table:
      | Name                             | Comments | Entries | Actions     |
      | Glossary without defaultapproval | 0        | 2       | Approve (3) |
      | Glossary without entries         | -        | 0       | View        |
      | Glossary with comments           | 2        | 1       | View        |
    And I click on "Approve (3)" "link" in the "glossary_overview_collapsible" "region"
    And I should see "Pending approval (3)" in the "page-header" "region"

  @javascript
  Scenario: Students can see the glossary relevant information in the glossary overview
    # Add a few comments.
    Given I am on the "Glossary with comments" "glossary activity" page logged in as student2
    And I click on "Comments (0)" "link"
    And I set the following fields to these values:
      | Comment        | My first comment |
    And I click on "Save comment" "link"
    And I set the following fields to these values:
      | Comment        | My second comment |
    And I click on "Save comment" "link"
    When I am on the "Course 1" "course > activities > glossary" page logged in as "student1"
    And I should not see "Actions" in the "glossary_overview_collapsible" "region"
    Then the following should exist in the "Table listing all Glossary activities" table:
      | Name                             | Total entries | My entries | Comments |
      | Glossary without defaultapproval | 2             | 3          | 0        |
      | Glossary without entries         | 0             | 0          | -        |
      | Glossary with comments           | 1             | 1          | 2        |

  Scenario: The glossary overview report should generate log events
    Given I am on the "Course 1" "course > activities > glossary" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'glossary'"

  Scenario: The glossary index redirect to the activities overview
    Given the following "activity" exists:
      | activity    | glossary             |
      | course      | Acceptance test site |
      | name        | Home glossary        |
    And I log in as "admin"
    When I visit "/mod/glossary/index.php?id=1"
    Then the following should exist in the "Table listing all Glossary activities" table:
      | Name          | Entries | Actions |
      | Home glossary | 0       | View    |
