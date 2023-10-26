@mod @mod_lesson
Feature: In a lesson activity, a non editing teacher can grade essay questions
  As a non editing teacher
  I need to grade student answers to essay questions in lesson

  Scenario: non editing teacher grade essay questions
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group A | C1 | G1 |
      | Group B | C1 | G2 |
      | Group C | C1 | G3 |
    And the following "group members" exist:
      | user | group |
      | teacher1 | G1 |
      | teacher2 | G2 |
      | student1 | G1 |
      | student2 | G2 |
      | student3 | G3 |
    And the following "activities" exist:
      | activity | name             | course | idnumber |
      | lesson   | Test lesson name | C1     | lesson1  |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Essay"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Essay question |
      | Page contents | <p>Please write a story about a frog.</p> |
    And I press "Save page"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I set the field "Your answer" to "<p>Once upon a time there was a little green frog."
    And I press "Submit"
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I set the field "Your answer" to "<p>Once upon a time there were two little green frogs."
    And I press "Submit"
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    Then I should see "Grade essays"
    And I grade lesson essays
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Essay question"
    And I click on "Essay question" "link" in the "Student 1" "table_row"
    And I should see "Student 1's response"
    And I should see "Once upon a time there was a little green frog."
    And I set the following fields to these values:
      | Your comments | Well done. |
      | Essay score | 1 |
    And I press "Save changes"
    And I should see "Changes saved"
    And I select "Group A" from the "Separate groups" singleselect
    And I should see "Student 1"
    And I should not see "Student 2"
    And I select "Group B" from the "Separate groups" singleselect
    And I should see "Student 2"
    And I should not see "Student 1"
    And I select "Group C" from the "Separate groups" singleselect
    And I should see "No one in Group C has answered an essay question yet."
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher2
    Then I should see "Grade essays"
    And I grade lesson essays
    And I should not see "Student 1"
    And I should see "Student 2"
