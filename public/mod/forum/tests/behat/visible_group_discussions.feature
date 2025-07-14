@mod @mod_forum
Feature: Posting to all groups in a visible group discussion is restricted to users with access to all groups
  In order to post to all groups in a forum with visible groups
  As a teacher
  I need to have the accessallgroups capability

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group A | C1     | G1       | 1             |
      | Group B | C1     | G2       | 1             |
      | Group C | C1     | G3       | 1             |
      | Group D | C1     | G4       | 0             |
    And the following "group members" exist:
      | user | group |
      | teacher1 | G1 |
      | teacher1 | G2 |
      | student1 | G1 |
      | student2 | G1 |
      | student2 | G2 |
      | student3 | G4 |
    And the following "activities" exist:
      | activity   | name                   | course | idnumber     | groupmode |
      | forum      | Standard forum name    | C1     | groups       | 2         |
    And the following "mod_forum > discussions" exist:
      | forum  | name             | subject          | message          | group            |
      | groups | Initial Disc ALL | Initial Disc ALL | Disc ALL content | All participants |
      | groups | Initial Disc G1  | Initial Disc G1  | Disc G1 content  | G1               |
      | groups | Initial Disc G2  | Initial Disc G2  | Disc G2 content  | G2               |
      | groups | Initial Disc G3  | Initial Disc G3  | Disc G3 content  | G3               |

  Scenario: Teacher with accessallgroups can view all groups
    When I am on the "Standard forum name" "forum activity" page logged in as teacher1
    Then the "Visible groups" select box should contain "All participants"
    And the "Visible groups" select box should contain "Group A"
    And the "Visible groups" select box should contain "Group B"
    And the "Visible groups" select box should contain "Group C"
    And the "Visible groups" select box should not contain "Group D"
    And I select "All participants" from the "Visible groups" singleselect
    And I should see "Initial Disc ALL"
    And I should see "Initial Disc G1"
    And I should see "Initial Disc G2"
    And I should see "Initial Disc G2"
    And I select "Group A" from the "Visible groups" singleselect
    And I should see "Initial Disc ALL"
    And I should see "Initial Disc G1"
    But I should not see "Initial Disc G2"
    And I should not see "Initial Disc G3"
    And I select "Group B" from the "Visible groups" singleselect
    And I should see "Initial Disc ALL"
    And I should see "Initial Disc G2"
    But I should not see "Initial Disc G1"
    And I should not see "Initial Disc G3"

  Scenario: Teacher with accessallgroups can select any group when posting
    Given I am on the "Standard forum name" "forum activity" page logged in as teacher1
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then the "Group" select box should contain "All participants"
    And the "Group" select box should contain "Group A"
    And the "Group" select box should contain "Group B"
    And the "Group" select box should contain "Group C"
    And the "Group" select box should not contain "Group D"
    And I should see "Post a copy to all groups"

  Scenario: Teacher with accessallgroups can post in groups they are a member of
    Given I am on the "Standard forum name" "forum activity" page logged in as teacher1
    And I select "Group A" from the "Visible groups" singleselect
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then I should see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Teacher 1 -> Group B  |
      | Message | Teacher 1 -> Group B  |
      # Change the group in the post form.
      | Group   | Group B               |
    And I press "Post to forum"
    And I wait to be redirected
    # We should be redirected to the group that we selected when posting.
    And the field "Visible groups" matches value "Group B"
    And I should see "Group B" in the "Teacher 1 -> Group B" "table_row"
    And I should not see "Group A" in the "Teacher 1 -> Group B" "table_row"
    And I should not see "Group C" in the "Teacher 1 -> Group B" "table_row"
    # It should also be displayed under All participants
    And I select "All participants" from the "Visible groups" singleselect
    And I should see "Group B" in the "Teacher 1 -> Group B" "table_row"
    And I should not see "Group A" in the "Teacher 1 -> Group B" "table_row"
    And I should not see "Group C" in the "Teacher 1 -> Group B" "table_row"
    # It should not be displayed in Groups A, or C.
    And I select "Group A" from the "Visible groups" singleselect
    And I should not see "Teacher 1 -> Group B"
    And I select "Group C" from the "Visible groups" singleselect
    And I should not see "Teacher 1 -> Group B"

  Scenario: Teacher with accessallgroups can post in groups they are not a member of
    Given I am on the "Standard forum name" "forum activity" page logged in as teacher1
    And I select "Group A" from the "Visible groups" singleselect
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then I should see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Teacher 1 -> Group C  |
      | Message | Teacher 1 -> Group C  |
      | Group   | Group C               |
    And I press "Post to forum"
    And I wait to be redirected
    # We should be redirected to the group that we selected when posting.
    And the field "Visible groups" matches value "Group C"
    # We redirect to the group posted in automatically.
    And I should see "Group C" in the "Teacher 1 -> Group C" "table_row"
    And I should not see "Group A" in the "Teacher 1 -> Group C" "table_row"
    And I should not see "Group B" in the "Teacher 1 -> Group C" "table_row"
    # It should also be displayed under All participants
    And I select "All participants" from the "Visible groups" singleselect
    And I should see "Group C" in the "Teacher 1 -> Group C" "table_row"
    And I should not see "Group A" in the "Teacher 1 -> Group C" "table_row"
    And I should not see "Group B" in the "Teacher 1 -> Group C" "table_row"
    # It should not be displayed in Groups A, or B.
    And I select "Group A" from the "Visible groups" singleselect
    And I should not see "Teacher 1 -> Group C"
    And I select "Group B" from the "Visible groups" singleselect
    And I should not see "Teacher 1 -> Group C"

  Scenario: Teacher with accessallgroups can post to all groups
    Given I am on the "Standard forum name" "forum activity" page logged in as teacher1
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And I set the following fields to these values:
      | Subject                   | Teacher 1 -> Post to all  |
      | Message                   | Teacher 1 -> Post to all  |
      | Post a copy to all groups | 1                       |
    And I press "Post to forum"
    And I wait to be redirected
    # Posting to all groups means that we should be redirected to the page we started from.
    And the field "Visible groups" matches value "All participants"
    And I select "Group A" from the "Visible groups" singleselect
    Then I should see "Group A" in the "Teacher 1 -> Post to all" "table_row"
    And I should not see "Group B" in the "Teacher 1 -> Post to all" "table_row"
    And I should not see "Group C" in the "Teacher 1 -> Post to all" "table_row"
    And I select "Group B" from the "Visible groups" singleselect
    And I should see "Group B" in the "Teacher 1 -> Post to all" "table_row"
    And I should not see "Group A" in the "Teacher 1 -> Post to all" "table_row"
    And I should not see "Group C" in the "Teacher 1 -> Post to all" "table_row"
    And I select "Group C" from the "Visible groups" singleselect
    And I should see "Group C" in the "Teacher 1 -> Post to all" "table_row"
    And I should not see "Group A" in the "Teacher 1 -> Post to all" "table_row"
    And I should not see "Group B" in the "Teacher 1 -> Post to all" "table_row"
    # No point testing the "All participants".

  Scenario: Students can view all groups
    When I am on the "Standard forum name" "forum activity" page logged in as student1
    Then the "Visible groups" select box should contain "All participants"
    And the "Visible groups" select box should contain "Group A"
    And the "Visible groups" select box should contain "Group B"
    And the "Visible groups" select box should contain "Group C"
    And the "Visible groups" select box should not contain "Group D"
    And I select "All participants" from the "Visible groups" singleselect
    And I should see "Initial Disc ALL"
    And I should see "Initial Disc G1"
    And I should see "Initial Disc G2"
    And I should see "Initial Disc G2"
    And I select "Group A" from the "Visible groups" singleselect
    And I should see "Initial Disc ALL"
    And I should see "Initial Disc G1"
    But I should not see "Initial Disc G2"
    And I should not see "Initial Disc G3"
    And I select "Group B" from the "Visible groups" singleselect
    And I should see "Initial Disc ALL"
    And I should see "Initial Disc G2"
    But I should not see "Initial Disc G1"
    And I should not see "Initial Disc G3"

  Scenario: Students in one group can only post in their group
    When I am on the "Standard forum name" "forum activity" page logged in as student1
    Then I should see "Group A"
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And I should see "Group A"
    And I should not see "Group B"
    And I should not see "Group C"
    And I should not see "Group D"
    And I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Student -> B |
      | Message | Student -> B |
    And I press "Post to forum"
    And I wait to be redirected
    And I should see "Group A" in the "Student -> B" "table_row"
    And I should not see "Group B" in the "Student -> B" "table_row"

  Scenario: Students in multiple group can post in all of their group individually
    When I am on the "Standard forum name" "forum activity" page logged in as student2
    And I select "Group A" from the "Visible groups" singleselect
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And the "Group" select box should not contain "All participants"
    And the "Group" select box should contain "Group A"
    And the "Group" select box should contain "Group B"
    And the "Group" select box should not contain "Group C"
    And the "Group" select box should not contain "Group D"
    And I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Student -> B  |
      | Message | Student -> B  |
      | Group   | Group B       |
    And I press "Post to forum"
    And I wait to be redirected
    # We should be redirected to the group that we selected when posting.
    And the field "Visible groups" matches value "Group B"
    And I should see "Group B" in the "Student -> B" "table_row"
    And I should not see "Group A" in the "Student -> B" "table_row"
    And I select "Group A" from the "Visible groups" singleselect
    And I should not see "Student -> B"
    # Now try posting in Group A (starting at Group B)
    And I select "Group B" from the "Visible groups" singleselect
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And the "Group" select box should not contain "All participants"
    And the "Group" select box should contain "Group A"
    And the "Group" select box should contain "Group B"
    And the "Group" select box should not contain "Group C"
    And I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Student -> A  |
      | Message | Student -> A  |
      | Group   | Group A       |
    And I press "Post to forum"
    And I wait to be redirected
    # We should be redirected to the group that we selected when posting.
    And the field "Visible groups" matches value "Group A"
    And I should see "Group A" in the "Student -> A" "table_row"
    And I should not see "Group B" in the "Student -> A" "table_row"
    And I select "Group B" from the "Visible groups" singleselect
    And I should not see "Student -> A"

  Scenario: Students in no group can see all discussions, but not post.
    Given I log in as "student4"
    And I am on "Course 1" course homepage
    When I follow "Standard forum name"
    And I select "All participants" from the "Visible groups" singleselect
    Then I should see "Initial Disc ALL"
    And I should see "Initial Disc G1"
    And I should see "Initial Disc G2"
    And I should see "Initial Disc G2"
    And I should see "You are not able to create a discussion"
    And I should not see "Add discussion topic"

  Scenario: Students in non-participation groups can see all discussions, but not post.
    Given I log in as "student3"
    And I am on "Course 1" course homepage
    When I follow "Standard forum name"
    And I select "All participants" from the "Visible groups" singleselect
    Then I should see "Initial Disc ALL"
    And I should see "Initial Disc G1"
    And I should see "Initial Disc G2"
    And I should see "Initial Disc G2"
    And I should see "You are not able to create a discussion"
    And I should not see "Add discussion topic"
