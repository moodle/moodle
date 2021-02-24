@mod @mod_lesson
Feature: In a lesson activity, students can see questions in random order
  In order to create a lesson with clusters
  As a teacher
  I need to add content pages and questions with clusters and end of clusters pages

  Background:
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
      | activity | name                 | intro                    | course | idnumber | section |
      | lesson   | Lesson with clusters | Test lesson description  | C1     | lesson1  | 1       |
    And I log in as "teacher1"

  Scenario: Lesson with two clusters
    Given I am on "Course 1" course homepage with editing mode on
    And I follow "Lesson with clusters"
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
    And I follow "Expanded"
    And I click on "Add a cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][3]" "xpath_element"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][4]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | question 1 |
      | Page contents | Question from cluster 1 |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][5]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | question 2 |
      | Page contents | Question from cluster 1 |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add an end of cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][6]" "xpath_element"
    And I click on "Add a content page" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][7]" "xpath_element"
    And I set the following fields to these values:
      | Page title | Third page name |
      | Page contents | Content page after cluster 1 |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I click on "Add a cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][8]" "xpath_element"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][9]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | question 3 |
      | Page contents | Question from cluster 2 |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | Unseen question within a cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][10]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | question 4 |
      | Page contents | Question from cluster 2 |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | Unseen question within a cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add an end of cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][11]" "xpath_element"
    And I click on "Add a content page" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][12]" "xpath_element"
    And I set the following fields to these values:
      | Page title | Fourth page name |
      | Page contents | Content page after cluster 2 |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Lesson with clusters"
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Question from cluster 1"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster 1"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Content page after cluster 1"
    And I press "Next page"
    And I should see "Question from cluster 2"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster 2"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Content page after cluster 2"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 4 (out of 4)."
