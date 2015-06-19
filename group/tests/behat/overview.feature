@core @core_group
Feature: Group overview
  In order to view an overview of the groups
  As a teacher
  I need to visit the group overview page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student0 | Student   | 0        | student0@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
      | student5 | Student   | 5        | student5@example.com |
      | student6 | Student   | 6        | student6@example.com |
      | student7 | Student   | 7        | student7@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student0 | C1     | student        |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
      | student6 | C1     | student        |
      | student7 | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group 3 | C1     | G3       |
      | Group 4 | C1     | G4       |
    And the following "group members" exist:
      | user     | group |
      | student0 | G1    |
      | student1 | G1    |
      | student2 | G2    |
      | student3 | G3    |
      | student4 | G3    |
      | student5 | G4    |
    And the following "groupings" exist:
      | name       | course | idnumber |
      | Grouping 1 | C1     | GG1      |
      | Grouping 2 | C1     | GG2      |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG1      | G2    |
      | GG2      | G2    |
      | GG2      | G3    |

  Scenario Outline: Filter the overview in various different ways
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Groups" node in "Course administration > Users"
    And I follow "Overview"

    When I select "<grouping>" from the "Grouping" singleselect
    And I select "<group>" from the "group" singleselect

    Then the groups overview should include groups "<expectedgroups>" in groupings "<expectedgroupings>"
    And the groups overview should not include groups "<notexpectedgroups>"
    And the groups overview should include members "<expectedmembers>" in groups "<expectedmembergroups>"
    And the groups overview should not include members "<notexpectedmembers>"

    Examples:
      | grouping    | group    | expectedgroups                                        | expectedgroupings                                                                     | notexpectedgroups                   | expectedmembers                                                                        | expectedmembergroups                                                     | notexpectedmembers                                                          |
      | All         | All      | Group 1, Group 2, Group 2, Group 3, Group 4, No group | Grouping 1, Grouping 1, Grouping 2, Grouping 2, [Not in a grouping], [Not in a group] |                                     | Student 0, Student 1, Student 2, Student 3, Student 4, Student 5, Student 6, Student 7 | Group 1, Group 1, Group 2, Group 3, Group 3, Group 4, No group, No group |                                                                             |
      | Grouping 1  | All      | Group 1, Group 2                                      | Grouping 1, Grouping 1                                                                | Group 3, No group                   | Student 0, Student 1, Student 2                                                        | Group 1, Group 1, Group 2                                                | Student 3, Student 4, Student 5, Student 6, Student 7                       |
      | Grouping 2  | All      | Group 2, Group 3                                      | Grouping 2, Grouping 2                                                                | Group 1, No group                   | Student 2, Student 3, Student 4                                                        | Group 2, Group 3, Group 3                                                | Student 0, Student 1, Student 5, Student 6, Student 7                       |
      | No grouping | All      | Group 4, No group                                     | [Not in a grouping], [Not in a group]                                                 | Group 1, Group 2, Group 3           | Student 5, Student 6, Student 7                                                        | Group 4, No group, No group                                              | Student 0, Student 1, Student 2, Student 3, Student 4                       |
      | All         | Group 1  | Group 1                                               | Grouping 1                                                                            | Group 2, Group 3, Group 4, No group | Student 0, Student 1                                                                   | Group 1, Group 1                                                         | Student 2, Student 3, Student 4, Student 5, Student 6, Student 7            |
      | All         | Group 2  | Group 2, Group 2                                      | Grouping 1, Grouping 2                                                                | Group 1, Group 3, Group 4, No group | Student 2                                                                              | Group 2                                                                  | Student 0, Student 1, Student 3, Student 4, Student 5, Student 6, Student 7 |
      | All         | No group | No group                                              | [Not in a group]                                                                      | Group 1, Group 2, Group 3, Group 4  | Student 6, Student 7                                                                   | No group, No group                                                       | Student 0, Student 1, Student 2, Student 3, Student 4, Student 5            |
