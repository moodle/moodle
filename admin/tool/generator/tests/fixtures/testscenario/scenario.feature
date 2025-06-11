Feature: Fixture to prepare scenario for testing
  Scenario: Create course content
    Given the following config values are set as admin:
      | sendcoursewelcomemessage | 0 | enrol_manual |
    And the following "course" exists:
      | fullname         | Course test |
      | shortname        | C1          |
      | category         | 0           |
      | numsections      | 3           |
      | initsections     | 1           |
    And the following "activities" exist:
      | activity | name              | intro                       | course   | idnumber | section | visible |
      | assign   | Activity sample 1 | Test assignment description | C1       | sample1  | 1       | 1       |
      | assign   | Activity sample 2 | Test assignment description | C1       | sample2  | 1       | 0       |

  @cleanup
  Scenario: clean course from fixture to prepare scenario for testing
    Given the course "Course test" is deleted

  Scenario: Create users
    Given the following "users" exist:
      | username      | firstname  | lastname | email              |
      | teachersample | Teacher    | Test1    | sample@example.com |
    And the following "course enrolments" exist:
      | user     | course   | role           |
      | teachersample | C1       | editingteacher |
    And "5" "users" exist with the following data:
      | username  | studentsample[count]             |
      | firstname | Student                          |
      | lastname  | Test[count]                      |
      | email     | studentsample[count]@example.com |
    And "5" "course enrolments" exist with the following data:
      | user   | studentsample[count] |
      | course | C1                   |
      | role   | student              |

  @cleanup
  Scenario: clean users from fixture to prepare scenario for testing
    Given the user "teachersample" is deleted
    And the user "studentsample1" is deleted
    And the user "studentsample2" is deleted
    And the user "studentsample3" is deleted
    And the user "studentsample4" is deleted
    And the user "studentsample5" is deleted
