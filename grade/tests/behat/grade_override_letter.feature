@core @core_grades
Feature: Grade letters can be overridden
  In order to test the grade letters functionality
  As a teacher I override site defaults
  and alter the grade letters

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grades"
    And I follow "Letters"
    And I follow "Edit grade letters"

  Scenario: Grade letters can be completely overridden
    When I set the following fields to these values:
      | override               | 1      |
      | Grade letter 1         | Z      |
      | gradeboundary1         | 95     |
      | Grade letter 2         | Y      |
      | gradeboundary2         | 85     |
      | Grade letter 3         | X      |
      | gradeboundary3         | 75     |
      | Grade letter 4         | W      |
      | gradeboundary4         | 65     |
      | Grade letter 5         | V      |
      | gradeboundary5         | 55     |
      | Grade letter 6         | U      |
      | gradeboundary6         | 45     |
      | gradeboundary7         | Unused |
      | gradeboundary8         | Unused |
      | gradeboundary9         | Unused |
      | gradeboundary10        | Unused |
      | gradeboundary11        | Unused |
      | gradeboundary12        | Unused |
      | gradeboundary13        | Unused |
      | gradeboundary14        | Unused |
    And I press "Save changes"
    Then the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest  | Letter |
      | 100.00 % | 95.00 % | Z      |
      | 94.99 %  | 85.00 % | Y      |
      | 84.99 %  | 75.00 % | X      |
      | 74.99 %  | 65.00 % | W      |
      | 64.99 %  | 55.00 % | V      |
      | 54.99 %  | 45.00 % | U      |

  @javascript
  Scenario: I delete a grade letter
    Given I set the following fields to these values:
      | override               | 1      |
      | Grade letter 1         | A      |
      | gradeboundary1         | 90     |
      | Grade letter 2         | B      |
      | gradeboundary2         | 80     |
      | Grade letter 3         | C      |
      | gradeboundary3         | 50     |
      | Grade letter 4         | D      |
      | gradeboundary4         | 40     |
      | Grade letter 5         | E      |
      | gradeboundary5         | 20     |
      | Grade letter 6         | F      |
      | gradeboundary6         | 0      |
      | gradeboundary7         | Unused |
      | gradeboundary8         | Unused |
      | gradeboundary9         | Unused |
      | gradeboundary10        | Unused |
      | gradeboundary11        | Unused |
      | gradeboundary12        | Unused |
      | gradeboundary13        | Unused |
      | gradeboundary14        | Unused |
    And I press "Save changes"
    And the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 90.00 %  | A      |
      | 89.99 %  | 80.00 %  | B      |
      | 79.99 %  | 50.00 %  | C      |
      | 49.99 %  | 40.00 %  | D      |
      | 39.99 %  | 20.00 %  | E      |
      | 19.99 %  | 0.00 %   | F      |
    When I follow "Edit grade letters"
    And I set the following fields to these values:
      | override               | 1      |
      | Grade letter 1         | A      |
      | gradeboundary1         | 90     |
      | Grade letter 2         | B      |
      | gradeboundary2         | 80     |
      | Grade letter 3         | C      |
      | gradeboundary3         | 50     |
      | Grade letter 4         | D      |
      | gradeboundary4         | 40     |
      | Grade letter 5         |        |
      | gradeboundary5         | Unused |
      | Grade letter 6         | F      |
      | gradeboundary6         | 0      |
      | gradeboundary7         | Unused |
      | gradeboundary8         | Unused |
      | gradeboundary9         | Unused |
    And I press "Save changes"
    Then the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 90.00 %  | A      |
      | 89.99 %  | 80.00 %  | B      |
      | 79.99 %  | 50.00 %  | C      |
      | 49.99 %  | 40.00 %  | D      |
      | 39.99 %  | 0.00 %   | F      |

  @javascript
  Scenario: I override grade letters for a second time
    Given I set the following fields to these values:
      | override               | 1      |
      | Grade letter 1         | A+     |
      | gradeboundary1         | 90     |
      | Grade letter 2         | A      |
      | gradeboundary2         | 80     |
      | Grade letter 3         | B+     |
      | gradeboundary3         | 70     |
      | Grade letter 4         | B      |
      | gradeboundary4         | 60     |
      | Grade letter 5         | C      |
      | gradeboundary5         | 50     |
      | Grade letter 6         | D      |
      | gradeboundary6         | 40     |
      | Grade letter 7         | F      |
      | gradeboundary7         | 0      |
      | gradeboundary8         | Unused |
      | gradeboundary9         | Unused |
      | gradeboundary10        | Unused |
      | gradeboundary11        | Unused |
      | gradeboundary12        | Unused |
      | gradeboundary13        | Unused |
      | gradeboundary14        | Unused |
    And I press "Save changes"
    And the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 90.00 %  | A+     |
      | 89.99 %  | 80.00 %  | A      |
      | 79.99 %  | 70.00 %  | B+     |
      | 69.99 %  | 60.00 %  | B      |
      | 59.99 %  | 50.00 %  | C      |
      | 49.99 %  | 40.00 %  | D      |
      | 39.99 %  | 0.00 %   | F      |
    When I follow "Edit grade letters"
    And I set the following fields to these values:
      | override               | 1      |
      | Grade letter 1         | α      |
      | gradeboundary1         | 95     |
      | Grade letter 2         | β      |
      | gradeboundary2         | 85     |
      | Grade letter 3         | γ      |
      | gradeboundary3         | 70     |
      | Grade letter 4         | δ      |
      | gradeboundary4         | 55     |
      | Grade letter 5         |        |
      | gradeboundary5         | Unused |
      | Grade letter 6         | Ω      |
      | gradeboundary6         | 0      |
      | Grade letter 7         | π      |
      | gradeboundary7         | 90     |
      | gradeboundary8         | Unused |
      | gradeboundary9         | Unused |
      | gradeboundary10        | Unused |
    And I press "Save changes"
    Then the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 95.00 %  | α      |
      | 94.99 %  | 90.00 %  | π      |
      | 89.99 %  | 85.00 %  | β      |
      | 84.99 %  | 70.00 %  | γ      |
      | 69.99 %  | 55.00 %  | δ      |
      | 54.99 %  | 0.00 %   | Ω      |
