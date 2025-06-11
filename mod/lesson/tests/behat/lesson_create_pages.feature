@mod @mod_lesson
Feature: In a lesson activity, teacher can create lesson's pages
  In order to set up an existing lesson
  As a teacher
  I need to create pages in the lesson

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name             | course | idnumber |
      | lesson   | Test lesson name | C1     | lesson1  |

  Scenario: Create content page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title         | First page name     |
      | Page contents      | First page contents |
      | id_answer_editor_0 | Forward             |
      | id_jumpto_0        | Next page           |
      | id_answer_editor_1 | Backward            |
      | id_jumpto_1        | Previous page       |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "First page name"
    And I should see "First page contents"
    And I should see "Forward" in the "Content 1" "table_row"
    And I should see "Next page" in the "Jump 1" "table_row"
    And I should see "Backward" in the "Content 2" "table_row"
    And I should see "Previous page" in the "Jump 2" "table_row"

  Scenario: Create essay page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Essay"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title    | Music essay                            |
      | Page contents | Write a really interesting music essay |
      | Jump          | End of lesson                          |
      | Score         | 1                                      |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "Music essay"
    And I should see "Write a really interesting music essay"
    And I should see "End of lesson" in the "Jump 1" "table_row"

  Scenario: Create matching page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Matching"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title           | Geography                        |
      | Page contents        | Match each city with its country |
      | id_answer_editor_0   | Correct!                         |
      | id_jumpto_0          | End of lesson                    |
      | id_score_0           | 2                                |
      | id_answer_editor_1   | Wrong!                           |
      | id_jumpto_1          | This page                        |
      | id_score_1           | 0                                |
      | id_answer_editor_2   | Barcelona                        |
      | id_response_editor_2 | Spain                            |
      | id_answer_editor_3   | Perth                            |
      | id_response_editor_3 | Australia                        |
      | id_answer_editor_4   | Tokyo                            |
      | id_response_editor_4 | Japan                            |
      | id_answer_editor_5   | Buenos Aires                     |
      | id_response_editor_5 | Argentina                        |
      | id_answer_editor_6   | Cairo                            |
      | id_response_editor_6 | Egypt                            |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "Geography"
    And I should see "Match each city with its country"
    And I should see "Correct!" in the "Correct response" "table_row"
    And I should see "2" in the "Correct answer score" "table_row"
    And I should see "End of lesson" in the "Correct answer jump" "table_row"
    And I should see "Wrong!" in the "Wrong response" "table_row"
    And I should see "0" in the "Wrong answer score" "table_row"
    And I should see "This page" in the "Wrong answer jump" "table_row"
    And I should see "Barcelona" in the "Answer 1" "table_row"
    And I should see "Spain" in the "Matches with answer 1" "table_row"
    And I should see "Perth" in the "Answer 2" "table_row"
    And I should see "Australia" in the "Matches with answer 2" "table_row"
    And I should see "Tokyo" in the "Answer 3" "table_row"
    And I should see "Japan" in the "Matches with answer 3" "table_row"
    And I should see "Buenos Aires" in the "Answer 4" "table_row"
    And I should see "Argentina" in the "Matches with answer 4" "table_row"
    And I should see "Cairo" in the "Answer 5" "table_row"
    And I should see "Egypt" in the "Matches with answer 5" "table_row"

  Scenario: Create multichoice page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title           | Multichoice question         |
      | Page contents        | What animal is an amphibian? |
      | id_answer_editor_0   | Frog                         |
      | id_response_editor_0 | Correct answer               |
      | id_jumpto_0          | End of lesson                |
      | id_score_0           | 2                            |
      | id_answer_editor_1   | Cat                          |
      | id_response_editor_1 | Incorrect answer             |
      | id_jumpto_1          | This page                    |
      | id_score_1           | 0                            |
      | id_answer_editor_2   | Dog                          |
      | id_response_editor_2 | Incorrect answer             |
      | id_jumpto_2          | Next page                    |
      | id_score_2           | 0                            |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "Multichoice question"
    And I should see "What animal is an amphibian?"
    And I should see "Frog" in the "Answer 1" "table_row"
    And I should see "Correct answer" in the "Response 1" "table_row"
    And I should see "End of lesson" in the "//tr[contains(.,'Jump')][1]" "xpath_element"
    And I should see "2" in the "//tr[contains(.,'Score')][1]" "xpath_element"
    And I should see "Cat" in the "Answer 2" "table_row"
    And I should see "Incorrect answer" in the "Response 2" "table_row"
    And I should see "This page" in the "//tr[contains(.,'Jump')][2]" "xpath_element"
    And I should see "0" in the "//tr[contains(.,'Score')][2]" "xpath_element"
    And I should see "Dog" in the "Answer 3" "table_row"
    And I should see "Incorrect answer" in the "Response 3" "table_row"
    And I should see "Next page" in the "//tr[contains(.,'Jump')][3]" "xpath_element"
    And I should see "0" in the "//tr[contains(.,'Score')][3]" "xpath_element"

  Scenario: Create numerical page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title            | Really hard question |
      | Page contents         | What is 1 + 2?       |
      | id_answer_editor_0    | 3                    |
      | id_response_editor_0  | Correct              |
      | id_jumpto_0           | End of lesson        |
      | id_score_0            | 2                    |
      | id_answer_editor_1    | 2                    |
      | id_response_editor_1  | Close, but wrong     |
      | id_jumpto_1           | Next page            |
      | id_score_1            | 1                    |
      | id_enableotheranswers | 1                    |
      | id_response_editor_6  | Wrong                |
      | id_jumpto_6           | This page            |
      | id_score_6            | 0                    |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "Really hard question"
    And I should see "What is 1 + 2?"
    And I should see "3" in the "Answer 1" "table_row"
    And I should see "Correct" in the "Response 1" "table_row"
    And I should see "End of lesson" in the "//tr[contains(.,'Jump')][1]" "xpath_element"
    And I should see "2" in the "//tr[contains(.,'Score')][1]" "xpath_element"
    And I should see "2" in the "Answer 2" "table_row"
    And I should see "Close, but wrong" in the "Response 2" "table_row"
    And I should see "Next page" in the "//tr[contains(.,'Jump')][2]" "xpath_element"
    And I should see "1" in the "//tr[contains(.,'Score')][2]" "xpath_element"
    And I should see "@#wronganswer#@" in the "Answer 3" "table_row"
    And I should see "Wrong" in the "Response 3" "table_row"
    And I should see "This page" in the "//tr[contains(.,'Jump')][3]" "xpath_element"
    And I should see "0" in the "//tr[contains(.,'Score')][3]" "xpath_element"

  Scenario: Create short answer page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Short answer"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title            | Geography                                |
      | Page contents         | Capital of Canada                        |
      | id_answer_editor_0    | Ottawa                                   |
      | id_response_editor_0  | Correct                                  |
      | id_jumpto_0           | End of lesson                            |
      | id_score_0            | 2                                        |
      | id_answer_editor_1    | Toronto                                  |
      | id_response_editor_1  | It's in Canada, but it's not the capital |
      | id_jumpto_1           | Next page                                |
      | id_score_1            | 1                                        |
      | id_answer_editor_2    | Vancouver                                |
      | id_response_editor_2  | It's in Canada, but it's not the capital |
      | id_jumpto_2           | Next page                                |
      | id_score_2            | 1                                        |
      | id_enableotheranswers | 1                                        |
      | id_response_editor_6  | Wrong                                    |
      | id_jumpto_6           | This page                                |
      | id_score_6            | 0                                        |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "Geography"
    And I should see "Capital of Canada"
    And I should see "Ottawa" in the "Answer 1" "table_row"
    And I should see "Correct" in the "Response 1" "table_row"
    And I should see "End of lesson" in the "//tr[contains(.,'Jump')][1]" "xpath_element"
    And I should see "2" in the "//tr[contains(.,'Score')][1]" "xpath_element"
    And I should see "Toronto" in the "Answer 2" "table_row"
    And I should see "It's in Canada, but it's not the capital" in the "Response 2" "table_row"
    And I should see "Next page" in the "//tr[contains(.,'Jump')][2]" "xpath_element"
    And I should see "1" in the "//tr[contains(.,'Score')][2]" "xpath_element"
    And I should see "Vancouver" in the "Answer 3" "table_row"
    And I should see "It's in Canada, but it's not the capital" in the "Response 3" "table_row"
    And I should see "Next page" in the "//tr[contains(.,'Jump')][3]" "xpath_element"
    And I should see "1" in the "//tr[contains(.,'Score')][3]" "xpath_element"
    And I should see "@#wronganswer#@" in the "Answer 4" "table_row"
    And I should see "Wrong" in the "Response 4" "table_row"
    And I should see "This page" in the "//tr[contains(.,'Jump')][4]" "xpath_element"
    And I should see "0" in the "//tr[contains(.,'Score')][4]" "xpath_element"

  Scenario: Create true/false page
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title           | True/false question       |
      | Page contents        | Paper is made from trees. |
      | id_answer_editor_0   | True                      |
      | id_response_editor_0 | Correct                   |
      | id_jumpto_0          | End of lesson             |
      | id_score_0           | 2                         |
      | id_answer_editor_1   | False                     |
      | id_response_editor_1 | Wrong                     |
      | id_jumpto_1          | This page                 |
      | id_score_1           | 0                         |
    And I press "Save page"
    And I select edit type "Expanded"
    Then I should see "True/false question"
    And I should see "Paper is made from trees."
    And I should see "True" in the "Answer 1" "table_row"
    And I should see "Correct" in the "Response 1" "table_row"
    And I should see "End of lesson" in the "//tr[contains(.,'Jump')][1]" "xpath_element"
    And I should see "2" in the "//tr[contains(.,'Score')][1]" "xpath_element"
    And I should see "False" in the "Answer 2" "table_row"
    And I should see "Wrong" in the "Response 2" "table_row"
    And I should see "This page" in the "//tr[contains(.,'Jump')][2]" "xpath_element"
    And I should see "0" in the "//tr[contains(.,'Score')][2]" "xpath_element"

  Scenario: Create cluster pages
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a cluster"
    And I select edit type "Expanded"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][2]" "xpath_element"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title           | question 1            |
      | Page contents        | Question from cluster |
      | id_answer_editor_0   | Correct answer        |
      | id_response_editor_0 | Good                  |
      | id_jumpto_0          | Cluster               |
      | id_score_0           | 1                     |
      | id_answer_editor_1   | Incorrect answer      |
      | id_response_editor_1 | Bad                   |
      | id_jumpto_1          | This page             |
      | id_score_1           | 0                     |
    And I press "Save page"
    And I click on "Add a content page" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][3]" "xpath_element"
    And I set the following fields to these values:
      | Page title         | Second page name                                                                     |
      | Page contents      | This page mark the the beginning of the subcluster it should not be seen by students |
      | id_answer_editor_0 | Next page                                                                            |
      | id_jumpto_0        | Next page                                                                            |
    And I press "Save page"
    And I click on "Add an end of branch" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][4]" "xpath_element"
    And I click on "Add an end of cluster" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][5]" "xpath_element"
    Then I should see "Cluster"
    And I should see "Unseen question within a cluster" in the "//tr[contains(.,'Jump')][1]" "xpath_element"
    And I should see "Question from cluster"
    And I should see "This page mark the the beginning of the subcluster it should not be seen by students"
    And I should see "End of branch"
    And I should see "End of cluster"
