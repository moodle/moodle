@mod @mod_choicegroup
Feature: Use the choicegroup activity with groups within groupings
  In order to use choicegroup in a course with groupings
  As a teacher
  I need to be assured choicegroup behaves correctly

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Ann       | Student2 | student2@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "groups" exist:
      | name | course | idnumber |
      | A    | C1     | C1G1     |
      | B    | C1     | C1G2     |
      | C    | C1     | C1G3     |
      | D    | C1     | C1G4     |
    And the following "groupings" exist:
      | name | course | idnumber |
      | X    | C1     | GG1      |
      | Y    | C1     | GG2      |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | C1G1  |
      | GG1      | C1G2  |
      | GG2      | C1G1  |
    And the following "group members" exist:
      | user     | group |
      | student1 | C1G1  |
      | student1 | C1G2  |
    And the following "activities" exist:
      | activity    | name           | intro                      | course | idnumber     |
      | choicegroup | Group choice 1 | Group choice 1 for testing | C1     | choicegroup1 |

  @javascript
  Scenario: View a choicegroup activity with groups within groupings
    Given I am on the "Group choice 1" "choicegroup activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Group choice name | Choose your group        |
      | Description       | Group choice description |
    And I press "Expand All Groupings"
    And I should see "X"
    And I should see "Y"
    And I should see "A"
    And I should see "B"
    And I set the field "availablegroups" to "A"
    And I press "Add"
    And I press "Save and return to course"
    # Student view.
    When I am on the "Choose your group" "choicegroup activity" page logged in as student1
    Then I should see "A"