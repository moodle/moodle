@mod @mod_checklist @checklist
Feature: Teacher update checklist works as expected

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Checklist" to section "1" and I fill the form with:
      | Checklist    | Test checklist      |
      | Introduction | This is a checklist |
      | Updates by   | Teacher only        |
    And the following items exist in checklist "Test checklist":
      | text                      | required |
      | Checklist required item 1 | required |
      | Checklist required item 2 | required |
      | Checklist required item 3 | required |
      | Checklist optional item 4 | optional |
      | Checklist optional item 5 | optional |
    And I log out

  @javascript
  Scenario: A teacher updates a checklist from the report overview and the student can see it
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    When I press "Edit checks"
    # Row 3 = first row with a student in it, Item 3 = checklist item 1.
    And I set the field with xpath "//table[contains(@class,'checklistreport')]//tr[3]/td[3]/select" to "Yes"
    # Row 3 = first row with a student in it, Item 4 = checklist item 2.
    And I set the field with xpath "//table[contains(@class,'checklistreport')]//tr[3]/td[4]/select" to "No"
    # Row 3 = first row with a student in it, Item 7 = checklist item 5.
    And I set the field with xpath "//table[contains(@class,'checklistreport')]//tr[3]/td[7]/select" to "Yes"
    And I press "Save"
    And ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-unchecked.c2" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should exist in the "Student 1" "table_row"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then ".teachermarkyes" "css_element" should exist in the "Checklist required item 1" "list_item"
    And ".teachermarkno" "css_element" should exist in the "Checklist required item 2" "list_item"
    And ".teachermarkundecided" "css_element" should exist in the "Checklist required item 3" "list_item"
    And ".teachermarkundecided" "css_element" should exist in the "Checklist optional item 4" "list_item"
    And ".teachermarkyes" "css_element" should exist in the "Checklist optional item 5" "list_item"
    And I should see "33%" in the "#checklistprogressrequired" "css_element"
    And I should see "40%" in the "#checklistprogressall" "css_element"

  @javascript
  Scenario: A teacher clicks 'Toggle Row' and all items are updated
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    When I press "Edit checks"
    And I click on "Toggle Row" "button" in the "Student 1" "table_row"
    And I press "Save"
    And ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should exist in the "Student 1" "table_row"

  @javascript
  Scenario: A teacher can update a student's checkmarks individually.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    When I click on "View progress for this user" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Checklist required item 2 | Yes |
      | Checklist required item 3 | No  |
      | Checklist optional item 4 | Yes |
      | Checklist optional item 5 | Yes |
    # Lowercase 'save' to avoid clash with hidden 'Save' element.
    And I press "save"
    Then I should see "33%" in the "#checklistprogressrequired" "css_element"
    And I should see "60%" in the "#checklistprogressall" "css_element"
    And I press "View all students"
    And ".level0-checked.c1" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-unchecked.c3" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should exist in the "Student 1" "table_row"
