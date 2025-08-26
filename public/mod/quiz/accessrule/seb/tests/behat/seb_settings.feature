@mod_quiz @quizaccess @quizaccess_seb
Feature: SEB settings in quiz access rule

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |

  Scenario Outline: Require the use of Safe Exam Browser set to Yes – Configure manually shows message to students
    Given the following "activities" exist:
      | activity | course | name   | seb_requiresafeexambrowser | idnumber |
      | quiz     | C1     | Quiz 1 | <sebsetting>               | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name      | questiontext       |
      | Test questions   | truefalse | Reading   | Can you read this? |
    And quiz "Quiz 1" contains the following questions:
      | question  | page |
      | Reading   | 1    |
    When I am on the "Quiz 1" "quiz activity" page logged in as student1
    Then I <messagevisibility> see "This quiz has been configured so that students may only attempt it using the Safe Exam Browser."
    And "Attempt quiz" "button" <attemptbuttonvisibility> exist

    Examples:
      | sebsetting | messagevisibility | attemptbuttonvisibility |
      | 0          | should not        | should                  |
      | 1          | should            | should not              |

  Scenario Outline: Show Safe Exam Browser download button setting controls visibility of download link
    Given the following "activities" exist:
      | activity | course | name   | idnumber | seb_requiresafeexambrowser | seb_showsebdownloadlink   |
      | quiz     | C1     | Quiz 1 | quiz1    | 1                          | <seb_showsebdownloadlink> |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name      | questiontext       |
      | Test questions   | truefalse | Reading   | Can you read this? |
    And quiz "Quiz 1" contains the following questions:
      | question  | page |
      | Reading   | 1    |
    When I am on the "Quiz 1" "quiz activity" page logged in as student1
    Then "Download Safe Exam Browser" "link" <downloadseblinkvisibility> exist
    And I should see "This quiz has been configured so that students may only attempt it using the Safe Exam Browser."

    Examples:
      | seb_showsebdownloadlink | downloadseblinkvisibility |
      | 0                       | should not                |
      | 1                       | should                    |
