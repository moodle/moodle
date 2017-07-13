@mod @mod_feedback
Feature: Test creating different types of feedback questions for non-anonymous feedback
  In order to create feedbacks
  As a teacher
  I need to be able to add different question types

  @javascript
  Scenario: Create different types of questions in non-anonymous feedback with javascript enabled
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity   | name                | course | idnumber    | anonymous |
      | feedback   | Learning experience | C1     | feedback0   | 2         |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Information" question to the feedback with:
      | Question         | this is an information question |
      | Label            | info                            |
      | Information type | Course                          |
    And I add a "Information" question to the feedback with:
      | Question         | this is a response time question |
      | Label            | curtime                          |
      | Information type | Responsestime                    |
    And I add a "Label" question to the feedback with:
      | Contents | label text |
    And I add a "Longer text answer" question to the feedback with:
      | Question         | this is a longer text answer |
      | Label            | longertext                   |
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option a\noption b\noption c  |
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | this is a multiple choice 2        |
      | Label                          | multichoice2                       |
      | Multiple choice type           | Multiple choice - multiple answers |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | option d\noption e\noption f       |
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | this is a multiple choice 3        |
      | Label                          | multichoice3                       |
      | Multiple choice type           | Multiple choice - single answer allowed (dropdownlist) |
      | Multiple choice values         | option g\noption h\noption i                           |
    And I add a "Multiple choice (rated)" question to the feedback with:
      | Question               | this is a multiple choice rated |
      | Label                  | multichoice4                    |
      | Multiple choice type   | Multiple choice - single answer |
      | Multiple choice values | 0/option k\n1/option l\n5/option m |
    And I add a "Numeric answer" question to the feedback with:
      | Question               | this is a numeric answer |
      | Label                  | numeric                  |
      | Range from             | 0                        |
      | Range to               | 100                      |
    And I add a "Short text answer" question to the feedback with:
      | Question               | this is a short text answer |
      | Label                  | shorttext                   |
      | Maximum characters accepted | 200                    |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | this is a longer text answer | my long answer |
      | option b                     | 1              |
      | option d                     | 1              |
      | option f                     | 1              |
      | this is a multiple choice 3  | option h       |
      | option l                     | 1              |
      | this is a numeric answer (0 - 100) | 35       |
      | this is a short text answer  | hello          |
    And I press "Submit your answers"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | this is a longer text answer | lots of feedbacks |
      | option a                     | 1              |
      | option d                     | 1              |
      | option e                     | 1              |
      | this is a multiple choice 3  | option i       |
      | option m                     | 1              |
      | this is a numeric answer (0 - 100) | 71       |
      | this is a short text answer  | no way         |
    And I press "Submit your answers"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I navigate to "Analysis" in current page administration
    And I should see "Submitted answers: 2"
    And I should see "Questions: 9"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I navigate to "Analysis" in current page administration
    And I should see "C1" in the "(info)" "table"
    And I should see "my long answer" in the "(longertext)" "table"
    And I should see "lots of feedbacks" in the "(longertext)" "table"
    And I show chart data for the "multichoice2" feedback
    And I should see "2 (100.00 %)" in the "option d" "table_row"
    And I should see "1 (50.00 %)" in the "option e" "table_row"
    And I should see "1 (50.00 %)" in the "option f" "table_row"
    And I show chart data for the "multichoice3" feedback
    And I should see "0" in the "option g" "table_row"
    And I should not see "%" in the "option g" "table_row"
    And I should see "1 (50.00 %)" in the "option h" "table_row"
    And I should see "1 (50.00 %)" in the "option i" "table_row"
    And I show chart data for the "multichoice4" feedback
    And I should see "0" in the "(0) option k" "table_row"
    And I should not see "%" in the "(0) option k" "table_row"
    And I should see "1 (50.00 %)" in the "(1) option l" "table_row"
    And I should see "1 (50.00 %)" in the "(5) option m" "table_row"
    And I should see "Average: 3.00"
    And I should see "35" in the "(numeric)" "table"
    And I should see "71" in the "(numeric)" "table"
    And I should see "Average: 53.00" in the "(numeric)" "table"
    And I should see "no way" in the "(shorttext)" "table"
    And I should see "hello" in the "(shorttext)" "table"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (50.00 %)" in the "option a" "table_row"
    And I should see "1 (50.00 %)" in the "option b" "table_row"
    And I log out
