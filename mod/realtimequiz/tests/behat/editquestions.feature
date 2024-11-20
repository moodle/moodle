@mod @mod_realtimequiz
Feature: Teacher can create a realtime quiz and edit the questions

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | Teacher   | 1        | teacher1@moodle.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity     | course | name               | idnumber | intro                             | questiontime |
      | realtimequiz | C1     | Test realtime quiz | RTQ01    | Test the realtime quiz is working | 20           |

  @javascript
  Scenario: Create a quiz and edit the questions
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I am on the "Test realtime quiz" "realtimequiz activity" page
    # Create a question.
    When I press "Add question"
    And I set the following fields to these values:
      | Question text | Where was Moodle created? |
      | Question time | 0                         |
      | answertext[1] | France                    |
      | answertext[2] | Australia                 |
      | answertext[3] | Italy                     |
    And I set the field "id_answercorrect_2" to "1"
    And I press "Save question"
    Then I should see "Where was Moodle created?"
    # Edit question.
    When I follow "Where was Moodle created?"
    Then the field "id_answercorrect_2" matches value "1"
    When I set the field "answertext[4]" to "Germany"
    And I set the field "Question text" to "Where was Moodle originally created?"
    And I press "Add space for 3 more answers"
    Then the field "answertext[4]" matches value "Germany"
    And "input[name='answertext[5]']" "css_element" should be visible
    And "input[name='answertext[6]']" "css_element" should be visible
    And "input[name='answertext[7]']" "css_element" should be visible
    # Create a second question.
    When I press "Save question and add another"
    And I set the following fields to these values:
      | Question text | Which UK city is known as the Steel City? |
      | answertext[1] | Sheffield                                 |
      | answertext[2] | Manchester                                |
      | answertext[3] | London                                    |
    And I set the field "id_answercorrect_1" to "1"
    And I press "Save question"
    Then "Where was Moodle originally created?" "text" should appear before "Which UK city is known as the Steel City?" "text"
    # Create a third question.
    When I press "Add question"
    And I set the following fields to these values:
      | Question text | How many trees are there in Sheffield? |
      | answertext[1] | 200                                    |
      | answertext[2] | 60 million                             |
      | answertext[3] | 2.5 million                            |
    And I set the field "id_answercorrect_3" to "1"
    And I press "Save question"
    Then "Where was Moodle originally created?" "text" should appear before "Which UK city is known as the Steel City?" "text"
    And "Which UK city is known as the Steel City?" "text" should appear before "How many trees are there in Sheffield?" "text"
    # Reorder questions.
    And "Move question 1 down" "link" should be visible
    And "Move question 2 down" "link" should be visible
    And "Move question 2 up" "link" should be visible
    And "Move question 3 up" "link" should be visible
    And "Move question 3 down" "link" should not exist
    And "Move question 1 up" "link" should not exist
    When I click on "Move question 2 up" "link"
    Then "Which UK city is known as the Steel City?" "text" should appear before "Where was Moodle originally created?" "text"
    And "Where was Moodle originally created?" "text" should appear before "How many trees are there in Sheffield?" "text"
    When I click on "Move question 2 down" "link"
    Then "Which UK city is known as the Steel City?" "text" should appear before "How many trees are there in Sheffield?" "text"
    And "How many trees are there in Sheffield?" "text" should appear before "Where was Moodle originally created?" "text"
    # Delete questions.
    When I click on "Delete question 3" "link"
    And I press "Yes"
    Then I should not see "Where was Moodle originally created?"
    And "Which UK city is known as the Steel City?" "text" should appear before "How many trees are there in Sheffield?" "text"
    # Check the responses are empty.
    When I follow "View responses"
    Then I should see "This Realtime quiz has not yet been attempted"
