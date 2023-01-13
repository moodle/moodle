@core @core_course
Feature: Course paged mode information
  In order to split the course in parts
  As a teacher
  I need to display the proper section information in a paged mode course

  @javascript
  Scenario Outline: Section summary information for teachers and students in paged courses
    Given the following "courses" exist:
      | fullname | shortname | category | format         | numsections | coursedisplay | enablecompletion |
      | Course 1 | C1        | 0        | <courseformat> | 3           | 1             | <completion>     |
    And the following "activities" exist:
      | activity | course | name         | section | completion   |
      | chat     | C1     | Chat room    | 1       | <completion> |
      | data     | C1     | Database     | 1       | <completion> |
      | forum    | C1     | First forum  | 2       | <completion> |
      | forum    | C1     | Second forum | 2       | <completion> |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | First    | student1@example.com |
      | teacher1 | Teacher   | First    | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    When I log in as "<user>"
    And I am on "Course 1" course homepage
    Then I should see "Chat: 1" in the "#section-1" "css_element"
    And I should see "Database: 1" in the "#section-1" "css_element"
    And I should <show> "Progress:" in the "#section-1" "css_element"
    And I should see "Forums: 2" in the "#section-2" "css_element"
    And I should <show> "Progress:" in the "#section-2" "css_element"

    Examples:
      | user     | courseformat | completion | show    |
      | student1 | topics       | 0          | not see |
      | student1 | weeks        | 0          | not see |
      | student1 | topics       | 1          | see     |
      | student1 | weeks        | 1          | see     |
      | teacher1 | topics       | 0          | not see |
      | teacher1 | weeks        | 0          | not see |
      | teacher1 | topics       | 1          | see     |
      | teacher1 | weeks        | 1          | see     |

  @javascript
  Scenario Outline: Section summary information for guest in paged courses
    Given the following "courses" exist:
      | fullname | shortname | category | format         | numsections | coursedisplay | enablecompletion |
      | Course 1 | C1        | 0        | <courseformat> | 3           | 1             | <completion>     |
    And the following "activities" exist:
      | activity | course | name         | section | completion   |
      | chat     | C1     | Chat room    | 1       | <completion> |
      | data     | C1     | Database     | 1       | <completion> |
      | forum    | C1     | First forum  | 2       | <completion> |
      | forum    | C1     | Second forum | 2       | <completion> |
    And I am on the "Course 1" "enrolment methods" page logged in as admin
    And I click on "Enable" "link" in the "Guest access" "table_row"
    And I log out
    When I log in as "guest"
    And I am on "Course 1" course homepage
    Then I should see "Chat: 1" in the "#section-1" "css_element"
    And I should see "Database: 1" in the "#section-1" "css_element"
    And I should not see "Progress:" in the "#section-1" "css_element"
    And I should see "Forums: 2" in the "#section-2" "css_element"
    And I should not see "Progress:" in the "#section-2" "css_element"

    Examples:
      | courseformat | completion |
      | topics       | 0          |
      | weeks        | 0          |
      | topics       | 1          |
      | weeks        | 1          |
