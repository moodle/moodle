@ou @ou_vle @qtype @qtype_oumultiresponse @_switch_window @javascript
Feature: Preview an OU multiple response question
  As a teacher
  In order to check my OU multiple response questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype           | name                | template    |
      | Test questions   | oumultiresponse | oumultiresponse 001 | two_of_four |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Preview a question and submit a partially correct response.
    When I choose "Preview" action for "oumultiresponse 001" in the question bank
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Two" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Two is even"
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."
    And I switch to the main window

  Scenario: Preview a question and submit a correct response.
    When I choose "Preview" action for "oumultiresponse 001" in the question bank
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Three" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Three is odd"
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The odd numbers are One and Three."
    And I should see "The correct answers are: One, Three"
    And I switch to the main window

  Scenario: Preview a question and submit a partially correct response and has partially correct feedback number.
    When I choose "Edit question" action for "oumultiresponse 001" in the question bank
    And I set the following fields to these values:
      | For any partially correct response                                  | Parts, but only parts, of your response are correct. |
      | For any incorrect response                                          | That is not right at all.                            |
      | Show the number of correct responses once the question has finished | 1                                                    |
    And I click on "#id_submitbutton" "css_element"
    When I choose "Preview" action for "oumultiresponse 001" in the question bank
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Two" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Two is even"
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."
    And I should see "You have correctly selected one option."
    And I switch to the main window
