@mod @mod_forum @forumreport @forumreport_summary
Feature: Course level forum summary report
  In order to gain an overview of students' forum activities across a course
  As a teacher
  I should be able to prepare a summary report of all forums in a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student2 | C2     | student        |
      | student3 | C2     | student        |
    And the following "activities" exist:
      | activity | name   | description | course | idnumber |
      | forum    | forum1 | C1 forum 1  | C1     | forum1   |
      | forum    | forum2 | C1 forum 2  | C1     | forum2   |
      | forum    | forum3 | C1 forum 3  | C1     | forum3   |
      | forum    | forum4 | C2 forum 1  | C2     | forum4   |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message      | created                 |
      | teacher1 | forum1 | discussion1 | Discussion 1 | ##2018-01-14 09:00:00## |
      | teacher1 | forum2 | discussion2 | Discussion 2 | ##2019-03-27 12:10:00## |
      | teacher1 | forum3 | discussion3 | Discussion 3 | ##2019-12-25 15:20:00## |
      | teacher1 | forum3 | discussion4 | Discussion 4 | ##2019-12-26 09:30:00## |
      | student1 | forum2 | discussion5 | Discussion 5 | ##2019-06-06 18:40:00## |
      | student1 | forum3 | discussion6 | Discussion 6 | ##2020-01-25 11:50:00## |
    And the following forum replies exist in course "Course 1":
      | user     | forum  | discussion  | subject | message | created                 |
      | teacher1 | forum1 | discussion1 | Re d1   | Reply 1 | ##2018-02-15 11:10:00## |
      | teacher1 | forum2 | discussion5 | Re d5   | Reply 2 | ##2019-06-09 18:20:00## |
      | teacher1 | forum2 | discussion5 | Re d5   | Reply 3 | ##2019-07-10 09:30:00## |
      | student1 | forum1 | discussion1 | Re d1   | Reply 4 | ##2018-01-25 16:40:00## |
      | student1 | forum2 | discussion2 | Re d6   | Reply 5 | ##2019-03-28 11:50:00## |
      | student1 | forum3 | discussion4 | Re d4   | Reply 6 | ##2019-12-30 20:00:00## |
    And the following forum discussions exist in course "Course 2":
      | user     | forum  | name        | message      | created                 |
      | teacher1 | forum4 | discussion7 | Discussion 7 | ##2020-01-29 15:00:00## |
      | student2 | forum4 | discussion8 | Discussion 8 | ##2020-02-02 16:00:00## |
    And the following forum replies exist in course "Course 2":
      | user     | forum  | discussion  | subject | message | created                 |
      | teacher1 | forum4 | discussion8 | Re d8   | Reply 7 | ##2020-02-03 09:45:00## |
      | student2 | forum4 | discussion7 | Re d7   | Reply 8 | ##2020-02-04 13:50:00## |

  Scenario: Course forum summary report can be viewed by teacher and contains accurate data
    When I am on the forum2 "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I should see "Export posts"
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |                                 |                                |
      | First name / Surname | -3-         | -4-     | Earliest post                   | Most recent post               |
      | Student 1            | 1           | 1       | Thursday, 28 March 2019, 11:50  | Thursday, 6 June 2019, 6:40    |
      | Student 2            | 0           | 0       | -                               | -                              |
      | Teacher 1            | 1           | 2       | Wednesday, 27 March 2019, 12:10 | Wednesday, 10 July 2019, 9:30  |
    And the following should not exist in the "forumreport_summary_table" table:
      | First name / Surname |
      | Student 3            |
    And the "Forum selected" select box should contain "All forums in course"
    And the "Forum selected" select box should contain "forum1"
    And the "Forum selected" select box should contain "forum2"
    And the "Forum selected" select box should contain "forum3"
    And the "Forum selected" select box should not contain "forum4"
    Then I select "All forums in course" from the "Forum selected" singleselect
    And I should not see "Export posts"
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |                                 |                                  |
      | First name / Surname | -3-         | -4-     | Earliest post                   | Most recent post                 |
      | Student 1            | 2           | 3       | Thursday, 25 January 2018, 4:40 | Saturday, 25 January 2020, 11:50 |
      | Student 2            | 0           | 0       | -                               | -                                |
      | Teacher 1            | 4           | 3       | Sunday, 14 January 2018, 9:00   | Thursday, 26 December 2019, 9:30 |
    And the following should not exist in the "forumreport_summary_table" table:
      | First name / Surname |
      | Student 3            |

  Scenario: Course forum summary report correctly formats forum activity names
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "activity" exists:
      | activity | forum |
      | course   | C1    |
      | name     | <span class="multilang" lang="en">F-Eng</span><span class="multilang" lang="de">F-Ger</span> |
    When I am on the "C1" "Course" page logged in as "teacher1"
    And I follow "F-Eng"
    And I navigate to "Reports" in current page administration
    Then the "Forum selected" select box should contain "F-Eng"

  Scenario: Students given the view capability can view their own course report data
    Given the following "permission overrides" exist:
      | capability               | permission | role    | contextlevel | reference |
      | forumreport/summary:view | Allow      | student | Course       | C1        |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "forum1"
    And I navigate to "Reports" in current page administration
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |                                 |                                 |
      | First name / Surname | -2-         | -3-     | Earliest post                   | Most recent post                |
      | Student 1            | 0           | 1       | Thursday, 25 January 2018, 4:40 | Thursday, 25 January 2018, 4:40 |
    And the following should not exist in the "forumreport_summary_table" table:
      | First name / Surname |
      | Student 2            |
      | Student 3            |
      | Teacher 1            |
    And the "Forum selected" select box should contain "All forums in course"
    And the "Forum selected" select box should contain "forum1"
    And the "Forum selected" select box should contain "forum2"
    And the "Forum selected" select box should contain "forum3"
    And the "Forum selected" select box should not contain "forum4"
    Then I select "All forums in course" from the "Forum selected" singleselect
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |                                 |                                  |
      | First name / Surname | -2-         | -3-     | Earliest post                   | Most recent post                 |
      | Student 1            | 2           | 3       | Thursday, 25 January 2018, 4:40 | Saturday, 25 January 2020, 11:50 |
    And the following should not exist in the "forumreport_summary_table" table:
      | First name / Surname |
      | Student 2            |
      | Student 3            |
      | Teacher 1            |
