@mod @mod_quiz  @qtype @qtype_gapfill @javascript @qtype_gapfill_add_quiz

Feature: Add a Gapfill quiz
    In order to evaluate students as a teacher
  Scenario: Add a small quiz with gapfill questions and make an attempt
  Background:
    Given the following "users" exist:
        | username | firstname | lastname | email                |
        | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
        | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
        | user     | course | role           |
        | teacher1 | C1     | editingteacher |
        | student1 | C1     | student        |
    And the following "question categories" exist:
        | contextlevel | reference | name           |
        | Course       | C1        | Test questions |
    And the following "questions" exist:
        | questioncategory | qtype   | name            | questiontext                     | generalfeedback    |
        | Test questions   | gapfill | First question  | The [cat] sat on the [mat]       | Question1 feedback |
        | Test questions   | gapfill | Second question | The [cow] jumped over the [moon] | Question2 feedback |

    And the following "activity" exists:
        | activity           | quiz                                              |
        | course             | C1                                                |
        | name               | Gapfill single page quiz                          |
        | description        | Test Gapfill with more than one question per page |
        | idnumber           | 0001                                              |
        | section            | 0                                                 |
        | preferredbehaviour | interactive                                       |

    And quiz "Gapfill single page quiz" contains the following questions:
        | question        | page | requireprevious |
        | First question  | 1    | 0               |
        | Second question | 1    | 0               |

#############################################################################
#All questions on a single page. This will check that javascript only works
#on the current question and is not applied to every question as happened
#with an early bug
##############################################################################

    And I log in as "student1"
    And I am on "Course 1" course homepage

    And I follow "Gapfill single page quiz"
    And I press "Attempt quiz"
    Then I should see "Question 1"
    And I type "cat" into gap "1" in the gapfill question
    And I type "mat" into gap "2" in the gapfill question
    And I press "Check"

    Then I should see "Question1 feedback"
    And I should not see "Question2 feedback"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I am on "Course 1" course homepage
    And I log out
##########################################################################################
# Reformat for one question per page
##########################################################################################
    And I am on the "Gapfill single page quiz" "mod_quiz > Edit" page logged in as "teacher1"
    And I click on "Attempts" "link"
    And I click on "mod-quiz-report-overview-report-selectall-attempts" "checkbox"
    And I click on "Delete selected attempts" "button"
    # And I click on "Yes" "button" in the "Confirmation" "dialogue"
    And I click on "Yes" "button"
    And I am on the "Gapfill single page quiz" "mod_quiz > Edit" page logged in as "teacher1"
    And I press "Repaginate"
    Then I click on "Go" "button" in the "Repaginate" "dialogue"
    And I log out
#########################################################################################
# The and I follow part will not work so the next bit is commented out
##########################################################################################
    # And I log in as "student1"
    # And I am on "Course 1" course homepage
    # And I follow "Gapfill single page quiz"
    # And I press "Attempt quiz"

    # Then I should see "Question 1"
    # And I type "cat" into gap "1" in the gapfill question
    # And I press "Next page"
    # Then I should see "Question 2"
    # And I type "cow" into gap "1" in the gapfill question
    # And I type "moon" into gap "2" in the gapfill question
    # And I press "Finish attempt ..."
    # And I press "Submit all and finish"
    # And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    # And I follow "Finish review"
    # Then I log out
