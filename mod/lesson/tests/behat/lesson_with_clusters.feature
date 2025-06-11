@mod @mod_lesson
Feature: In a lesson activity, students can see questions in random order
  In order to create a lesson with clusters
  As a teacher
  I need to add content pages and questions with clusters and end of clusters pages

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
      | activity | name                 | course | idnumber |
      | lesson   | Lesson with clusters | C1     | lesson1  |
    And I log in as "teacher1"

  Scenario: Lesson with two clusters
    Given the following "mod_lesson > pages" exist:
      | lesson               | qtype        | title            | content                      |
      | Lesson with clusters | content      | First page name  | First page contents          |
      | Lesson with clusters | content      | Second page name | Second page contents         |
      | Lesson with clusters | cluster      | Cluster 1        | Cluster 1                    |
      | Lesson with clusters | multichoice  | Question 1       | Question from cluster 1      |
      | Lesson with clusters | multichoice  | Question 2       | Question from cluster 1      |
      | Lesson with clusters | endofcluster | End of cluster 1 | End of cluster 1             |
      | Lesson with clusters | content      | Third page name  | Content page after cluster 1 |
      | Lesson with clusters | cluster      | Cluster 2        | Cluster 2                    |
      | Lesson with clusters | multichoice  | Question 3       | Question from cluster 2      |
      | Lesson with clusters | multichoice  | Question 4       | Question from cluster 2      |
      | Lesson with clusters | endofcluster | End of cluster 2 | End of cluster 2             |
      | Lesson with clusters | content      | Fourth page name | Content page after cluster 2 |
    And the following "mod_lesson > answers" exist:
      | page             | answer           | response | jumpto                           | score |
      | First page name  | Next page        |          | Next page                        | 0     |
      | Second page name | Previous page    |          | Previous page                    | 0     |
      | Second page name | Next page        |          | Next page                        | 0     |
      | Cluster 1        |                  |          | Unseen question within a cluster | 0     |
      | Question 1       | Correct answer   | Good     | Cluster 1                        | 1     |
      | Question 1       | Incorrect answer | Bad      | This page                        | 0     |
      | Question 2       | Correct answer   | Good     | Cluster 1                        | 1     |
      | Question 2       | Incorrect answer | Bad      | This page                        | 0     |
      | End of cluster 1 |                  |          | Next page                        | 0     |
      | Third page name  | Next page        |          | Next page                        | 0     |
      | Cluster 2        |                  |          | Unseen question within a cluster | 0     |
      | Question 3       | Correct answer   | Good     | Unseen question within a cluster | 1     |
      | Question 3       | Incorrect answer | Bad      | This page                        | 0     |
      | Question 4       | Correct answer   | Good     | Unseen question within a cluster | 1     |
      | Question 4       | Incorrect answer | Bad      | This page                        | 0     |
      | End of cluster 2 |                  |          | Next page                        | 0     |
      | Fourth page name | Next page        |          | Next page                        | 0     |
    When I am on the "Lesson with clusters" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Question from cluster 1"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster 1"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Content page after cluster 1"
    And I press "Next page"
    And I should see "Question from cluster 2"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster 2"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Content page after cluster 2"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 4 (out of 4)."
