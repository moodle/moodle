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

  Scenario: Filter the overview in various different ways
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Groups" node in "Course administration > Users"
    And I follow "Overview"

    # Grouping All and Group All filter
    When I select "All" from the "Grouping" singleselect
    And I select "All" from the "group" singleselect
    # Following groups should exist in groupings.
    Then the group overview should include groups "Group 1, Group 2" in grouping "Grouping 1"
    And the group overview should include groups "Group 2,Group 3" in grouping "Grouping 2"
    And the group overview should include groups "Group 4" in grouping "[Not in a grouping]"
    And the group overview should include groups "No group" in grouping "[Not in a group]"
    # Following members should exit in group.
    And "Student 0" "text" should exist in the "Group 1" "table_row"
    And "Student 1" "text" should exist in the "Group 1" "table_row"
    And "Student 2" "text" should exist in the "Group 2" "table_row"
    And "Student 3" "text" should exist in the "Group 3" "table_row"
    And "Student 4" "text" should exist in the "Group 3" "table_row"
    And "Student 5" "text" should exist in the "Group 4" "table_row"
    And "Student 6" "text" should exist in the "No group" "table_row"
    And "Student 7" "text" should exist in the "No group" "table_row"

    # Grouping 1 and Group All filter
    And I select "Grouping 1" from the "Grouping" singleselect
    And I select "All" from the "group" singleselect
    # Following groups should exist in groupings.
    And the group overview should include groups "Group 1, Group 2" in grouping "Grouping 1"
    # Following groups should not exits
    And "Group 3" "table_row" should not exist
    And "No group" "table_row" should not exist
    # Following members should exit in group.
    And "Student 0" "text" should exist in the "Group 1" "table_row"
    And "Student 1" "text" should exist in the "Group 1" "table_row"
    And "Student 2" "text" should exist in the "Group 2" "table_row"
    # Following members should not exit in group.
    And I should not see "Student 3"
    And I should not see "Student 4"
    And I should not see "Student 5"
    And I should not see "Student 6"
    And I should not see "Student 7"

    # Grouping 2 and Group All filter
    And I select "Grouping 2" from the "Grouping" singleselect
    And I select "All" from the "group" singleselect
    # Following groups should exist in groupings.
    And the group overview should include groups "Group 2, Group 3" in grouping "Grouping 2"
    # Following groups should not exits
    And "Group 1" "table_row" should not exist
    And "No group" "table_row" should not exist
    # Following members should exit in group.
    And "Student 2" "text" should exist in the "Group 2" "table_row"
    And "Student 3" "text" should exist in the "Group 3" "table_row"
    And "Student 4" "text" should exist in the "Group 3" "table_row"
    # Following members should not exit in group.
    And I should not see "Student 0"
    And I should not see "Student 1"
    And I should not see "Student 5"
    And I should not see "Student 6"
    And I should not see "Student 7"

    # No grouping and Group All filter
    And I select "No grouping" from the "Grouping" singleselect
    And I select "All" from the "group" singleselect
    # Following groups should exist in groupings.
    And the group overview should include groups "Group 4" in grouping "[Not in a grouping]"
    And the group overview should include groups "No group" in grouping "[Not in a group]"
    # Following groups should not exits
    And "Group 1" "table_row" should not exist
    And "Group 2" "table_row" should not exist
    And "Group 3" "table_row" should not exist
    # Following members should exit in group.
    And "Student 5" "text" should exist in the "Group 4" "table_row"
    And "Student 6" "text" should exist in the "No group" "table_row"
    And "Student 7" "text" should exist in the "No group" "table_row"
    # Following members should not exit in group.
    And I should not see "Student 0"
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "Student 3"
    And I should not see "Student 4"

    # Grouping All and Group 1 filter
    And I select "All" from the "Grouping" singleselect
    And I select "Group 1" from the "group" singleselect
    # Following groups should exist in groupings.
    And the group overview should include groups "Group 1" in grouping "Grouping 1"
    # Following groups should not exits
    And "Group 2" "table_row" should not exist
    And "Group 3" "table_row" should not exist
    And "Group 4" "table_row" should not exist
    And "No group" "table_row" should not exist
    # Following members should exit in group.
    And "Student 0" "text" should exist in the "Group 1" "table_row"
    And "Student 1" "text" should exist in the "Group 1" "table_row"
    # Following members should not exit in group.
    And I should not see "Student 2"
    And I should not see "Student 3"
    And I should not see "Student 4"
    And I should not see "Student 5"
    And I should not see "Student 6"
    And I should not see "Student 7"

    # Grouping All and Group 2 filter
    And I select "All" from the "Grouping" singleselect
    And I select "Group 2" from the "group" singleselect
    # Following groups should exist in groupings.
    And the group overview should include groups "Group 2" in grouping "Grouping 1"
    And the group overview should include groups "Group 2" in grouping "Grouping 2"
    # Following groups should not exits
    And "Group 1" "table_row" should not exist
    And "Group 3" "table_row" should not exist
    And "Group 4" "table_row" should not exist
    And "No group" "table_row" should not exist
    # Following members should exit in group.
    And "Student 2" "text" should exist in the "Group 2" "table_row"
    # Following members should not exit in group.
    And I should not see "Student 0"
    And I should not see "Student 1"
    And I should not see "Student 3"
    And I should not see "Student 4"
    And I should not see "Student 5"
    And I should not see "Student 6"
    And I should not see "Student 7"

    # Grouping All and No group filter
    And I select "All" from the "Grouping" singleselect
    And I select "No group" from the "group" singleselect
    # Following groups should exist in groupings.
    And the group overview should include groups "No group" in grouping "[Not in a group]"
    # Following groups should not exits
    And "Group 1" "table_row" should not exist
    And "Group 2" "table_row" should not exist
    And "Group 3" "table_row" should not exist
    And "Group 4" "table_row" should not exist
    # Following members should exit in group.
    And "Student 6" "text" should exist in the "No group" "table_row"
    And "Student 7" "text" should exist in the "No group" "table_row"
    # Following members should not exit in group.
    And I should not see "Student 0"
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "Student 3"
    And I should not see "Student 4"
    And I should not see "Student 5"
