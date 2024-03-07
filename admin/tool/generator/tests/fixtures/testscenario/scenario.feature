Feature: Prepare scenario for testing
  Scenario: Create course content
    Given the following "course" exists:
      | fullname         | Course test |
      | shortname        | C1          |
      | category         | 0           |
      | numsections      | 3           |
      | initsections     | 1           |
    And the following "activities" exist:
      | activity | name              | intro                       | course   | idnumber | section | visible |
      | assign   | Activity sample 1 | Test assignment description | C1       | sample1  | 1       | 1       |
      | assign   | Activity sample 2 | Test assignment description | C1       | sample2  | 1       | 0       |
  Scenario: Create users
    Given the following "users" exist:
      | username | firstname  | lastname | email              |
      | teacher1 | Teacher    | Test1    | sample@example.com |
    And the following "course enrolments" exist:
      | user     | course   | role           |
      | teacher1 | C1       | editingteacher |
    And "5" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | Test[count]                |
      | email     | student[count]@example.com |
    And "5" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   | student        |
