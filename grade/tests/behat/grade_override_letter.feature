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

  Scenario Outline: Grade letters can be completely overridden
    When I set the following fields to these values:
      | override               | 1    |
      | Grade letter 1         | <l1> |
      | gradeboundary1         | <b1> |
      | Grade letter 2         | <l2> |
      | gradeboundary2         | <b2> |
      | Grade letter 3         | <l3> |
      | gradeboundary3         | <b3> |
      | Grade letter 4         | <l4> |
      | gradeboundary4         | <b4> |
      | Grade letter 5         | <l5> |
      | gradeboundary5         | <b5> |
      | Grade letter 6         | <l6> |
      | gradeboundary6         | <b6> |
      | Grade letter 7         | <l7> |
      | gradeboundary7         | <b7> |
      | Grade letter 8         | <l8> |
      | gradeboundary8         | <b8> |
      | Grade letter 9         | <l9> |
      | gradeboundary9         | <b9> |
      | Grade letter 10        |      |
      | gradeboundary10        |      |
      | Grade letter 11        |      |
      | gradeboundary11        |      |
      | Grade letter 12        |      |
      | gradeboundary12        |      |
      | Grade letter 13        |      |
      | gradeboundary13        |      |
      | Grade letter 14        |      |
      | gradeboundary14        |      |
    And I press "Save changes"
    Then I should see "The default grade letters are currently overridden."
    And the following should exist in the "grade-letters-view" table:
      | Highest | Lowest | Letter    |
      | <high1> | <low1> | <letter1> |
      | <high2> | <low2> | <letter2> |
      | <high3> | <low3> | <letter3> |
      | <high4> | <low4> | <letter4> |
      | <high5> | <low5> | <letter5> |
      | <high6> | <low6> | <letter6> |

    Examples:
    | l1 | b1    | l2 | b2    | l3 | b3    | l4 | b4    | l5 | b5    | l6 | b6    | l7 | b7 | l8 | b8   | l9 | b9 | high1    | low1     | letter1 | high2   | low2    | letter2 | high3    | low3    | letter3 | high4    | low4    | letter4 | high5    | low5    | letter5 | high6    | low6    | letter6 |
    | Z  | 95    | Y  | 85    | X  | 75    | W  | 65    | V  | 55    | U  | 45    |    |    |    |      |    |    | 100.00 % | 95.00 %  | Z       | 94.99 % | 85.00 % | Y       | 84.99 %  | 75.00 % | X       | 74.99 %  | 65.00 % | W       | 64.99 %  | 55.00 % | V       | 54.99 %  | 45.00 % | U       |
    | 5  | 100   | 4  | 80    | 3  | 60    | 2  | 40    | 1  | 20    | 0  | 0     |    |    |    |      |    |    | 100.00 % | 100.00 % | 5       | 99.99 % | 80.00 % | 4       | 79.99 %  | 60.00 % | 3       | 59.99 %  | 40.00 % | 2       | 39.99 %  | 20.00 % | 1       | 19.99 %  | 0.00 %  | 0       |
    | A  | 95.25 | B  | 76.75 | C  | 50.01 | D  | 40    | F  | 0.01  | F- | 0     |    |    |    |      |    |    | 100.00 % | 95.25 %  | A       | 95.24 % | 76.75 % | B       | 76.74 %  | 50.01 % | C       | 50.00 %  | 40.00 % | D       | 39.99 %  | 0.01 %  | F       | 0.00 %   | 0.00 %  | F-      |
    |    |       |    |       |    |       | A  | 95.25 | B  | 76.75 | C  | 50.01 | D  | 40 | F  | 0.01 | F- | 0  | 100.00 % | 95.25 %  | A       | 95.24 % | 76.75 % | B       | 76.74 %  | 50.01 % | C       | 50.00 %  | 40.00 % | D       | 39.99 %  | 0.01 %  | F       | 0.00 %   | 0.00 %  | F-      |
    |    |       | A  | 95.25 | B  | 76.75 | C  | 50.01 |    |       |    |       | D  | 40 | F  | 0.01 | F- | 0  | 100.00 % | 95.25 %  | A       | 95.24 % | 76.75 % | B       | 76.74 %  | 50.01 % | C       | 50.00 %  | 40.00 % | D       | 39.99 %  | 0.01 %  | F       | 0.00 %   | 0.00 %  | F-      |

  Scenario: I delete a grade letter
    Given I set the following fields to these values:
      | override               | 1  |
      | Grade letter 1         | A  |
      | gradeboundary1         | 90 |
      | Grade letter 2         | B  |
      | gradeboundary2         | 80 |
      | Grade letter 3         | C  |
      | gradeboundary3         | 50 |
      | Grade letter 4         | D  |
      | gradeboundary4         | 40 |
      | Grade letter 5         | E  |
      | gradeboundary5         | 20 |
      | Grade letter 6         | F  |
      | gradeboundary6         | 0  |
      | Grade letter 7         |    |
      | gradeboundary7         |    |
      | Grade letter 8         |    |
      | gradeboundary8         |    |
      | Grade letter 9         |    |
      | gradeboundary9         |    |
      | Grade letter 10        |    |
      | gradeboundary10        |    |
      | Grade letter 11        |    |
      | gradeboundary11        |    |
      | Grade letter 12        |    |
      | gradeboundary12        |    |
      | Grade letter 13        |    |
      | gradeboundary13        |    |
      | Grade letter 14        |    |
      | gradeboundary14        |    |
    And I press "Save changes"
    And I should see "The default grade letters are currently overridden."
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
      | override               | 1  |
      | Grade letter 1         | A  |
      | gradeboundary1         | 90 |
      | Grade letter 2         | B  |
      | gradeboundary2         | 80 |
      | Grade letter 3         | C  |
      | gradeboundary3         | 50 |
      | Grade letter 4         | D  |
      | gradeboundary4         | 40 |
      | Grade letter 5         |    |
      | gradeboundary5         |    |
      | Grade letter 6         | F  |
      | gradeboundary6         | 0  |
    And I press "Save changes"
    Then I should see "The default grade letters are currently overridden."
    And the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 90.00 %  | A      |
      | 89.99 %  | 80.00 %  | B      |
      | 79.99 %  | 50.00 %  | C      |
      | 49.99 %  | 40.00 %  | D      |
      | 39.99 %  | 0.00 %   | F      |

  Scenario: I override grade letters for a second time
    Given I set the following fields to these values:
      | override               | 1  |
      | Grade letter 1         | A+ |
      | gradeboundary1         | 90 |
      | Grade letter 2         | A  |
      | gradeboundary2         | 80 |
      | Grade letter 3         | B+ |
      | gradeboundary3         | 70 |
      | Grade letter 4         | B  |
      | gradeboundary4         | 60 |
      | Grade letter 5         | C  |
      | gradeboundary5         | 50 |
      | Grade letter 6         | D  |
      | gradeboundary6         | 40 |
      | Grade letter 7         | F  |
      | gradeboundary7         | 0  |
      | Grade letter 8         |    |
      | gradeboundary8         |    |
      | Grade letter 9         |    |
      | gradeboundary9         |    |
      | Grade letter 10        |    |
      | gradeboundary10        |    |
      | Grade letter 11        |    |
      | gradeboundary11        |    |
      | Grade letter 12        |    |
      | gradeboundary12        |    |
      | Grade letter 13        |    |
      | gradeboundary13        |    |
      | Grade letter 14        |    |
      | gradeboundary14        |    |
    And I press "Save changes"
    And I should see "The default grade letters are currently overridden."
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
      | override               | 1  |
      | Grade letter 1         | α  |
      | gradeboundary1         | 95 |
      | Grade letter 2         | β  |
      | gradeboundary2         | 85 |
      | Grade letter 3         | γ  |
      | gradeboundary3         | 70 |
      | Grade letter 4         | δ  |
      | gradeboundary4         | 55 |
      | Grade letter 5         |    |
      | gradeboundary5         |    |
      | Grade letter 6         | Ω  |
      | gradeboundary6         | 0  |
      | Grade letter 7         | π  |
      | gradeboundary7         | 90 |
    And I press "Save changes"
    Then I should see "The default grade letters are currently overridden."
    And the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 95.00 %  | α      |
      | 94.99 %  | 90.00 %  | π      |
      | 89.99 %  | 85.00 %  | β      |
      | 84.99 %  | 70.00 %  | γ      |
      | 69.99 %  | 55.00 %  | δ      |
      | 54.99 %  | 0.00 %   | Ω      |