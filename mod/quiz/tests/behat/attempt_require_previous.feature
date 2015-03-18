@mod @mod_quiz
Feature: Attemp a quiz where some questions require that the previous question has been answered.
  In order to complete a quiz where questions require previous ones to be complete
  As a student
  I need later questions to appear once earlier ones have been answered.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | student  | Student   | One      | student@moodle.com |
      | teacher  | Teacher   | One      | teacher@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student  | C1     | student |
      | teacher  | C1     | teacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |

  @javascript
  Scenario: A question that requires the previous one is initally blocked
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 0               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"

    Then I should see "First question"
    And I should see "This question cannot be attempted until the previous question has been completed."
    And I should not see "Second question"
    And I log out
    And I log in as "teacher"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Attempts: 1"
    And I follow "Review attempt"
    And I should see "First question"
    And I should see "This question cannot be attempted until the previous question has been completed."
    And I should not see "Second question"

  @javascript
  Scenario: A question requires the previous one becomes available when the first one is answered
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 0               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "True" "radio" in the "First question" "question"
    And I press "Check"

    Then I should see "First question"
    And I should not see "This question cannot be attempted until the previous question has been completed."
    And I should see "Second question"

  @javascript
  Scenario: After quiz submitted, all questions show on the review page
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz     | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 0               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I press "Next"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"

    Then the state of "First question" question is shown as "Not answered"
    And the state of "Second question" question is shown as "Not answered"

  @javascript
  Scenario: A questions cannot be blocked in a deferred feedback quiz (despite what is set in the DB).
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | deferredfeedback   |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 0               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"

    Then I should see "First question"
    And I should see "Second question"
    And I should not see "This question cannot be attempted until the previous question has been completed."

  @javascript
  Scenario: A questions cannot be blocked in a shuffled quiz (despite what is set in the DB).
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour | shufflequestions | questionsperpage |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  | 1                | 2                |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 1               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"

    Then I should see "First question"
    And I should see "Second question"
    And I should not see "This question cannot be attempted until the previous question has been completed."

  @javascript
  Scenario: A questions cannot be blocked in sequential quiz (despite what is set in the DB).
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour | navmethod  |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  | sequential |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 1               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"

    Then I should see "First question"
    And I should see "Second question"
    And I should not see "This question cannot be attempted until the previous question has been completed."

  @javascript
  Scenario: A questions not blocked if the previous one cannot finish, e.g. essay (despite what is set in the DB).
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | essay       | Story | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | Story    | 1    | 0               |
      | TF2      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"

    Then I should see "First question"
    And I should see "Second question"
    And I should not see "This question cannot be attempted until the previous question has been completed."

  @javascript
  Scenario: A questions not blocked if the previous one cannot finish, e.g. description (despite what is set in the DB).
    Given the following "questions" exist:
      | questioncategory | qtype       | name | questiontext   |
      | Test questions   | description | Info | Read me        |
      | Test questions   | truefalse   | TF1  | First question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | Info     | 1    | 0               |
      | TF1      | 1    | 1               |

    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"

    Then I should see "Read me"
    And I should see "First question"
    And I should not see "This question cannot be attempted until the previous question has been completed."
