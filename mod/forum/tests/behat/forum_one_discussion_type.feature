@mod @mod_forum
Feature: Each person posts one discussion forum type
  In order to limit the number of discussions in a forum
  As a teacher
  I want to set up a forum that allows each person to post one discussion topic

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
    And the following "activities" exist:
      | activity | name    | course | type     | idnumber |
      | forum    | Forum 1 | C1     | eachuser | forum1   |

  Scenario: Student can only post once on a 'Each person posts one discussion' forum
    Given the following "mod_forum > discussions" exist:
      | user     | forum  | name                | message            |
      | student1 | forum1 | student1 discussion | posted by student1 |
      | student2 | forum1 | student2 discussion | posted by student2 |
    When I am on the "Forum 1" "forum activity" page logged in as student3
    # Only 1 discussion can be added to forum.
    And I should see "This forum allows each person to start one discussion topic."
    And I click on "Add discussion topic" "link"
    And I set the following fields to these values:
      | Subject | student3 discussion |
      | Message | posted by student3  |
    And I press "Post to forum"
    # Confirm 2nd discussion topic cannot be added.
    Then "Add discussion topic" "link" should not exist
    # Confirm user can reply to other posts.
    And I click on "student1 discussion" "link"
    And I click on "Reply" "link"
    And I set the following fields to these values:
      | Message | Reply to student1 discussion |
    And I press "Post to forum"
    And I am on the "Forum 1" "forum activity" page
    And I click on "student2 discussion" "link"
    And I click on "Reply" "link"
    And I set the following fields to these values:
      | Message | Reply to student2 discussion |
    And I press "Post to forum"
    And I am on the "Forum 1" "forum activity" page
    And I click on "student3 discussion" "link"
    And I click on "Reply" "link"
    And I set the following fields to these values:
      | Message | Reply to student3 discussion |
    And I press "Post to forum"
