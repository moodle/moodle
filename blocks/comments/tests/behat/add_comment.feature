@block @block_comments
Feature: Add a comment to the comments block
  In order to comment on a conversation or a topic
  As a user
  In need to add comments to courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@asd.com |
      | student1 | Student | First | student1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Comments" block
    And I log out
    And I log in as "student1"
    And I follow "Course 1"

  @javascript
  Scenario: Add a comment with Javascript enabled
    When I add "I'm a comment from student1" comment to comments block
    Then I should see "I'm a comment from student1"

  Scenario: Add a comment with Javascript disabled
    When I follow "Show comments"
    And I add "I'm a comment from student1" comment to comments block
    Then I should see "I'm a comment from student1"

  @javascript
  Scenario: Test comment block pagination
    When I add "Super test comment 01" comment to comments block
    And I add "Super test comment 02" comment to comments block
    And I add "Super test comment 03" comment to comments block
    And I add "Super test comment 04" comment to comments block
    And I add "Super test comment 05" comment to comments block
    And I add "Super test comment 06" comment to comments block
    And I add "Super test comment 07" comment to comments block
    And I add "Super test comment 08" comment to comments block
    And I add "Super test comment 09" comment to comments block
    And I add "Super test comment 10" comment to comments block
    And I add "Super test comment 11" comment to comments block
    And I add "Super test comment 12" comment to comments block
    And I add "Super test comment 13" comment to comments block
    And I add "Super test comment 14" comment to comments block
    And I add "Super test comment 15" comment to comments block
    And I add "Super test comment 16" comment to comments block
    And I add "Super test comment 17" comment to comments block
    And I add "Super test comment 18" comment to comments block
    And I add "Super test comment 19" comment to comments block
    And I add "Super test comment 20" comment to comments block
    And I add "Super test comment 21" comment to comments block
    And I add "Super test comment 22" comment to comments block
    And I add "Super test comment 23" comment to comments block
    And I add "Super test comment 24" comment to comments block
    And I add "Super test comment 25" comment to comments block
    And I add "Super test comment 26" comment to comments block
    And I add "Super test comment 27" comment to comments block
    And I add "Super test comment 28" comment to comments block
    And I add "Super test comment 29" comment to comments block
    And I add "Super test comment 30" comment to comments block
    And I add "Super test comment 31" comment to comments block
    Then I should see "Super test comment 01"
    And I should see "Super test comment 31"
    And I follow "Course 1"
    And I should not see "Super test comment 01"
    And I should not see "Super test comment 02"
    And I should not see "Super test comment 16"
    And I should see "Super test comment 17"
    And I should see "Super test comment 31"
    And I should see "1" in the ".block_comments .comment-paging" "css_element"
    And I should see "2" in the ".block_comments .comment-paging" "css_element"
    And I should see "3" in the ".block_comments .comment-paging" "css_element"
    And I should not see "4" in the ".block_comments .comment-paging" "css_element"
    And I click on "2" "link" in the ".block_comments .comment-paging" "css_element"
    And I should not see "Super test comment 01"
    And I should see "Super test comment 02"
    And I should see "Super test comment 16"
    And I should not see "Super test comment 17"
    And I should not see "Super test comment 31"
    And I click on "3" "link" in the ".block_comments .comment-paging" "css_element"
    And I should see "Super test comment 01"
    And I should not see "Super test comment 02"
    And I should not see "Super test comment 16"
    And I should not see "Super test comment 17"
    And I should not see "Super test comment 31"
    And I click on "1" "link" in the ".block_comments .comment-paging" "css_element"
    And I should not see "Super test comment 01"
    And I should not see "Super test comment 02"
    And I should not see "Super test comment 16"
    And I should see "Super test comment 17"
    And I should see "Super test comment 31"
