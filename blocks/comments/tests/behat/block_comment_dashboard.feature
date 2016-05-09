@block @block_comments
Feature: Enable Block comments on the dashboard and view comments
  In order to enable the comments block on a the dashboard
  As a teacher
  I can add the comments block to my dashboard

  Scenario: Add the comments block on the dashboard and add comments
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
    And I log in as "teacher1"
    And I press "Customise this page"
    And I add the "Comment" block
    And I follow "Show comments"
    When I add "I'm a comment from the teacher" comment to comments block
    Then I should see "I'm a comment from the teacher"
