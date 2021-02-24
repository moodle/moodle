@mod @mod_lesson
Feature: In a lesson activity, students can see their progress viewing a progress bar.
  In order to create a lesson with conditional paths
  As a teacher
  I need to add pages and questions with links between them

  Scenario: Student navigation with progress bar
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
      | activity   | name             | intro                   | course | section | idnumber  |
      | lesson     | Test lesson name | Test lesson description | C1     | 1       | lesson1   |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Progress bar | Yes |
    And I press "Save and return to course"
    And I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | Next page |
      | id_jumpto_1 | Next page |
    And I press "Save page"
    And I click on "Expanded" "link" in the "region-main" "region"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][3]" "xpath_element"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Hardest question ever |
      | Page contents | 1 + 1? |
      | id_answer_editor_0 | 2 |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | Second page name |
      | id_score_1 | 0 |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test lesson name"
    Then I should see "First page contents"
    And I should see "You have completed 0% of the lesson"
    And I press "Next page"
    And I should see "Second page contents"
    And I should see "You have completed 33% of the lesson"
    And I press "Previous page"
    And I should see "First page contents"
    And I should see "You have completed 67% of the lesson"
    And I press "Next page"
    And I should see "Second page contents"
    And I should see "You have completed 67% of the lesson"
    And I press "Next page"
    And I should see "1 + 1?"
    And I should see "You have completed 67% of the lesson"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "Correct answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "You have completed 100% of the lesson"
