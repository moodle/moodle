@mod @mod_feedback
Feature: Mapping courses in a feedback
  In order to collect the same feedback about multiple courses
  As a manager
  I need to be able to map site feedback to courses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Username  | 1        |
      | user2    | Username  | 2        |
      | user3    | Username  | 3        |
      | teacher  | Teacher   | 4        |
      | manager  | Manager   | 5        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
      | Course 3 | C3        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | user1   | C1     | student |
      | user1   | C2     | student |
      | user2   | C1     | student |
      | user2   | C2     | student |
      | user3   | C3     | student |
      | teacher | C1     | editingteacher |
      | teacher | C2     | editingteacher |
      | teacher | C3     | editingteacher |
    And the following "system role assigns" exist:
      | user    | course               | role    |
      | manager | Acceptance test site | manager |
    And I log in as "admin"
    And I navigate to "Manage activities" node in "Site administration > Plugins > Activity modules"
    And I click on "Show" "link" in the "Feedback" "table_row"
    And I navigate to "Manage blocks" node in "Site administration > Plugins > Blocks"
    And I click on "Show" "link" in the "Feedback" "table_row"
    And I log out
    And the following "activities" exist:
      | activity   | name             | course               | idnumber  | anonymous | publish_stats |
      | feedback   | Course feedback  | Acceptance test site | feedback0 | 1         | 1             |
      | feedback   | Another feedback | C1                   | feedback1 | 1         | 1             |
    When I log in as "manager"
    And I am on site homepage
    And I follow "Course feedback"
    And I follow "Edit questions"
    And I add a "Information" question to the feedback with:
      | Question         | this is an information question |
      | Label            | info                            |
      | Information-Type | Course                          |
    And I add a "Multiple choice (rated)" question to the feedback with:
      | Question               | this is a multiple choice rated    |
      | Label                  | multichoicerated                   |
      | Multiple choice type   | Multiple choice - single answer    |
      | Multiple choice values | 0/option a\n1/option b\n5/option c |
    And I add a "Multiple choice" question to the feedback with:
      | Question               | this is a simple multiple choice    |
      | Label                  | multichoicesimple                   |
      | Multiple choice type   | Multiple choice - single answer allowed (dropdownlist) |
      | Multiple choice values | option d\noption e\noption f                           |
    And I log out
    And I log in as "teacher"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Feedback" block
    And I am on site homepage
    And I follow "Course 2"
    And I add the "Feedback" block
    And I am on site homepage
    And I follow "Course 3"
    And I add the "Feedback" block
    And I log out

  Scenario: Course feedback can not be mapped
    And I log in as "manager"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Another feedback"
    And I should not see "Mapped courses"
    And I should not see "Map feedback to courses"

  @javascript
  Scenario: Site feedback is not mapped to any course
    And I log in as "user1"
    And I am on site homepage
    And I follow "Course feedback"
    And I follow "Answer the questions..."
    And I should see "Acceptance test site" in the ".feedback_form" "css_element"
    And I set the following fields to these values:
      | option a                         | 1        |
      | this is a simple multiple choice | option d |
    And I press "Submit your answers"
    And I press "Continue"
    And I follow "Course 1"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I follow "Answer the questions..."
    And I should not see "Acceptance test site" in the ".feedback_form" "css_element"
    And I should see "C1" in the ".feedback_form" "css_element"
    And I set the following fields to these values:
      | option b                         | 1        |
      | this is a simple multiple choice | option e |
    And I press "Submit your answers"
    And I press "Continue"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I should not see "Answer the questions..."
    And I log out
    And I log in as "user2"
    And I follow "Course 1"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I follow "Answer the questions..."
    And I should not see "Acceptance test site" in the ".feedback_form" "css_element"
    And I should see "C1" in the ".feedback_form" "css_element"
    And I set the following fields to these values:
      | option c                         | 1        |
      | this is a simple multiple choice | option e |
    And I press "Submit your answers"
    And I press "Continue"
    And I log out
    And I log in as "manager"
    And I am on site homepage
    And I follow "Course feedback"

    And I follow "Analysis"
    And the field "Filter by course" matches value "Choose..."
    And I should see "1 (33.33 %)" in the "option a" "table_row"
    And I should see "1 (33.33 %)" in the "option b" "table_row"
    And I should see "1 (33.33 %)" in the "option c" "table_row"
    And I should see "Average: 2.00" in the "(multichoicerated)" "table"
    And I click on "Sort by course" "link" in the "(multichoicerated)" "table"
    And I should see "2.50" in the "C1" "table_row"
    And I should see "1.00" in the "Acceptance test site" "table_row"
    And I follow "Back"
    And I set the field "Filter by course" to "C1"
    And I should see "0" in the "option a" "table_row"
    And I should see "1 (50.00 %)" in the "option b" "table_row"
    And I should see "1 (50.00 %)" in the "option c" "table_row"
    And I log out

  @javascript
  Scenario: Site feedback is mapped to courses
    And I log in as "manager"
    And I am on site homepage
    And I follow "Course feedback"
    And I follow "Map feedback to courses"
    And I set the field "Courses" to "Course 2"
    And I set the field "Courses" to "Course 3"
    And I press "Save changes"
    And I log out

    And I log in as "user1"
    And I am on site homepage
    And I follow "Course feedback"
    And I should see "You can only access this feedback from a course"
    And I should not see "Answer the questions..."

    And I am on site homepage
    And I follow "Course 1"
    And "Feedback" "block" should not exist
    And I should not see "Course feedback"

    And I am on site homepage
    And I follow "Course 2"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I follow "Answer the questions..."
    And I should not see "Acceptance test site" in the ".feedback_form" "css_element"
    And I should see "C2" in the ".feedback_form" "css_element"
    And I set the following fields to these values:
      | option b                         | 1        |
      | this is a simple multiple choice | option e |
    And I press "Submit your answers"
    And I press "Continue"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I should not see "Answer the questions..."
    And I log out
    And I log in as "user2"
    And I follow "Course 2"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I follow "Answer the questions..."
    And I should not see "Acceptance test site" in the ".feedback_form" "css_element"
    And I should see "C2" in the ".feedback_form" "css_element"
    And I set the following fields to these values:
      | option c                         | 1        |
      | this is a simple multiple choice | option e |
    And I press "Submit your answers"
    And I press "Continue"
    And I log out
    And I log in as "user3"
    And I follow "Course 3"
    And I click on "Course feedback" "link" in the "Feedback" "block"
    And I follow "Answer the questions..."
    And I should not see "Acceptance test site" in the ".feedback_form" "css_element"
    And I should see "C3" in the ".feedback_form" "css_element"
    And I set the following fields to these values:
      | option c                         | 1        |
      | this is a simple multiple choice | option d |
    And I press "Submit your answers"
    And I press "Continue"
    And I log out
    And I log in as "manager"
    And I am on site homepage
    And I follow "Course feedback"
    And I follow "Analysis"
    And the field "Filter by course" matches value "Choose..."
    And I should see "0" in the "option a" "table_row"
    And I should see "1 (33.33 %)" in the "option b" "table_row"
    And I should see "2 (66.67 %)" in the "option c" "table_row"
    And I should see "Average: 3.67" in the "(multichoicerated)" "table"
    And I click on "Sort by course" "link" in the "(multichoicerated)" "table"
    And I should see "3.00" in the "C3" "table_row"
    And I should see "2.50" in the "C2" "table_row"
    And I follow "Back"
    And I set the field "Filter by course" to "C2"
    And I should see "0" in the "option a" "table_row"
    And I should see "1 (50.00 %)" in the "option b" "table_row"
    And I should see "1 (50.00 %)" in the "option c" "table_row"
    And I should see "2 (100.00 %)" in the "option e" "table_row"
    And I set the field "Filter by course" to "C3"
    And I should see "0" in the "option a" "table_row"
    And I should see "0" in the "option b" "table_row"
    And I should see "1 (100.00 %)" in the "option c" "table_row"
    And I should see "1 (100.00 %)" in the "option d" "table_row"
    And I follow "Show all"
    And I should see "1 (33.33 %)" in the "option d" "table_row"
    And I should see "2 (66.67 %)" in the "option e" "table_row"
    And I should see "0" in the "option f" "table_row"
    And I log out
