@mod @mod_forum @forumreport @forumreport_summary
Feature: Groups report filter is available if groups exist
  In order to retrieve targeted forum data
  As a teacher
  I can filter the forum summary report by groups of users

  Background:
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
      | Group A | C1     | G1       |
      | Group B | C1     | G2       |
      | Group C | C1     | G3       |
      | Group D | C1     | G4       |
      | Group E | C2     | G5       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | teacher1 | G2    |
      | teacher1 | G3    |
      | student1 | G3    |
    And the following "activities" exist:
      | activity | name   | course | idnumber | groupmode |
      | forum    | forum1 | C1     | c1forum1 | 1         |
      | forum    | forum2 | C1     | c1forum2 | 2         |
      | forum    | forum1 | C2     | c2forum1 | 0         |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message    | group            | created           |
      | teacher1 | forum1 | discussion1 | D1 message | G1               | ## 1 month ago ## |
      | teacher1 | forum1 | discussion2 | D2 message | G2               | ## 1 week ago ##  |
      | teacher1 | forum2 | discussion3 | D3 message | G1               | ## 6 days ago ##  |
      | teacher1 | forum2 | discussion4 | D4 message | G4               | ## 5 days ago ##  |
      | teacher1 | forum2 | discussion5 | D5 message | All participants | ## 4 days ago ##  |
      | student1 | forum1 | discussion6 | D6 message | G3               | ## 3 days ago ##  |
      | student2 | forum2 | discussion7 | D7 message | All participants | ## 2 days ago ##  |
    And the following forum replies exist in course "Course 1":
      | user     | forum  | discussion  | message    | created           |
      | teacher1 | forum1 | discussion1 | D1 reply   | ## 3 weeks ago ## |
      | teacher1 | forum2 | discussion3 | D3 reply   | ## 4 days ago ##  |
      | teacher1 | forum1 | discussion6 | D6 reply   | ## 2 days ago ##  |
      | student1 | forum1 | discussion6 | D6 reply 2 | ## 2 days ago ##  |
      | student2 | forum2 | discussion4 | D4 reply   | ## 4 days ago ##  |
      | student2 | forum2 | discussion5 | D5 reply   | ## 3 days ago ##  |
    And the following forum discussions exist in course "Course 2":
      | user     | forum  | name        | message         | created          |
      | teacher1 | forum1 | discussion1 | D1 other course | ## 1 week ago ## |
      | teacher1 | forum1 | discussion2 | D2 other course | ## 4 days ago ## |

  @javascript
  Scenario: All groups can be selected or cleared together in the groups filter, and are unchecked by default
    When I am on the "c1forum1" "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    Then "Groups" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Student 2            | 0           | 0       |
      | Teacher 1            | 2           | 2       |
    And I click on "Groups" "button"
    And "Group A" "checkbox" should exist in the "filter-groups-popover" "region"
    And "Group B" "checkbox" should exist in the "filter-groups-popover" "region"
    And "Group C" "checkbox" should exist in the "filter-groups-popover" "region"
    And "Group D" "checkbox" should exist in the "filter-groups-popover" "region"
    And "No groups" "checkbox" should exist in the "filter-groups-popover" "region"
    And "Group E" "checkbox" should not exist in the "filter-groups-popover" "region"
    And the following fields match these values:
      | Group A   | 0 |
      | Group B   | 0 |
      | Group C   | 0 |
      | Group D   | 0 |
      | No groups | 0 |
    And I click on "Select all" "button" in the "filter-groups-popover" "region"
    And the following fields match these values:
      | Group A   | 1 |
      | Group B   | 1 |
      | Group C   | 1 |
      | Group D   | 1 |
      | No groups | 1 |
    And I click on "Clear" "button" in the "filter-groups-popover" "region"
    And the following fields match these values:
      | Group A   | 0 |
      | Group B   | 0 |
      | Group C   | 0 |
      | Group D   | 0 |
      | No groups | 0 |
    And I click on "Select all" "button" in the "filter-groups-popover" "region"
    And I click on "Save" "button" in the "filter-groups-popover" "region"
    And "Groups (all)" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Student 2            | 0           | 0       |
      | Teacher 1            | 2           | 2       |

  @javascript
  Scenario: The summary report can be filtered by a subset of groups, and re-ordering the results retains the filter
    When I am on the "c1forum1" "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    Then "Groups" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Student 2            | 0           | 0       |
      | Teacher 1            | 2           | 2       |
    And I click on "Groups" "button"
    And I click on "Clear" "button" in the "filter-groups-popover" "region"
    And I click on "Group A" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Group C" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Group D" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Save" "button"
    And "Groups (3)" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Teacher 1            | 1           | 2       |
    And I should not see "Student 2"
    # Ensure re-ordering retains filter.
    And I click on "Number of discussions posted" "link"
    And "Groups (3)" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Teacher 1            | 1           | 2       |
    And I should not see "Student 2"

  @javascript
  Scenario: The summary report can be filtered as a mixture of groups and no groups
    When I am on the "c1forum2" "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    Then "Groups" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 0           | 0       |
      | Student 2            | 1           | 2       |
      | Teacher 1            | 3           | 1       |
    And I click on "Groups" "button"
    And I click on "Clear" "button" in the "filter-groups-popover" "region"
    And I click on "Group A" "checkbox" in the "filter-groups-popover" "region"
    And I click on "No groups" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Save" "button" in the "filter-groups-popover" "region"
    And "Groups (2)" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 0           | 0       |
      | Student 2            | 1           | 1       |
      | Teacher 1            | 2           | 1       |

  @javascript
  Scenario: The summary report can be filtered by no groups only
    When I am on the "c1forum2" "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    Then the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 0           | 0       |
      | Student 2            | 1           | 2       |
      | Teacher 1            | 3           | 1       |
    And I click on "Groups" "button"
    And I click on "Clear" "button" in the "filter-groups-popover" "region"
    And I click on "No groups" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Save" "button" in the "filter-groups-popover" "region"
    And "Groups (1)" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 0           | 0       |
      | Student 2            | 1           | 1       |
      | Teacher 1            | 1           | 0       |

  @javascript
  Scenario: Filtering by a group containing no users still allows the page to render
    # Log in as admin so Teacher 1 not existing on page can be confirmed.
    When I am on the "c1forum1" "forum activity" page logged in as admin
    And I navigate to "Reports" in current page administration
    Then "Groups" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Student 2            | 0           | 0       |
      | Teacher 1            | 2           | 2       |
    And I click on "Groups" "button"
    And I click on "Clear" "button" in the "filter-groups-popover" "region"
    And I click on "Group D" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Save" "button" in the "filter-groups-popover" "region"
    And "Groups (1)" "button" should exist
    And I should see "Nothing to display"
    And I should not see "Teacher 1"
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "With selected users..."
    And I should not see "Download table data as"

  @javascript
  Scenario: Course forum summary report can be filtered by group
    When I am on the "c1forum2" "forum activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I select "All forums in course" from the "Forum selected" singleselect
    And I click on "Groups" "button"
    And I click on "Clear" "button" in the "filter-groups-popover" "region"
    And I click on "Group A" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Group C" "checkbox" in the "filter-groups-popover" "region"
    And I click on "Save" "button"
    And "Groups (2)" "button" should exist
    Then the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Teacher 1            | 2           | 3       |
    And I should not see "Student 2"
    # Ensure re-ordering retains filter.
    And I click on "Number of discussions posted" "link"
    And "Groups (2)" "button" should exist
    And the following should exist in the "forumreport_summary_table" table:
    # |                      | Discussions | Replies |
      | First name / Last name | -3-         | -4-     |
      | Student 1            | 1           | 1       |
      | Teacher 1            | 2           | 3       |
    And I should not see "Student 2"
