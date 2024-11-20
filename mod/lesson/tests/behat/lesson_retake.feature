@mod @mod_lesson
Feature: Retake lesson activity
  In order for student to retake a lesson activity
  As a teacher
  I should be able to allow retakes

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    # Generate lesson with retakes enabled, maxgrade = 100 and handling of re-takes = use mean
    And the following "activities" exist:
      | activity   | name             | course | retake | grade[modgrade_point] | usemaxgrade |
      | lesson     | Test lesson name | C1     | 1      | 100                   | 0           |
    # Generate question pages
    And the following "mod_lesson > page" exist:
      | lesson           | qtype       | title      | content                        |
      | Test lesson name | multichoice | Question 1 | Which is not a plant?          |
      | Test lesson name | multichoice | Question 2 | Which is a plant?              |
      | Test lesson name | multichoice | Question 3 | Which is a plant and a colour? |
    # Generate question answers
    And the following "mod_lesson > answers" exist:
      | page       | answer   | jumpto    | score |
      | Question 1 | Brown    | Next page | 1     |
      | Question 1 | Lavender | Next page | 0     |
      | Question 2 | Brown    | Next page | 0     |
      | Question 2 | Lavender | Next page | 1     |
      | Question 3 | Brown    | Next page | 0     |
      | Question 3 | Lavender | Next page | 1     |

  Scenario: A student can retake a lesson
    # First attempt - all correct
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I set the following fields to these values:
      | Brown | 1 |
    And I press "Submit"
    And I set the following fields to these values:
      | Lavender | 1 |
    And I press "Submit"
    And I set the following fields to these values:
      | Lavender | 1 |
    And I press "Submit"
    # Confirm that lesson can be retaken
    When I am on the "Test lesson name" "lesson activity" page
    Then I should see "Which is not a plant?"
    # Second attempt - only 1 correct
    And I set the following fields to these values:
      | Lavender | 1 |
    And I press "Submit"
    And I set the following fields to these values:
      | Brown | 1 |
    And I press "Submit"
    And I set the following fields to these values:
      | Lavender | 1 |
    And I press "Submit"
    # Check that grade is 66.67 (mean of the 2 attempts)
    And I click on "View grades" "link"
    And "Test lesson name" row "Grade" column of "generaltable" table should contain "66.67"
    # Change handling of re-takes = use maximum
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Handling of re-takes | Use maximum |
    And I press "Save and display"
    # Confirm that lesson grade is the maximum of the 2 attempts (100)
    And I am on the "Course 1" "grades > user > View" page logged in as student1
    And "Test lesson name" row "Grade" column of "generaltable" table should contain "100.00"
    # Disable lesson retake
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Re-takes allowed | No |
    And I press "Save and display"
    # Confirm lesson cannot be retaken
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "You are not allowed to retake this lesson."
