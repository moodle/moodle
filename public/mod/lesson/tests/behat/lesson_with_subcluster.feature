@mod @mod_lesson
Feature: In a lesson activity, students can see questions in random order and a single question drawn from a branch
  In order to create a lesson with a cluster and a subcluster
  As a teacher
  I need to add content pages and questions with cluster, branchtable and end of branchtable  and end of cluster pages

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
      | activity | name                   | course | idnumber |
      | lesson   | Lesson with subcluster | C1     | lesson1  |
    And I log in as "teacher1"

  Scenario: Lesson with subcluster
    Given the following "mod_lesson > pages" exist:
      | lesson                 | qtype        | title            | content                                                     |
      | Lesson with subcluster | content      | First page name  | First page contents                                         |
      | Lesson with subcluster | cluster      | Cluster          | Cluster                                                     |
      | Lesson with subcluster | multichoice  | Question 1       | Question from cluster                                       |
      | Lesson with subcluster | multichoice  | Question 2       | Question from cluster                                       |
      | Lesson with subcluster | content      | Second page name | Beginning of the subcluster, should not be seen by students |
      | Lesson with subcluster | multichoice  | Question 3       | Question from cluster                                       |
      | Lesson with subcluster | multichoice  | Question 4       | Question from cluster                                       |
      | Lesson with subcluster | multichoice  | Question 5       | Question from cluster                                       |
      | Lesson with subcluster | endofbranch  | End of branch    | End of branch                                               |
      | Lesson with subcluster | multichoice  | Question 6       | Question from cluster                                       |
      | Lesson with subcluster | endofcluster | End of cluster   | End of cluster                                              |
      | Lesson with subcluster | content      | Third page name  | Content page after cluster                                  |
    And the following "mod_lesson > answers" exist:
      | page             | answer           | response | jumpto                           | score |
      | First page name  | Next page        |          | Next page                        | 0     |
      | Cluster          |                  |          | Unseen question within a cluster | 0     |
      | Question 1       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 1       | Incorrect answer | Bad      | This page                        | 0     |
      | Question 2       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 2       | Incorrect answer | Bad      | This page                        | 0     |
      | Second page name | Next page        |          | Next page                        | 0     |
      | Question 3       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 3       | Incorrect answer | Bad      | This page                        | 0     |
      | Question 4       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 4       | Incorrect answer | Bad      | This page                        | 0     |
      | Question 5       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 5       | Incorrect answer | Bad      | This page                        | 0     |
      | End of branch    |                  |          | Second page name                 | 0     |
      | Question 6       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 6       | Incorrect answer | Bad      | This page                        | 0     |
      | End of cluster   |                  |          | Next page                        | 0     |
      | Third page name  | Next page        |          | Next page                        | 0     |
    When I am on the "Lesson with subcluster" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "Question from cluster"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Question from cluster"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Content page after cluster"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 4 (out of 4)."
