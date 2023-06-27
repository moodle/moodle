@block @block_recent_activity
Feature: View structural changes in recent activity block
  In order to know when activities were changed
  As a user
  In need to see the structural changes in recent activity block

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username    | firstname | lastname | email            |
      | teacher1    | Terry1    | Teacher1 | teacher1@example.com |
      | assistant1  | Terry2    | Teacher2 | teacher2@example.com |
      | student1    | Sam1      | Student1 | student1@example.com |
      | student2    | Sam2      | Student2 | student2@example.com |
      | student3    | Sam3      | Student3 | student3@example.com |
    And the following "course enrolments" exist:
      | user        | course | role           |
      | teacher1    | C1     | editingteacher |
      | assistant1  | C1     | teacher        |
      | student1    | C1     | student        |
      | student2    | C1     | student        |
      | student3    | C1     | student        |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "groupings" exist:
      | name        | course | idnumber |
      | Grouping 1  | C1     | GG1      |
      | Grouping 2  | C1     | GG2      |
      | Grouping 3  | C1     | GG3      |
    And the following "group members" exist:
      | user        | group |
      | student1    | G1    |
      | student2    | G2    |
      | student3    | G1    |
      | student3    | G2    |
      | assistant1  | G1    |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG2      | G2    |
      | GG3      | G1    |
      | GG3      | G2    |

  Scenario: Check that Added module information is displayed respecting view capability
    Given the following "activities" exist:
      | activity | course | section | name                  | idnumber | description    | groupmode | grouping | visible |
      | forum    | C1     | 1       | ForumVisibleGroups    | forum1   | No description | 2         |          | 1       |
      | forum    | C1     | 1       | ForumSeparateGroups   | forum2   | No description | 1         |          | 1       |
      | forum    | C1     | 1       | ForumHidden           | forum3   | No description | 1         |          | 0       |
      | forum    | C1     | 1       | ForumNoGroups         | forum4   | No description | 0         |          | 1       |
      | forum    | C1     | 2       | ForumVisibleGroupsG1  | forum5   | No description | 2         | GG1      | 1       |
      | forum    | C1     | 2       | ForumSeparateGroupsG1 | forum6   | No description | 1         | GG1      | 1       |
      | forum    | C1     | 3       | ForumVisibleGroupsG2  | forum7   | No description | 2         | GG2      | 1       |
      | forum    | C1     | 3       | ForumSeparateGroupsG2 | forum8   | No description | 1         | GG2      | 1       |
    And I log in as "teacher1"

    And I am on "Course 1" course homepage
    And I click on "ForumVisibleGroupsG1" "link"
    And I click on "Edit settings" "link"
    And I set the following fields to these values:
      | Access restrictions | Grouping: Grouping 1 |
    And I press "Save and return to course"

    And I click on "ForumSeparateGroupsG1" "link"
    And I click on "Edit settings" "link"
    And I set the following fields to these values:
      | Access restrictions | Grouping: Grouping 1 |
    And I press "Save and return to course"

    And I click on "ForumVisibleGroupsG2" "link"
    And I click on "Edit settings" "link"
    And I set the following fields to these values:
      | Access restrictions | Grouping: Grouping 2 |
    And I press "Save and return to course"

    And I click on "ForumSeparateGroupsG2" "link"
    And I click on "Edit settings" "link"
    And I set the following fields to these values:
      | Access restrictions | Grouping: Grouping 2 |
    And I press "Save and return to course"

    And I am on "Course 1" course homepage with editing mode on
    When I add the "Recent activity" block

    Then I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should not see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should not see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should not see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out

    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should not see "ForumHidden" in the "Recent activity" "block"
    And I should not see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should not see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out

    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should not see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out

    # Teachers have capability to see all groups and hidden activities
    And I log in as "assistant1"
    And I am on "Course 1" course homepage
    And I should see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out

  Scenario: Updates and deletes in recent activity block
    Given the following "activity" exists:
      | activity    | forum          |
      | course      | C1             |
      | idnumber    | forum1         |
      | name        | ForumNew       |
      | description | No description |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Recent activity" block
    Then I should see "Added Forum" in the "Recent activity" "block"
    And I should see "ForumNew" in the "Recent activity" "block"
    And I log out

    # Update forum as a teacher after a second to ensure we have a new timestamp for recent activity.
    And I wait "1" seconds
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "Added Forum" in the "Recent activity" "block"
    And I should see "ForumNew" in the "Recent activity" "block"
    And I log out
    # Update forum as a teacher after a second to ensure we have a new timestamp for recent activity.
    And I wait "1" seconds

    # Update forum as a teacher
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "ForumNew"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | name | ForumUpdated |
    And I press "Save and return to course"
    And I log out
    And I wait "1" seconds
    # Student 1 already saw that forum was created, now he can see that forum was updated

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should see "Updated Forum" in the "Recent activity" "block"
    And I should see "ForumUpdated" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    # Student 2 has bigger interval and he can see one entry that forum was created but with the new name

    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I should see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should not see "Updated Forum" in the "Recent activity" "block"
    And I should see "ForumUpdated" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    # Delete forum as a teacher

    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I delete "ForumUpdated" activity
    And I run all adhoc tasks
    And I log out
    And I wait "1" seconds
    # Students 1 and 2 see that forum was deleted

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should not see "Updated Forum" in the "Recent activity" "block"
    And I should not see "ForumUpdated" in the "Recent activity" "block"
    And I should see "Deleted Forum" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    # Student 3 never knew that forum was created, so he does not see anything

    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I should not see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should not see "Updated Forum" in the "Recent activity" "block"
    And I should not see "ForumUpdated" in the "Recent activity" "block"
    And I should not see "Deleted Forum" in the "Recent activity" "block"
