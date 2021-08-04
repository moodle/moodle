@mod @mod_forum @forumreport @forumreport_summary
Feature: Report relevant content availability
  In order to view the appropriate report content
  As a teacher or student
  I need to have the associated capabilities

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | name   | description | course | idnumber |
      | forum    | forum1 | C1 forum    | C1     | forum1   |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message   | attachments | inlineattachments |
      | teacher1 | forum1 | discussion1 | message 1 |             |                   |
      | student1 | forum1 | discussion2 | message 3 | att1.jpg    | in1.jpg           |
      | student2 | forum1 | discussion3 | message 4 | att3.jpg    |                   |
    And the following forum replies exist in course "Course 1":
      | user     | forum  | discussion  | message | attachments        | inlineattachments |
      | student1 | forum1 | discussion1 | reply1  | att4.jpg, att5.jpg | in2.jpg           |
      | student2 | forum1 | discussion2 | reply2  |                    |                   |

  @javascript
  Scenario: Teachers can access report data about other users by default
    Given I am on the "forum1" "forum activity" page logged in as teacher1
    And I navigate to "Forum summary report" in current page administration
    Then the following should exist in the "forumreport_summary_table" table:
      | -2-       | -3- | -4- | -5- | -6- | -7- | -8- |
      | Student 1 | 1   | 1   | 5   | 0   | 3   | 14  |
      | Student 2 | 1   | 1   | 1   | 0   | 3   | 14  |
      | Teacher 1 | 1   | 0   | 0   | 1   | 2   | 8   |
    And "select-all-users" "checkbox" should be visible
    And "First name" "link" should be visible
    And "Surname" "link" should be visible
    And "Number of discussions posted" "link" should be visible
    And "Number of replies posted" "link" should be visible
    And "Number of attachments" "link" should be visible
    And "Number of views" "link" should be visible
    And "Word count" "link" should be visible
    And "Character count" "link" should be visible
    And "Earliest post" "link" should be visible
    And "Most recent post" "link" should be visible
    And I should see "Export posts"
    And "Export posts" "link" should not exist

  Scenario: Students cannot access the summary report by default
    Given I am on the "forum1" "forum activity" page logged in as student1
    Then "Forum summary report" "link" should not exist in current page administration

  @javascript
  Scenario: Students given the view capability can only view their own report data
    Given the following "permission overrides" exist:
      | capability               | permission | role    | contextlevel | reference |
      | forumreport/summary:view | Allow      | student | Course       | C1        |
    When I am on the "forum1" "forum activity" page logged in as student1
    And I navigate to "Forum summary report" in current page administration
    Then the following should exist in the "forumreport_summary_table" table:
      | -1-       | -2- | -3- | -4- | -5- | -6- | -7- |
      | Student 1 | 1   | 1   | 5   | 1   | 3   | 14  |
    And the following should not exist in the "forumreport_summary_table" table:
      | -1-       |
      | Student 2 |
      | Teacher 1 |
    And "select-all-users" "checkbox" should not exist
    And "First name" "link" should be visible
    And "Surname" "link" should be visible
    And "Number of discussions posted" "link" should be visible
    And "Number of replies posted" "link" should be visible
    And "Number of attachments" "link" should be visible
    And "Number of views" "link" should be visible
    And "Word count" "link" should be visible
    And "Character count" "link" should be visible
    And "Earliest post" "link" should be visible
    And "Most recent post" "link" should be visible
    And I should not see "Export posts"
