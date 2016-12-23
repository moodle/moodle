@block @block_course_overview @mod_quiz
Feature: View the quiz being due
  In order to know what quizzes are due
  As a student
  I can visit my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C2     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name                    | timeclose  |
      | quiz     | C1     | Q1A      | Quiz 1A No deadline     | 0          |
      | quiz     | C1     | Q1B      | Quiz 1B Past deadline   | 1337       |
      | quiz     | C1     | Q1C      | Quiz 1C Future deadline | 9000000000 |
      | quiz     | C1     | Q1D      | Quiz 1D Future deadline | 9000000000 |
      | quiz     | C1     | Q1E      | Quiz 1E Future deadline | 9000000000 |
      | quiz     | C2     | Q2A      | Quiz 2A Future deadline | 9000000000 |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | qtype     | name           | questiontext              | questioncategory |
      | truefalse | First question | Answer the first question | Test questions   |
    And quiz "Quiz 1A No deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 1B Past deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 1C Future deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 1D Future deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 1E Future deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 2A Future deadline" contains the following questions:
      | question       | page |
      | First question | 1    |

  Scenario: View my quizzes that are due
    Given I log in as "student1"
    When I am on homepage
    Then I should see "You have quizzes that are due" in the "Course overview" "block"
    And I should see "Quiz 1C Future deadline" in the "Course overview" "block"
    And I should see "Quiz 1D Future deadline" in the "Course overview" "block"
    And I should see "Quiz 1E Future deadline" in the "Course overview" "block"
    And I should not see "Quiz 1A No deadline" in the "Course overview" "block"
    And I should not see "Quiz 1B Past deadline" in the "Course overview" "block"
    And I should not see "Quiz 2A Future deadline" in the "Course overview" "block"
    And I log out
    And I log in as "student2"
    And I should see "You have quizzes that are due" in the "Course overview" "block"
    And I should not see "Quiz 1C Future deadline" in the "Course overview" "block"
    And I should not see "Quiz 1D Future deadline" in the "Course overview" "block"
    And I should not see "Quiz 1E Future deadline" in the "Course overview" "block"
    And I should not see "Quiz 1A No deadline" in the "Course overview" "block"
    And I should not see "Quiz 1B Past deadline" in the "Course overview" "block"
    And I should see "Quiz 2A Future deadline" in the "Course overview" "block"

  Scenario: View my quizzes that are due and never finished
    Given I log in as "student1"
    And I follow "Course 1"
    And I follow "Quiz 1D Future deadline"
    And I press "Attempt quiz now"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I follow "Course 1"
    And I follow "Quiz 1E Future deadline"
    And I press "Attempt quiz now"
    When I am on homepage
    Then I should see "You have quizzes that are due" in the "Course overview" "block"
    And I should see "Quiz 1C Future deadline" in the "Course overview" "block"
    And I should see "Quiz 1E Future deadline" in the "Course overview" "block"
    And I should not see "Quiz 1A No deadline" in the "Course overview" "block"
    And I should not see "Quiz 1B Past deadline" in the "Course overview" "block"
    And I should not see "Quiz 1D Future deadline" in the "Course overview" "block"
    And I should not see "Quiz 2A Future deadline" in the "Course overview" "block"
