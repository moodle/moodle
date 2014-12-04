@mod @mod_lesson
Feature: In a lesson activity, a non editing teacher can grade essay questions
  As a non editing teacher
  I need to grade student answers to essay questions in lesson

  @javascript
  Scenario: non editing teacher grade essay questions
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | teacher2 | Teacher | 2 | teacher2@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
    And I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Essay"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Essay question |
      | Page contents | <p>Please write a story about a frog.</p> |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I set the field "Your answer" to "<p>Once upon a time there was a little green frog."
    And I press "Submit"
    And I log out
    When I log in as "teacher2"
    And I follow "Course 1"
    And I follow "Test lesson name"
    Then I should see "Grade essays"
    And I follow "Grade essays"
    And I should see "Student 1"
    And I should see "Essay question"
    And I follow "Essay question"
    And I should see "Student 1's response"
    And I should see "Once upon a time there was a little green frog."
    And I set the following fields to these values:
      | Your comments | Well done. |
      | Essay score | 1 |
    And I press "Save changes"
    And I should see "Changes saved"
