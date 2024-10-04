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
      | activity | name                             | course | idnumber  | defaultapproval |
      | glossary | Glossary without defaultapproval | C1     | glossary1 | 0               |
      | glossary | Glossary without entries         | C1     | glossary2 | 0               |
    And the following "mod_glossary > entries" exist:
      | glossary  | user     | concept  | definition                                       | approved |
      | glossary1 | teacher1 | Dragon   | Large, winged, fire-breathing reptilian monster. | 1        |
      | glossary1 | student1 | Griffin  | Lion body, eagle head and wings.                 | 1        |
      | glossary1 | student1 | Minotaur | Half-human, half-bull, lived in labyrinth.       | 0        |
      | glossary1 | student1 | Hydra    | Many-headed serpent; regrows heads when cut.     | 0        |
      | glossary1 | student2 | Centaur  | Half-human, half-horse creature from Greek myth. | 0        |

  Scenario: Teacher can see the glossary relevant information in the glossary overview
    When I am on the "Course 1" "course > activities > glossary" page logged in as "teacher1"
    And I should not see "My Entries" in the "glossary_overview_collapsible" "region"
    Then the following should exist in the "Table listing all Glossary activities" table:
      | Name                             | Entries | Actions     |
      | Glossary without defaultapproval | 2       | Approve (3) |
      | Glossary without entries         | -       | -           |
    And I click on "Approve (3)" "link" in the "glossary_overview_collapsible" "region"
    And I should see "Pending approval (3)" in the "page-header" "region"

  Scenario: Students can see the glossary relevant information in the glossary overview
    When I am on the "Course 1" "course > activities > glossary" page logged in as "student1"
    And I should not see "Actions" in the "glossary_overview_collapsible" "region"
    Then the following should exist in the "Table listing all Glossary activities" table:
      | Name                             | My entries | Total entries |
      | Glossary without defaultapproval | 3          | 2             |
      | Glossary without entries         | -          | -             |

  Scenario: The glossary index redirect to the activities overview
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I click on "Glossaries" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course, with dates and other information."
    And I should see "Name" in the "glossary_overview_collapsible" "region"
    And I should see "Entries" in the "glossary_overview_collapsible" "region"
    And I should see "Actions" in the "glossary_overview_collapsible" "region"

  Scenario: The glossary overview report should generate log events
    Given I am on the "Course 1" "course > activities > glossary" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'glossary'"
