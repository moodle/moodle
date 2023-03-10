@mod @mod_lesson
Feature: In a lesson activity, students can exit and re-enter the activity when it consists only of cluster pages
  As a student
  I need to exit and re-enter a lesson out and into clusters.

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
      | activity   | name                | course | idnumber    |
      | lesson     | Lesson with cluster | C1     | lesson1     |
    And I am on the "Lesson with cluster" "lesson activity" page logged in as teacher1
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select edit type "Expanded"
    And I click on "Add a cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][2]" "xpath_element"
    And I follow "Update page: Cluster"
    And I set the following fields to these values:
      | Page title | C Cluster |
      | Page contents | C Cluster |
      | Jump | Unseen question within a cluster |
    And I press "Save page"
    And I click on "Add a cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][2]" "xpath_element"
    And I follow "Update page: Cluster"
    And I set the following fields to these values:
      | Page title | B Cluster |
      | Page contents | B Cluster |
      | Jump | Unseen question within a cluster |
    And I press "Save page"
    And I click on "Add a cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][2]" "xpath_element"
    And I follow "Update page: Cluster"
    And I set the following fields to these values:
      | Page title | A Cluster |
      | Page contents | A Cluster |
      | Jump | Unseen question within a cluster |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][3]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 1 A Cluster |
      | Page contents | Question 1 from A cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | B Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][4]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 2 A Cluster |
      | Page contents | Question 2 from A cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | B Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][5]" "xpath_element"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 3 A Cluster |
      | Page contents | Question 3 from A cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | B Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add an end of cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][6]" "xpath_element"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][8]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 1 B Cluster |
      | Page contents | Question 1 from B cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | C Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][9]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 2 B Cluster |
      | Page contents | Question 2 from B cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | C Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][10]" "xpath_element"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 3 B Cluster |
      | Page contents | Question 3 from B cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | C Cluster |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add an end of cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][11]" "xpath_element"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][13]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 1 C Cluster |
      | Page contents | Question 1 from C cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][14]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 2 C Cluster |
      | Page contents | Question 2 from C cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][15]" "xpath_element"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Question 3 C Cluster |
      | Page contents | Question 3 from C cluster |
      | id_answer_editor_0 | Correct answer |
      | id_response_editor_0 | Good |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Incorrect answer |
      | id_response_editor_1 | Bad |
      | id_jumpto_1 | Unseen question within a cluster |
      | id_score_1 | 0 |
    And I press "Save page"
    And I click on "Add an end of cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][16]" "xpath_element"

  Scenario: Accessing as student to a cluster only lesson
    Given I am on the "Lesson with cluster" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Correct answer"
    And I set the following fields to these values:
      | Incorrect answer | 1 |
    And I press "Submit"
    And I should see "Bad"
    And I press "Continue"
    And I set the following fields to these values:
      | Incorrect answer | 1 |
    And I press "Submit"
    And I should see "Bad"
    And I press "Continue"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Incorrect answer"
    And I set the following fields to these values:
      | Incorrect answer | 1 |
    And I press "Submit"
    And I am on "Course 1" course homepage
    And I follow "Lesson with cluster"
    And I should see "Do you want to start at the last page you saw?"
    And I click on "No" "link" in the "#page-content" "css_element"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Correct answer"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    Then I should see "Correct answer"
