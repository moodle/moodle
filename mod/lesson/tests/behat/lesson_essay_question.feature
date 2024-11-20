@mod @mod_lesson @_file_upload
Feature: In a lesson activity, teacher can add an essay question
  As a teacher
  I need to add an essay question in a lesson and grade student attempts

  Scenario: questions with essay question
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name             | course | idnumber  | feedback |
      | lesson   | Test lesson name | C1     | lesson1   | 1        |
    And the following "mod_lesson > page" exist:
      | lesson           | qtype   | title          | content                                          |
      | Test lesson name | essay   | Essay question | <p>Please write a story about a <b>frog</b>.</p> |
    And the following "mod_lesson > answer" exist:
      | page           | jumpto    | score |
      | Essay question | Next page | 1     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "Please write a story about a frog."
    And I set the field "Your answer" to "<p>Once upon a time there was a little <b>green</b> frog."
    And I press "Submit"
    And I should see "Your answer"
    And I should see "Once upon a time there was a little green frog."
    And I should not see "&lt;b&gt;"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "You earned 0 out of 0 for the automatically graded questions."
    And I should see "Your 1 essay question(s) will be graded and added into your final score at a later date."
    And I should see "Your current grade without the essay question(s) is 0 out of 1."
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I grade lesson essays
    And I should see "Student 1"
    And I should see "Essay question"
    And I follow "Essay question"
    And I should see "Student 1's response"
    And I should see "Once upon a time there was a little green frog."
    And I set the following fields to these values:
      | Your comments | <p>Well <b>done</b>.</p> |
      | Essay score | 1 |
    And I press "Save changes"
    And I should see "Changes saved"
    And I navigate to "Reports" in current page administration
    And I should see "Student 1"
    And I click on ".lesson-attempt-link" "css_element" in the "Student 1" "table_row"
    And I should see "Essay: Essay question"
    And I should see "Please write a story about a frog."
    And I should see "Once upon a time there was a little green frog."
    And I should see "Well done."
    And I should not see "&lt;b&gt;"
