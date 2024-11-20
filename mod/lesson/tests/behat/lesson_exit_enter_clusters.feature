@mod @mod_lesson
Feature: In a lesson activity, students can exit and re-enter the activity when it consists only of cluster pages
  As a student
  I need to exit and re-enter a lesson out and into clusters.

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
      | activity   | name                | course | idnumber    |
      | lesson     | Lesson with cluster | C1     | lesson1     |
    And the following "mod_lesson > pages" exist:
      | lesson              | qtype        | title                | content                   |
      | Lesson with cluster | content      | First page name      | First page contents       |
      | Lesson with cluster | cluster      | A Cluster            | A Cluster                 |
      | Lesson with cluster | multichoice  | Question 1 A Cluster | Question 1 from A cluster |
      | Lesson with cluster | multichoice  | Question 2 A Cluster | Question 2 from A cluster |
      | Lesson with cluster | multichoice  | Question 3 A Cluster | Question 3 from A cluster |
      | Lesson with cluster | endofcluster | End of A cluster     | End of A cluster          |
      | Lesson with cluster | cluster      | B Cluster            | B Cluster                 |
      | Lesson with cluster | multichoice  | Question 1 B Cluster | Question 1 from B cluster |
      | Lesson with cluster | multichoice  | Question 2 B Cluster | Question 2 from B cluster |
      | Lesson with cluster | multichoice  | Question 3 B Cluster | Question 3 from B cluster |
      | Lesson with cluster | endofcluster | End of B cluster     | End of B cluster          |
      | Lesson with cluster | cluster      | C Cluster            | C Cluster                 |
      | Lesson with cluster | multichoice  | Question 1 C Cluster | Question 1 from C cluster |
      | Lesson with cluster | multichoice  | Question 2 C Cluster | Question 2 from C cluster |
      | Lesson with cluster | multichoice  | Question 3 C Cluster | Question 3 from C cluster |
      | Lesson with cluster | endofcluster | End of C cluster     | End of C cluster          |
    And the following "mod_lesson > answers" exist:
      | page                 | answer           | response | jumpto                           | score |
      | First page name      | Next page        |          | Next page                        | 0     |
      | A Cluster            |                  |          | Unseen question within a cluster | 0     |
      | Question 1 A Cluster | Correct answer   | Good     | B Cluster                        | 1     |
      | Question 1 A Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | Question 2 A Cluster | Correct answer   | Good     | B Cluster                        | 1     |
      | Question 2 A Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | Question 3 A Cluster | Correct answer   | Good     | B Cluster                        | 1     |
      | Question 3 A Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | End of A cluster     |                  |          | Next page                        | 0     |
      | B Cluster            |                  |          | Unseen question within a cluster | 0     |
      | Question 1 B Cluster | Correct answer   | Good     | C Cluster                        | 1     |
      | Question 1 B Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | Question 2 B Cluster | Correct answer   | Good     | C Cluster                        | 1     |
      | Question 2 B Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | Question 3 B Cluster | Correct answer   | Good     | C Cluster                        | 1     |
      | Question 3 B Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | End of B cluster     |                  |          | Next page                        | 0     |
      | C Cluster            |                  |          | Unseen question within a cluster | 0     |
      | Question 1 C Cluster | Correct answer   | Good     | End of lesson                    | 1     |
      | Question 1 C Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | Question 2 C Cluster | Correct answer   | Good     | End of lesson                    | 1     |
      | Question 2 C Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | Question 3 C Cluster | Correct answer   | Good     | End of lesson                    | 1     |
      | Question 3 C Cluster | Incorrect answer | Bad      | Unseen question within a cluster | 0     |
      | End of C cluster     |                  |          | Next page                        | 0     |

  Scenario: Accessing as student to a cluster only lesson
    Given I am on the "Lesson with cluster" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Correct answer"
    And I set the following fields to these values:
      | Incorrect answer | 1 |
    And I press "Submit"
    And I should see "Bad"
    And I press "Continue"
    And I set the following fields to these values:
      | Incorrect answer | 1 |
    And I press "Submit"
    And I should see "Bad"
    And I press "Continue"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    And I should see "Incorrect answer"
    And I set the following fields to these values:
      | Incorrect answer | 1 |
    And I press "Submit"
    And I am on "Course 1" course homepage
    And I follow "Lesson with cluster"
    And I should see "Do you want to start at the last page you saw?"
    And I click on "No" "link" in the "#page-content" "css_element"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Correct answer"
    And I set the following fields to these values:
      | Correct answer | 1 |
    And I press "Submit"
    And I should see "Good"
    And I press "Continue"
    Then I should see "Correct answer"
