@mod @mod_forum @forumreport @forumreport_summary
Feature: Groups report filter is not available if no groups exist
  When no groups exist
  As a teacher
  I can view the forum summary report for all users of a forum

  Scenario: Report data is available without groups filter if no groups exist
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C2     | editingteacher |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C2     | G1       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    And the following "activities" exist:
      | activity | name   | course | idnumber | groupmode |
      | forum    | forum1 | C1     | forum1C1   | 0         |
      | forum    | forum2 | C1     | forum2C1   | 0         |
      | forum    | forum1 | C2     | forum1C2   | 2         |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message    | created           |
      | teacher1 | forum1 | discussion1 | D1 message | ## 1 month ago ## |
      | teacher1 | forum1 | discussion2 | D2 message | ## 1 week ago ##  |
      | teacher1 | forum2 | discussion3 | D3 message | ## 4 days ago ##  |
      | student1 | forum1 | discussion4 | D4 message | ## 3 days ago ##  |
      | student2 | forum2 | discussion5 | D5 message | ## 2 days ago##   |
    And the following forum replies exist in course "Course 1":
      | user     | forum  | discussion  | message    | created           |
      | teacher1 | forum1 | discussion1 | D1 reply   | ## 3 weeks ago ## |
      | teacher1 | forum1 | discussion2 | D2 reply   | ## 6 days ago ##  |
      | teacher1 | forum2 | discussion3 | D3 reply   | ## 3 days ago ##  |
      | student1 | forum1 | discussion1 | D1 reply 2 | ## 2 weeks ago ## |
      | student2 | forum2 | discussion3 | D3 reply   | ## 2 days ago ##  |
    And the following forum discussions exist in course "Course 2":
      | user     | forum  | name        | message         | created          |
      | teacher1 | forum1 | discussion1 | D1 other course | ## 1 week ago ## |
      | teacher1 | forum1 | discussion2 | D2 other course | ## 4 days ago ## |
    When I am on the forum1C1 "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    Then "Groups" "button" should not exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions |
      | First name           | -3-         |
      | Teacher 1            | 2           |
      | Student 1            | 1           |
      | Student 2            | 0           |
