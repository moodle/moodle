@javascript @qtype_matchwiris @studentwiris @wqmdl-271
Feature: Student answers a quiz with a Matching (WIRIS) question

    Background:
    Given the following "users" exist:
        | username | firstname | lastname | email                |
        | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
        | fullname | shortname |
        | Course 1 | C1        |
    And the following "course enrolments" exist:
        | user     | course | role    |
        | student1 | C1     | student |

    And the following "activities" exist:
        | activity | name                     | course | idnumber |
        | quiz     | WIRIS Matching Quiz      | C1     | quiz_mt  |

    And the following "question categories" exist:
        | contextlevel | reference | name       |
        | Course       | C1        | WIRIS bank |

    # Two pairs for a simple match
    And the following "questions" exist:
        | questioncategory | qtype        | name                     | questiontext                                   | defaultmark | subquestions[1] | subanswers[1] | subquestions[2] | subanswers[2] |
        | WIRIS bank       | matchwiris   | Match WIRIS capitals   | <p>Match each country to its capital.</p>     | 2.0         | One          | 1         | Two           | 2          |

    And quiz "WIRIS Matching Quiz" contains the following questions:
        | question                 | page |
        | Match WIRIS capitals   | 1    |

    Scenario: Student attempts and submits the Matching (WIRIS) quiz
    Given I am on the "WIRIS Matching Quiz" "mod_quiz > View" page logged in as "student1"
    When I press "Attempt quiz"
    And I set the field "Answer 1 Question 1" to "1"
    And I set the field "Answer 2 Question 1" to "2"
    And I click on "Finish attempt ..." "link"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    #Then I should see "Review"