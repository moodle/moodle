@core @core_grades @javascript
Feature: Grade letters can be overridden
  In order to test the grade letters functionality
  As a teacher I override site defaults
  and alter the grade letters/edit/letter/index.php

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
    And I am on "Course 1" course homepage
    And I navigate to "More > Grade letters" in the course gradebook
    And I click on "Edit" "button"

  Scenario Outline: Grade letters can be completely overridden
    When I set the following fields to these values:
      | Override site defaults | 1    |
      | Grade letter 1         | <l1> |
      | Letter grade boundary 1| <b1> |
      | Grade letter 2         | <l2> |
      | Letter grade boundary 2| <b2> |
      | Grade letter 3         | <l3> |
      | Letter grade boundary 3| <b3> |
      | Grade letter 4         | <l4> |
      | Letter grade boundary 4| <b4> |
      | Grade letter 5         | <l5> |
      | Letter grade boundary 5| <b5> |
      | Grade letter 6         | <l6> |
      | Letter grade boundary 6| <b6> |
      | Grade letter 7         | <l7> |
      | Letter grade boundary 7| <b7> |
      | Grade letter 8         | <l8> |
      | Letter grade boundary 8| <b8> |
      | Grade letter 9         | <l9> |
      | Letter grade boundary 9| <b9> |
      | Grade letter 10        |      |
      | Letter grade boundary 10|     |
      | Grade letter 11        |      |
      | Letter grade boundary 11|     |
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

  Scenario Outline: Define grade letters with custom decimal separator
    Given the following "language customisations" exist:
      | component       | stringid | value        |
      | core_langconfig | decsep   | <decsep>     |
    When I set the following fields to these values:
      | Override site defaults | 1    |
      | Grade letter 1         | <l1> |
      | Letter grade boundary 1| <b1> |
      | Grade letter 2         | <l2> |
      | Letter grade boundary 2| <b2> |
      | Grade letter 3         | <l3> |
      | Letter grade boundary 3| <b3> |
      | Grade letter 4         |      |
      | Letter grade boundary 4|      |
      | Grade letter 5         |      |
      | Letter grade boundary 5|      |
      | Grade letter 6         |      |
      | Letter grade boundary 6|      |
      | Grade letter 7         |      |
      | Letter grade boundary 7|      |
      | Grade letter 8         |      |
      | Letter grade boundary 8|      |
      | Grade letter 9         |      |
      | Letter grade boundary 9|      |
      | Grade letter 10        |      |
      | Letter grade boundary 10|     |
      | Grade letter 11        |      |
      | Letter grade boundary 11|     |
    And I press "Save changes"
    Then I should see "The default grade letters are currently overridden."
    And the following should exist in the "grade-letters-view" table:
      | Highest | Lowest | Letter |
      | <high1> | <low1> | <l1>   |
      | <high2> | <low2> | <l2>   |
      | <high3> | <low3> | <l3>   |

    Examples:
      | decsep | l1 | b1    | l2 | b2    | l3 | b3   | high1    | low1    | high2   | low2    | high3   | low3   |
      | .      | A  | 88.88 | B  | 50.00 | C  | 0.00 | 100.00 % | 88.88 % | 88.87 % | 50.00 % | 49.99 % | 0.00 % |
      | #      | A  | 88#88 | B  | 50#00 | C  | 0#00 | 100#00 % | 88#88 % | 88#87 % | 50#00 % | 49#99 % | 0#00 % |

  Scenario: Define additional grade letters
    Given I set the field "Override site defaults" to "1"
    When I press "Add 3 field(s) to form"
    And I set the following fields to these values:
      | Grade letter 11          | E  |
      | Letter grade boundary 11 | 50 |
      | Grade letter 12          | F  |
      | Letter grade boundary 12 | 40 |
      | Grade letter 13          | G  |
      | Letter grade boundary 13 | 30 |
      | Grade letter 14          | U  |
      | Letter grade boundary 14 | 0  |
    And I press "Save changes"
    Then I should see "The default grade letters are currently overridden."
    And the following should exist in the "grade-letters-view" table:
      | Highest  | Lowest   | Letter |
      | 100.00 % | 93.00 %  | A      |
      | 92.99 %  | 90.00 %  | A-     |
      | 89.99 %  | 87.00 %  | B+     |
      | 86.99 %  | 83.00 %  | B      |
      | 82.99 %  | 80.00 %  | B-     |
      | 79.99 %  | 77.00 %  | C+     |
      | 76.99 %  | 73.00 %  | C      |
      | 72.99 %  | 70.00 %  | C-     |
      | 69.99 %  | 67.00 %  | D+     |
      | 66.99 %  | 60.00 %  | D      |
      | 59.99 %  | 50.00 %  | E      |
      | 49.99 %  | 40.00 %  | F      |
      | 39.99 %  | 30.00 %  | G      |
      | 29.99 %  | 0.00 %   | U      |

  Scenario: I delete a grade letter
    Given I set the following fields to these values:
      | Override site defaults | 1  |
      | Grade letter 1         | A  |
      | Letter grade boundary 1| 90 |
      | Grade letter 2         | B  |
      | Letter grade boundary 2| 80 |
      | Grade letter 3         | C  |
      | Letter grade boundary 3| 50 |
      | Grade letter 4         | D  |
      | Letter grade boundary 4| 40 |
      | Grade letter 5         | E  |
      | Letter grade boundary 5| 20 |
      | Grade letter 6         | F  |
      | Letter grade boundary 6| 0  |
      | Grade letter 7         |    |
      | Letter grade boundary 7|    |
      | Grade letter 8         |    |
      | Letter grade boundary 8|    |
      | Grade letter 9         |    |
      | Letter grade boundary 9|    |
      | Grade letter 10        |    |
      | Letter grade boundary 10|   |
      | Grade letter 11        |    |
      | Letter grade boundary 11|   |
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
    When I click on "Edit" "button"
    And I set the following fields to these values:
      | Override site defaults | 1  |
      | Grade letter 1         | A  |
      | Letter grade boundary 1| 90 |
      | Grade letter 2         | B  |
      | Letter grade boundary 2| 80 |
      | Grade letter 3         | C  |
      | Letter grade boundary 3| 50 |
      | Grade letter 4         | D  |
      | Letter grade boundary 4| 40 |
      | Grade letter 5         |    |
      | Letter grade boundary 5|    |
      | Grade letter 6         | F  |
      | Letter grade boundary 6| 0  |
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
      | Override site defaults | 1  |
      | Grade letter 1         | A+ |
      | Letter grade boundary 1| 90 |
      | Grade letter 2         | A  |
      | Letter grade boundary 2| 80 |
      | Grade letter 3         | B+ |
      | Letter grade boundary 3| 70 |
      | Grade letter 4         | B  |
      | Letter grade boundary 4| 60 |
      | Grade letter 5         | C  |
      | Letter grade boundary 5| 50 |
      | Grade letter 6         | D  |
      | Letter grade boundary 6| 40 |
      | Grade letter 7         | F  |
      | Letter grade boundary 7| 0  |
      | Grade letter 8         |    |
      | Letter grade boundary 8|    |
      | Grade letter 9         |    |
      | Letter grade boundary 9|    |
      | Grade letter 10        |    |
      | Letter grade boundary 10|   |
      | Grade letter 11        |    |
      | Letter grade boundary 11|   |
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
    When I click on "Edit" "button"
    And I set the following fields to these values:
      | Override site defaults | 1  |
      | Grade letter 1         | α  |
      | Letter grade boundary 1| 95 |
      | Grade letter 2         | β  |
      | Letter grade boundary 2| 85 |
      | Grade letter 3         | γ  |
      | Letter grade boundary 3| 70 |
      | Grade letter 4         | δ  |
      | Letter grade boundary 4| 55 |
      | Grade letter 5         |    |
      | Letter grade boundary 5|    |
      | Grade letter 6         | Ω  |
      | Letter grade boundary 6| 0  |
      | Grade letter 7         | π  |
      | Letter grade boundary 7| 90 |
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
