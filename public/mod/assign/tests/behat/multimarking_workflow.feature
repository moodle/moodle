@mod @mod_assign @javascript
Feature: Complete multi-marking workflow
  In order to conduct an assignment with multi-marking
  As a teacher
  I need to:
    - Allocate markers to the student(s)
    - Allocate marks to the student(s) submission(s)
    - Calculate a final grade based on the configured multi-marking agreement method
    - Release the final grade to the student(s)

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname | email                |
      | student1 | Student    | One      | student1@example.com |
      | student2 | Student    | Two      | student2@example.com |
      | student3 | Student    | Three    | student3@example.com |
      | student4 | Student    | Four     | student4@example.com |
      | teacher1 | Teacher    | One      | teacher1@example.com |
      | teacher2 | Teacher    | Two      | teacher2@example.com |
      | teacher3 | Teacher    | Three    | teacher3@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | teacher3 | C1     | editingteacher |
    And the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
      | Group 2 | C1     | G2       | 1             |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
      | student3 | G2    |
      | student4 | G2    |
    And the following "activity" exists:
      | activity                 | assign        |
      | course                   | C1            |
      | idnumber                 | A1            |
      | name                     | Assignment 1  |
      | section                  | 1             |
      | completion               | 1             |
      | markingworkflow          | 1             |
      | markingallocation        | 1             |
      | markercount              | 2             |
      | multimarkmethod          | average       |
      | multimarkrounding        | 1             |
      | grade[modgrade_type]     | point         |
      | grade[modgrade_point]    | 100           |
    And the following "activity" exists:
      | activity                 | assign        |
      | course                   | C1            |
      | idnumber                 | A2            |
      | name                     | Assignment 2  |
      | section                  | 1             |
      | completion               | 1             |
      | markingworkflow          | 1             |
      | markingallocation        | 1             |
      | markercount              | 2             |
      | multimarkmethod          | maximum       |
      | multimarkrounding        | 1             |
      | teamsubmission           | 1             |
      | grade[modgrade_type]     | point         |
      | grade[modgrade_point]    | 100           |

  Scenario: Complete workflow of multi-marking
    # Firstly, allocate the markers to the students.
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    When I click on "Allocate marker" "button" in the "sticky-footer" "region"
    And I click on "Allocate marker" "button" in the ".modal-footer" "css_element"
    And I select "Teacher One" from the "Allocated marker 1" singleselect
    And I select "Teacher Two" from the "Allocated marker 2" singleselect
    And I press "Save changes"
    Then the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2    | Status     |
      | Student One   | Teacher One | Teacher Two | Not marked |
      | Student Two   | Teacher One | Teacher Two | Not marked |
      | Student Three |             |             | Not marked |
      | Student Four  |             |             | Not marked |
    # Then allocate marks to the student submissions as teacher1.
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "99"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I go to "Student Two" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "11"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2    | Status     |
      | Student One   | 99          |             | In marking |
      | Student Two   | 11          |             | In marking |
      | Student Three |             |             | Not marked |
      | Student Four  |             |             | Not marked |
    # Then allocate marks to the student submissions as teacher2.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "88"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I go to "Student Two" "Assignment 1" activity advanced marking page
    And I set the field "Mark out of 100" to "22"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2    | Status            | Grade |
      | Student One   | 99          | 88          | Marking completed | 94    |
      | Student Two   | 11          | 22          | Marking completed | 17    |
      | Student Three |             |             | Not marked        |       |
      | Student Four  |             |             | Not marked        |       |
    # Then we check the calculated final grade and release them to the students.
    And I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the "Set marking workflow state" "dialogue"
    And I set the field "Workflow context" to "Grade"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2    | Status     | Final grade |
      | Student One   | 99          | 88          | Released   | 94          |
      | Student Two   | 11          | 22          | Released   | 17          |
      | Student Three |             |             | Not marked |             |
      | Student Four  |             |             | Not marked |             |
    # Marks can be released without all markers having marked with the capability to manage restricted grades.
    When the following "role capability" exists:
      | role                              | editingteacher |
      | mod/assign:managerestrictedgrades | allow          |
    And I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the "Set marking workflow state" "dialogue"
    And I set the field "Workflow context" to "Grade"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2    | Status     | Final grade |
      | Student One   | 99          | 88          | Released   | 94          |
      | Student Two   | 11          | 22          | Released   | 17          |
      | Student Three |             |             | Released   |             |
      | Student Four  |             |             | Released   |             |

  Scenario: Complete workflow of multi-marking with group submissions
    # Firstly, allocate the markers to the students (to test thoroughly, one group will have the same marker
    # for both students. The other group won't).
    Given I am on the "A2" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I navigate to "Submissions" in current page administration
    And I click on "Quick grading" "checkbox"
    When I set the field "Allocated marker 1" in the "Student One" "table_row" to "Teacher One"
    And I set the field "Allocated marker 1" in the "Student Two" "table_row" to "Teacher One"
    And I set the field "Allocated marker 1" in the "Student Three" "table_row" to "Teacher One"
    And I set the field "Allocated marker 1" in the "Student Four" "table_row" to "Teacher One"
    And I set the field "Allocated marker 2" in the "Student One" "table_row" to "Teacher Two"
    And I set the field "Allocated marker 2" in the "Student Two" "table_row" to "Teacher Two"
    And I set the field "Allocated marker 2" in the "Student Three" "table_row" to "Teacher Two"
    And I set the field "Allocated marker 2" in the "Student Four" "table_row" to "Teacher Three"
    And I click on "Save" "button" in the "sticky-footer" "region"
    And I press "Continue"
    And I click on "Quick grading" "checkbox"
    Then the following should exist in the "submissions" table:
      | First name    | Marker 1    | Marker 2      | Status |
      | Student One   | Teacher One | Teacher Two   |        |
      | Student Two   | Teacher One | Teacher Two   |        |
      | Student Three | Teacher One | Teacher Two   |        |
      | Student Four  | Teacher One | Teacher Three |        |
    # Next we test adding a mark as Marker 1 (teacher1) to a student in Group 1. This should populate to the other
    # student in Group 1, but not the student with this same marker, who is not in Group 1.
    And I go to "Student One" "Assignment 2" activity advanced marking page
    And I set the field "Mark out of 100" to "50"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I go to "Student Four" "Assignment 2" activity advanced marking page
    And I set the field "Mark out of 100" to "60"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A2" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name    | Marker 1 | Marker 2 | Status     |
      | Student One   | 50       |          | In marking |
      | Student Two   | 50       |          | In marking |
      | Student Three | 60       |          | In marking |
      | Student Four  | 60       |          | In marking |
    # Next we add a mark as teacher2 to a student in both groups. The mark given to the student in Group 1 should
    # populate to the other student in Group 1. The mark given to the student in Group 2 should not, as they are not
    # an allocated marker for that final student.
    And I am on the "A2" "assign activity" page logged in as teacher2
    And I go to "Student One" "Assignment 2" activity advanced marking page
    And I set the field "Mark out of 100" to "30"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A2" "assign activity" page
    And I go to "Student Three" "Assignment 2" activity advanced marking page
    And I set the field "Mark out of 100" to "15"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A2" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name    | Marker 1 | Marker 2 | Status            |
      | Student One   | 50       | 30       | Marking completed |
      | Student Two   | 50       | 30       | Marking completed |
      | Student Three | 60       | 15       | Marking completed |
      | Student Four  | 60       |          | In marking        |
    # Then we login as teacher3 and add that last mark to the final student.
    And I am on the "A2" "assign activity" page logged in as teacher3
    And I go to "Student Four" "Assignment 2" activity advanced marking page
    And I set the field "Mark out of 100" to "99"
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I am on the "A2" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name    | Marker 1 | Marker 2 | Status            |
      | Student One   | 50       | 30       | Marking completed |
      | Student Two   | 50       | 30       | Marking completed |
      | Student Three | 60       | 15       | Marking completed |
      | Student Four  | 60       | 99       | Marking completed |
    # Then we check that the grades have been calculated correctly.
    And I am on the "A2" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | First name    | Marker 1 | Marker 2 | Status            | Grade |
      | Student One   | 50       | 30       | Marking completed | 50    |
      | Student Two   | 50       | 30       | Marking completed | 50    |
      | Student Three | 60       | 15       | Marking completed | 60    |
      | Student Four  | 60       | 99       | Marking completed | 99    |
    # Then we release them all and check the final grade column.
    And I am on the "A2" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the "Set marking workflow state" "dialogue"
    And I set the field "Workflow context" to "Grade"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And the following should exist in the "submissions" table:
      | First name    | Marker 1 | Marker 2 | Status   | Final grade |
      | Student One   | 50       | 30       | Released | 50          |
      | Student Two   | 50       | 30       | Released | 50          |
      | Student Three | 60       | 15       | Released | 60          |
      | Student Four  | 60       | 100      | Released | 99          |

  @javascript
  Scenario: The mark is read-only once released, and is preserved when the workflow state is changed
    Given the following "mod_assign > submissions" exist:
      | assign       | user     | onlinetext                       |
      | Assignment 1 | student1 | I'm the student first submission |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user     | marker   |
      | Assignment 1 | student1 | teacher1 |
      | Assignment 1 | student1 | teacher2 |
    And I am on the "Assignment 1" "assign activity" page logged in as teacher1
    And I go to "Student One" "Assignment 1" activity advanced marking page
    # Mark the assignment and check the field is still editable.
    And I set the following fields to these values:
      | Mark out of 100        | 50                |
      | Notify student         | 0                 |
      | Marking workflow state | Marking completed |
    And the "Mark out of 100" "field" should not be readonly
    And I press "Save changes"
    # Marking is not yet complete, as the second marker has not marked, so the grade cannot be released.
    And I am on the "Assignment 1" "assign activity" page logged in as teacher2
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And I set the following fields to these values:
      | Mark out of 100        | 50                |
      | Notify student         | 0                 |
      | Marking workflow state | Marking completed |
    And I press "Save changes"
    # The mark is not editable when the grade has been released.
    And I am on the "Assignment 1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the ".modal-footer" "css_element"
    And I select "Grade" from the "Workflow context" singleselect
    When I select "Released" from the "Marking workflow state" singleselect
    And I press "Save changes"
    Then I should see "50.00" in the "Student One" "table_row"
    And I should see "Released" in the "Student One" "table_row"
    And "User mark" "field" should not exist in the "Student One" "table_row"
    # The mark is not editable on the marker panel when the grade has been released.
    And I go to "Student One" "Assignment 1" activity advanced marking page
    And the following fields match these values:
      | Mark out of 100 | 50.00 |
    And the "Mark out of 100" "field" should be readonly
    And I follow "View all submissions"
    # The mark is editable again when the status it changed into an editable state.
    And I click on "Quick grading" "checkbox"
    And I set the field "selectall" to "1"
    And I click on "Change marking state" "button" in the "sticky-footer" "region"
    And I click on "Change marking state" "button" in the ".modal-footer" "css_element"
    And I select "Grade" from the "Workflow context" singleselect
    And I select "In review" from the "Marking workflow state" singleselect
    And I press "Save changes"
    And the following fields match these values:
      | User mark | 50.00 |
    And I should see "In review" in the "Student One" "table_row"
    And "User mark" "field" should exist in the "Student One" "table_row"
