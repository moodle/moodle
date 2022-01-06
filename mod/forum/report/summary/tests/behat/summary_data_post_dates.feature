@mod @mod_forum @forumreport @forumreport_summary
Feature: Post date columns data available
  In order to determine users' earliest and most recent forum posts
  As a teacher
  I need to view that data in the forum summary report

  Scenario: Add posts and view accurate summary report
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
    And the following "activities" exist:
      | activity | name   | description     | course | idnumber |
      | forum    | forum1 | C1 first forum  | C1     | forum1   |
      | forum    | forum2 | C1 second forum | C1     | forum2   |
      | forum    | forum1 | C2 first forum  | C2     | forum1   |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message         | created                 |
      | teacher1 | forum1 | discussion1 | t1 earliest     | ##2018-01-02 09:00:00## |
      | teacher1 | forum1 | discussion2 | t1 between      | ##2018-03-27 10:00:00## |
      | teacher1 | forum2 | discussion3 | t1 other forum  | ##2018-01-01 11:00:00## |
      | student1 | forum1 | discussion4 | s1 latest       | ##2019-03-27 13:00:00## |
      | student2 | forum2 | discussion5 | s2 other forum  | ##2018-03-27 09:00:00## |
    And the following forum replies exist in course "Course 1":
      | user     | forum  | discussion  | message         | created                 |
      | teacher1 | forum1 | discussion1 | t1 between      | ##2018-01-02 10:30:00## |
      | teacher1 | forum1 | discussion2 | t1 latest       | ##2019-09-01 07:00:00## |
      | teacher1 | forum2 | discussion3 | t1 other forum  | ##2019-09-12 08:00:00## |
      | student1 | forum1 | discussion1 | s1 earliest     | ##2019-03-27 04:00:00## |
      | student2 | forum2 | discussion3 | s2 other forum  | ##2018-03-27 10:00:00## |
    And the following forum discussions exist in course "Course 2":
      | user     | forum  | name        | message         | created                 |
      | teacher1 | forum1 | discussion1 | t1 other course | ##2017-01-01 03:00:00## |
      | teacher1 | forum1 | discussion2 | t1 other course | ##2019-09-13 23:59:00## |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "forum1"
    And I navigate to "Forum summary report" in current page administration
    Then "Teacher 1" row "Earliest post" column of "forumreport_summary_table" table should contain "Tuesday, 2 January 2018, 9:00"
    Then "Teacher 1" row "Most recent post" column of "forumreport_summary_table" table should contain "Sunday, 1 September 2019, 7:00"
    Then "Student 1" row "Earliest post" column of "forumreport_summary_table" table should contain "Wednesday, 27 March 2019, 4:00"
    Then "Student 1" row "Most recent post" column of "forumreport_summary_table" table should contain "Wednesday, 27 March 2019, 1:00"
    Then "Student 2" row "Earliest post" column of "forumreport_summary_table" table should contain "-"
    Then "Student 2" row "Most recent post" column of "forumreport_summary_table" table should contain "-"
