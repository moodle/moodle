@mod @mod_realtimequiz
Feature: Students can attempt a quiz under the control of a teacher

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | Teacher   | 1        | teacher1@moodle.com |
      | student1 | Student   | 1        | student1@moodle.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity     | course | name               | idnumber | intro                             | questiontime |
      | realtimequiz | C1     | Test realtime quiz | RTQ01    | Test the realtime quiz is working | 15           |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test realtime quiz" "realtimequiz activity" page
    And I press "Add question"
    And I set the following fields to these values:
      | Question text | Which UK city is known as the Steel City? |
      | answertext[1] | Sheffield                                 |
      | answertext[2] | Manchester                                |
      | answertext[3] | London                                    |
    And I set the field "id_answercorrect_1" to "1"
    And I press "Save question and add another"
    And I set the following fields to these values:
      | Question text | How many trees are there in Sheffield? |
      | answertext[1] | 200                                    |
      | answertext[2] | 60 million                             |
      | answertext[3] | 2.5 million                            |
    And I set the field "id_answercorrect_3" to "1"
    And I press "Save question and add another"
    And I set the following fields to these values:
      | Question text | What is your favourite colour? |
      | answertext[1] | Red                            |
      | answertext[2] | Green                          |
      | answertext[3] | Purple                         |
    And I set the field "No 'right' answer" to "1"
    And I press "Save question"

  @javascript
  Scenario: Teacher starts quiz, then students attempt it
    When I am on the "Test realtime quiz" "realtimequiz activity" page
    And I set the field "sessionname" to "Test session"
    And I press "Start quiz"
    # Question 1.
    And I press "Next"
    And I wait "2" seconds
    Then I should see "Which UK city is known as the Steel City?"
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on the "Test realtime quiz" "realtimequiz activity" page
    And I press "Join"
    Then I should see "Which UK city is known as the Steel City?"
    When I click on "A" "button" in the "#questionarea" "css_element"
    Then I should see "Answer sent - waiting"
    # Question 1 results.
    When I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "teacher1"
    And I press "Reconnect to quiz"
    And I wait "10" seconds
    # Question 1 results.
    Then I should see "Sheffield" in the "li.realtimequiz-answer-pos-1" "css_element"
    And I should see "1" in the "li.realtimequiz-answer-pos-1" "css_element"
    And I should see "Manchester" in the "li.realtimequiz-answer-pos-2" "css_element"
    And I should see "0" in the "li.realtimequiz-answer-pos-2" "css_element"
    And I should see "London" in the "li.realtimequiz-answer-pos-3" "css_element"
    And I should see "0" in the "li.realtimequiz-answer-pos-3" "css_element"
    And I should see "This question: 100% correct. Overall: 100% correct."
    # Question 2.
    When I press "Next"
    And I wait "2" seconds
    Then I should see "How many trees are there in Sheffield?"
    And I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "student1"
    And I press "Join"
    Then I should see "How many trees are there in Sheffield?"
    When I click on "B" "button" in the "#questionarea" "css_element"
    Then I should see "Answer sent - waiting"
    # Question 2 results.
    When I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "teacher1"
    And I press "Reconnect to quiz"
    And I wait "10" seconds
    Then I should see "200" in the "li.realtimequiz-answer-pos-1" "css_element"
    And I should see "0" in the "li.realtimequiz-answer-pos-1" "css_element"
    And I should see "60 million" in the "li.realtimequiz-answer-pos-2" "css_element"
    And I should see "1" in the "li.realtimequiz-answer-pos-2" "css_element"
    And I should see "2.5 million" in the "li.realtimequiz-answer-pos-3" "css_element"
    And I should see "0" in the "li.realtimequiz-answer-pos-3" "css_element"
    And I should see "This question: 0% correct. Overall: 50% correct."
    # Question 3.
    When I press "Next"
    And I wait "2" seconds
    Then I should see "What is your favourite colour?"
    And I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "student1"
    And I press "Join"
    Then I should see "What is your favourite colour?"
    When I click on "C" "button" in the "#questionarea" "css_element"
    Then I should see "Answer sent - waiting"
    # Question 3 results.
    When I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "teacher1"
    And I press "Reconnect to quiz"
    And I wait "10" seconds
    Then I should see "Red" in the "li.realtimequiz-answer-pos-1" "css_element"
    And I should see "0" in the "li.realtimequiz-answer-pos-1" "css_element"
    And I should see "Green" in the "li.realtimequiz-answer-pos-2" "css_element"
    And I should see "0" in the "li.realtimequiz-answer-pos-2" "css_element"
    And I should see "Purple" in the "li.realtimequiz-answer-pos-3" "css_element"
    And I should see "1" in the "li.realtimequiz-answer-pos-3" "css_element"
    # Final results.
    When I press "Next"
    Then I should see "Final results"
    And I should see "Class result: 66% correct."

    # Check the responses.
    When I follow "View responses"
    Then "100%" "text" in the "Which UK city is known as the Steel City?" "table_row" should be visible
    And "0%" "text" in the "How many trees are there in Sheffield?" "table_row" should be visible
    And "1" "text" in the "What is your favourite colour?" "table_row" should be visible
    And "0" "text" in the "What is your favourite colour?" "table_row" should be visible
    When I set the field "showsession" to "Test session"
    And I press "Show"
    Then "100%" "text" in the "Which UK city is known as the Steel City?" "table_row" should be visible
    And "0%" "text" in the "How many trees are there in Sheffield?" "table_row" should be visible
    And "1" "text" in the "What is your favourite colour?" "table_row" should be visible
    And "0" "text" in the "What is your favourite colour?" "table_row" should be visible
    When I follow "Which UK city is known as the Steel City?"
    Then "*[title='Correct answer']" "css_element" in the "Student 1" "table_row" should be visible
    When I press "Next question"
    Then "*[title='Wrong answer']" "css_element" in the "Student 1" "table_row" should be visible
    When I press "Next question"
    Then "*[title='Correct answer']" "css_element" in the "Student 1" "table_row" should be visible
    When I press "Back to full results"
    And I follow "Show users"
    Then I should see "Student 1"
    # TODO this should probably be fixed at some point to give the correct answer when there are
    # 'no right answer' questions present
    And I should see "Average class score is 33.33%"
