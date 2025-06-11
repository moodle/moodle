@mod @mod_lesson
Feature: In a lesson activity, teacher can edit a cluster page
  In order to modify an existing lesson and change navigation
  As a teacher
  I need to edit cluster pages in the lesson

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
      | activity | name                | course | idnumber |
      | lesson   | Lesson with cluster | C1     | lesson1  |
    And the following "mod_lesson > pages" exist:
      | lesson              | qtype        | title            | content                    |
      | Lesson with cluster | content      | First page name  | First page contents        |
      | Lesson with cluster | cluster      | Cluster          | Cluster                    |
      | Lesson with cluster | multichoice  | Question 1       | Question from cluster      |
      | Lesson with cluster | multichoice  | Question 2       | Question from cluster      |
      | Lesson with cluster | endofcluster | End of cluster   | End of cluster             |
      | Lesson with cluster | content      | Second page name | Content page after cluster |
    And the following "mod_lesson > answers" exist:
      | page             | answer           | response | jumpto                           | score |
      | First page name  | Next page        |          | Next page                        | 0     |
      | Cluster          |                  |          | Unseen question within a cluster | 0     |
      | Question 1       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 1       | Incorrect answer | Bad      | This page                        | 0     |
      | Question 2       | Correct answer   | Good     | Cluster                          | 1     |
      | Question 2       | Incorrect answer | Bad      | This page                        | 0     |
      | End of cluster   |                  |          | Next page                        | 0     |
      | Second page name | Next page        |          | Next page                        | 0     |

  Scenario: Edit lesson cluster page
    Given I am on the "Lesson with cluster" "lesson activity" page logged in as teacher1
    And I press "Edit lesson"
    And I select edit type "Expanded"
    And I click on "//th[normalize-space(.)='Cluster']/descendant::a[3]" "xpath_element"
    When I set the following fields to these values:
      | Page title | Modified name |
      | Page contents | Modified contents |
    And I press "Save page"
    Then I should see "Modified name"
    And I click on "//th[normalize-space(.)='Modified name']/descendant::a[3]" "xpath_element"
    And I should see "Unseen question within a cluster"
    And I press "Cancel"
    And I click on "//th[normalize-space(.)='End of cluster']/descendant::a[3]" "xpath_element"
    And I set the following fields to these values:
      | Page title | Modified end |
      | Page contents | Modified end contents |
      | id_jumpto_0 | Second page name |
    And I press "Save page"
    And I should see "Modified end"
    And I am on the "Lesson with cluster" "lesson activity" page logged in as student1
    And I should see "First page contents"
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
    And I should see "Content page after cluster"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 2 (out of 2)."
