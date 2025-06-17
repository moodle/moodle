@mod @mod_lesson
Feature: In a lesson activity, students can navigate through a series of pages in various ways depending upon their answers to questions
  In order to create a lesson with conditional paths
  As a teacher
  I need to add pages and questions with links between them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name                                                                                                       | course | idnumber |
      | lesson   | Test lesson name                                                                                           | C1     | lesson1  |
      | page     | <span class="multilang" lang="en">A page (EN)</span><span class="multilang" lang="eu">Orri bat (EU)</span> | C1     | PAGE1    |

  Scenario: Student navigation with pages and questions
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title                 | content              |
      | Test lesson name | content | First page name       | First page contents  |
      | Test lesson name | content | Second page name      | Second page contents |
      | Test lesson name | numeric | Hardest question ever | 1 + 1?               |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response         | jumpto           | score |
      | First page name       | Next page     |                  | Next page        | 0     |
      | Second page name      | Previous page |                  | Previous page    | 0     |
      | Second page name      | Next page     |                  | Next page        | 0     |
      | Hardest question ever | 2             | Correct answer   | End of lesson    | 1     |
      | Hardest question ever | 1             | Incorrect answer | Second page name | 0     |
    When I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Link to next activity" to "Page - A page (EN)"
    And I press "Save and display"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I should not see "First page contents"
    And I press "Previous page"
    And I should see "First page contents"
    And I should not see "Second page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Second page name"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "You have reached the maximum number of tries for this question. The lesson will now move to the next page."
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."
    And I should see "Go to A page (EN)"
    And I should not see "Orri bat (EU)"

  Scenario: Student reattempts a question until out of attempts
    Given the following "mod_lesson > page" exist:
      | lesson           | qtype     | title         | content      |
      | Test lesson name | truefalse | Test question | Test content |
    And the following "mod_lesson > answers" exist:
      | page          | answer | jumpto    | score |
      | Test question | right  | Next page | 1     |
      | Test question | wrong  | This page | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_review | Yes |
      | id_maxattempts | 3 |
    And I press "Save and display"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "You have 2 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "You have 1 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should not see "Yes, I'd like to try again"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"

  Scenario: Student reattempts a question until out of attempts with specific jumps
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title           | content        |
      | Test lesson name | truefalse | Test question   | Test content 1 |
      | Test lesson name | truefalse | Test question 2 | Test content 2 |
    And the following "mod_lesson > answers" exist:
      | page            | answer | jumpto        | score |
      | Test question   | right  | Next page     | 1     |
      | Test question   | wrong  | This page     | 0     |
      | Test question 2 | right  | Test question | 1     |
      | Test question 2 | wrong  | Test question | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_review | Yes |
      | id_maxattempts | 3 |
    And I press "Save and display"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "Test content 1"
    And I set the following fields to these values:
      | right | 1 |
    And I press "Submit"
    And I should see "Test content 2"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "You have 2 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content 2"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "You have 1 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content 2"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should not see "Yes, I'd like to try again"
    And I press "Continue"
    And I should see "Test content 1"

  Scenario: Student should not see remaining attempts notification if maximum number of attempts is set to unlimited
    Given the following "mod_lesson > page" exist:
      | lesson           | qtype     | title         | content      |
      | Test lesson name | truefalse | Test question | Test content |
    And the following "mod_lesson > answers" exist:
      | page          | answer | jumpto    | score |
      | Test question | right  | Next page | 1     |
      | Test question | wrong  | This page | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_review | Yes |
      | id_maxattempts | 0 |
    And I press "Save and display"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should not see "attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content"
    And I set the following fields to these values:
      | right | 1 |
    And I press "Submit"
    And I should not see "Yes, I'd like to try again"
    And I should see "Congratulations - end of lesson reached"
