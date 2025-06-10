@mod @mod_adaptivequiz
Feature: Attempt feedback
  In order to get inspired and engaged
  As a student
  I want to get feedback on my attempts on adaptive quiz and ability estimation if I'm allowed to

  Background:
    Given the following "users" exist:
      | username | firstname | lastname    | email                       |
      | teacher1 | John      | The Teacher | johntheteacher@example.com  |
      | student1 | Peter     | The Student | peterthestudent@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name                    |
      | Course       | C1        | Adaptive Quiz Questions |
    And the following "questions" exist:
      | questioncategory        | qtype     | name | questiontext    | answer |
      | Adaptive Quiz Questions | truefalse | Q1   | First question  | True   |
      | Adaptive Quiz Questions | truefalse | Q2   | Second question | True   |
    And the following "core_question > Tags" exist:
      | question | tag    |
      | Q1       | adpq_1 |
      | Q2       | adpq_2 |
    And the following "activity" exists:
      | activity           | adaptivequiz            |
      | idnumber           | adaptivequiz1           |
      | course             | C1                      |
      | name               | Adaptive Quiz           |
      | startinglevel      | 1                       |
      | lowestlevel        | 1                       |
      | highestlevel       | 2                       |
      | minimumquestions   | 1                       |
      | maximumquestions   | 2                       |
      | standarderror      | 20                      |
      | questionpoolnamed  | Adaptive Quiz Questions |
      | showabilitymeasure | 1                       |

  @javascript
  Scenario: Get default textual feedback after an attempt is finished
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    Then I should see "You've finished the attempt, thank you for taking the quiz!"

  @javascript
  Scenario: Get customized textual feedback after an attempt is finished
    Given I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Attempt feedback | Thank you for taking the test! |
    And I click on "Save and return to course" "button"
    And I log out
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    Then I should see "Thank you for taking the test!"

  @javascript
  Scenario: Get estimated ability after an attempt is finished
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    Then I should see "Estimated ability: 1.7 / 1 - 2"

  @javascript
  Scenario: View attempt summary with estimated ability for the only allowed attempt
    Given I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Attempts allowed | 1 |
    And I click on "Save and return to course" "button"
    And I log out
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    Then I should see "Attempt Summary"
    And "attemptsummarytable" "table" should exist
    And I should see "Completed" in the "#attemptstatecell" "css_element"
    And I should see "1.7 / 1 - 2" in the "#abilitymeasurecell" "css_element"

  @javascript
  Scenario: View attempts summary with estimated ability for several attempts
    Given I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    When I am on the "adaptivequiz1" "Activity" page
    Then I should see "Your previous attempts"
    And "userattemptstable" "table" should exist
    And I should see "Estimated ability / 1 - 2" in the "th.abilitymeasurecol" "css_element"
    And I should see "Completed" in the "#userattemptstable_r0 td.statecol" "css_element"
    And I should see "Completed" in the "#userattemptstable_r1 td.statecol" "css_element"
    And I should see "1.7" in the "#userattemptstable_r0 td.abilitymeasurecol" "css_element"
    And I should see "1.7" in the "#userattemptstable_r1 td.abilitymeasurecol" "css_element"

  @javascript
  Scenario: Estimated ability after an attempt is finished is not visible when set accordingly
    Given I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Show ability measure to students | No |
    And I click on "Save and return to course" "button"
    And I log out
    When I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    Then I should not see "Estimated ability: 1.7 / 1 - 2"

  @javascript
  Scenario: Estimated ability for the only allowed attempt is not visible for a student when set accordingly
    Given I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Attempts allowed                 | 1  |
      | Show ability measure to students | No |
    And I click on "Save and return to course" "button"
    And I log out
    And I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    When I am on the "adaptivequiz1" "Activity" page
    Then "#abilitymeasurecell" "css_element" should not exist

  @javascript
  Scenario: Estimated ability is not visible for a student in attempts summary when set accordingly
    Given I am on the "adaptivequiz1" "Activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Show ability measure to students | No |
    And I click on "Save and return to course" "button"
    And I log out
    And I am on the "adaptivequiz1" "Activity" page logged in as "student1"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    And I click on "Start attempt" "link"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Submit answer"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Submit answer"
    And I press "Continue"
    When I am on the "adaptivequiz1" "Activity" page
    Then ".abilitymeasurecol" "css_element" should not exist
