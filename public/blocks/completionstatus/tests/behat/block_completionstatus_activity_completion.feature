@block @block_completionstatus @core_completion
Feature: Enable Block Completion in a course using activity completion
  In order to view the completion block in a course
  As a teacher
  I can add completion block to a course and set up activity completion

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | T1       |
      | student1 | Student   | 1        | student1@example.com | S1       |
      | student2 | Student   | 2        | student2@example.com | S2       |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity   | course | idnumber   | name             | gradepass | completion   | completionview | completionusegrade | completionpassgrade |
      | page       | C1     | page1      | Test page name   |           | 2            | 1              | 0                  | 0                   |
      | assign     | C1     | assign1    | Test assign name | 50        | 2            | 0              | 1                  | 1                   |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | completionstatus | Course       | C1        | course-view-*   | side-pre      |
    And I am on the "Course 1" course page logged in as teacher1
    And I change window size to "large"
    And I turn editing mode on

  Scenario: Completion status block when student has not started any activities
    Given I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test page name | 1 |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "0 of 1" in the "Activity completion" "table_row"

  Scenario: Completion status block when student has completed a page
    Given I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test page name | 1 |
    And I press "Save changes"
    When I am on the "Test page name" "page activity" page logged in as student1
    And I am on "Course 1" course homepage
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "1 of 1" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "Yes" in the "Activity completion" "table_row"

  Scenario: Completion status block with items with passing grade
    Given I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test assign name | 1 |
    And I press "Save changes"
    And the following "grade grades" exist:
      | gradeitem           | user     | grade |
      | Test assign name    | student1 | 53    |
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "1 of 1" in the "Activity completion" "table_row"
    And I trigger cron
    And I am on "Course 1" course homepage
    And I follow "More details"
    And I should see "Achieving grade, Achieving passing grade" in the "Activity completion" "table_row"
    And I should see "Yes" in the "Activity completion" "table_row"

  Scenario: Completion status block with items with failing grade
    Given the following "grade grades" exist:
      | gradeitem           | user     | grade |
      | Test assign name    | student1 | 49    |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test assign name | 1 |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "0 of 1" in the "Activity completion" "table_row"
    And I trigger cron
    And I am on "Course 1" course homepage
    And I follow "More details"
    And I should see "Achieving grade, Achieving passing grade" in the "Activity completion" "table_row"
    And I should see "No" in the "Activity completion" "table_row"

  @javascript
  Scenario: Student visibility respects combined activity and section restrictions with progressive completion
    Given the following "activities" exist:
      | activity | name   | intro                  | course | idnumber | section | visible | completion | completionview |
      | page     | task A | page description       | C1     | page1    | 0       | 1       | 2          | 1              |
      | page     | task B | page description       | C1     | page2    | 1       | 1       | 2          | 1              |
      | assign   | task C | assignment description | C1     | assign1  | 1       | 1       | 2          | 1              |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Page - task A       | 1 |
      | Page - task B       | 1 |
      | Assignment - task C | 1 |
    And I press "Save changes"
    # Add conditionally visible restriction (open eye) to section 1 requiring task A completion.
    And I turn editing mode on
    And I edit the section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I press "Save changes"
    # Add conditionally hidden restriction (closed eye) to task C requiring task A completion.
    And I am on the "task C" "assign activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Item name displayed with access restriction information if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "task A"
    And I press "Save and return to course"
    And I log out
    # Initial state: Only unrestricted visible activities appear.
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Not yet started"
    And I should see "0 of 1" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "task A" in the "criteriastatus" "table"
    And I should not see "task B" in the "criteriastatus" "table"
    And I should not see "task C" in the "criteriastatus" "table"
    # After completing task A: Section 2 activities become visible.
    And I click on "task A" "link"
    And I am on the "Course 1" course page logged in as student1
    And I should see "Status: In progress"
    And I should see "1 of 3" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "task A" in the "criteriastatus" "table"
    And I should see "task B" in the "criteriastatus" "table"
    And I should see "task C" in the "criteriastatus" "table"

  @javascript
  Scenario: Student completion view shows only accessible activities considering all activity restrictions
    Given the following "activities" exist:
      | activity | name   | intro                  | course | idnumber | section | visible | completion | completionview |
      | page     | task A | page description       | C1     | page1    | 0       | 1       | 2          | 1              |
      | page     | task B | page  description      | C1     | assign1  | 1       | 1       | 2          | 1              |
      | assign   | task C | assignment description | C1     | assign2  | 1       | 1       | 2          | 1              |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    # Set completion of the activities.
    And I set the following fields to these values:
      | Page - task A       | 1 |
      | Page - task B       | 1 |
      | Assignment - task C | 1 |
    And I press "Save changes"
    # Add conditionally visible restriction (open eye) to "task B".
    And I am on the "task B" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I press "Save and return to course"
    # Add conditionally hidden restriction (closed eye) to "task C".
    And I am on the "task C" "assign activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Item name displayed with access restriction information if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "task A"
    And I press "Save and return to course"
    When I am on the "Course 1" course page logged in as student1
    And I should see "Status: Not yet started"
    And I should see "0 of 2" in the "Activity completion" "table_row"
    And I follow "More details"
    Then I should see "task A" in the "criteriastatus" "table"
    And I should see "task B" in the "criteriastatus" "table"
    And I should not see "task C" in the "criteriastatus" "table"
    And I click on "task A" "link"
    # Complete task A to make task C visible.
    And I am on the "Course 1" course page logged in as student1
    And I should see "Status: In progress"
    And I should see "1 of 3" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "task A" in the "criteriastatus" "table"
    And I should see "task B" in the "criteriastatus" "table"
    And I should see "task C" in the "criteriastatus" "table"

  @javascript
  Scenario: Hidden activities do not appear in the completion status block
    Given the following "activities" exist:
      | activity | name   | intro            | course | idnumber    | section | visible | completion | completionview |
      | page     | task A | page description | C1     | page1       | 0       | 1       | 2          | 1              |
      | page     | task B | page description | C1     | page2       | 0       | 0       | 2          | 1              |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    # Set completion of the activities.
    And I set the following fields to these values:
      | Page - task A | 1 |
      | Page - task B | 1 |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as student1
    And I should see "Status: Not yet started"
    And I should see "0 of 1" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "task A" in the "criteriastatus" "table"
    Then I should not see "task B" in the "criteriastatus" "table"

  @javascript
  Scenario: Activities in the hidden section do not appear in the completion status block
    Given the following "activities" exist:
      | activity | name   | intro            | course | idnumber | section | visible | completion | completionview |
      | page     | task A | page description | C1     | page1    | 0       | 1       | 2          | 1              |
      | page     | task B | page description | C1     | page2    | 1       | 1       | 2          | 1              |
      | assign   | task C | page description | C1     | page3    | 2       | 1       | 2          | 1              |
      | assign   | task D | page description | C1     | page4    | 3       | 1       | 2          | 1              |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    # Set completion of the activities.
    And I set the following fields to these values:
      | Page - task A       | 1 |
      | Page - task B       | 1 |
      | Assignment - task C | 1 |
      | Assignment - task D | 1 |
    And I press "Save changes"
    # Hide section 1 to make sure book activities are not visible to the student.
    And I hide section "1"
    # Add conditionally visible restriction to section 2.
    And I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I press "Save changes"
    # Add conditionally hidden restriction to section 3.
    And I am on the "Course 1" course page
    And I edit the section "3"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I click on "Item name displayed with access restriction information if student doesn't meet this condition • Click to hide" "link"
    And I press "Save changes"
    And I log out
    When I am on the "Course 1" course page logged in as student1
    And I should see "Status: Not yet started"
    And I should see "0 of 1" in the "Activity completion" "table_row"
    And I follow "More details"
    Then I should see "task A" in the "criteriastatus" "table"
    And I should not see "task B" in the "criteriastatus" "table"
    And I should not see "task C" in the "criteriastatus" "table"
    And I should not see "task D" in the "criteriastatus" "table"
    # Complete task A to make other activities visible.
    And I click on "task A" "link"
    And I am on the "Course 1" course page logged in as student1
    And I should see "Status: In progress"
    And I should see "1 of 3" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "task A" in the "criteriastatus" "table"
    And I should not see "task B" in the "criteriastatus" "table"
    And I should see "task C" in the "criteriastatus" "table"
    And I should see "task D" in the "criteriastatus" "table"

  @javascript
  Scenario: Activities with disabled completion tracking are omitted from the completion view
    Given the following "activities" exist:
      | activity | name   | intro            | course | idnumber | section | visible | completion | completionview |
      | page     | task A | page description | C1     | page1    | 0       | 1       | 2          | 1              |
      | page     | task B | page description | C1     | page2    | 1       | 1       | 2          | 1              |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    # Set completion of the activities.
    And I set the following fields to these values:
      | Page - task A | 1 |
      | Page - task B | 1 |
    And I press "Save changes"
    # Disable completion tracking for "task A".
    And I am on the "task A" "page activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | completion | 0 |
    And I press "Save and return to course"
    When I am on the "Course 1" course page logged in as student1
    And I should see "Status: Not yet started"
    And I should see "0 of 1" in the "Activity completion" "table_row"
    And I follow "More details"
    Then I should not see "task A" in the "criteriastatus" "table"
    And I should see "task B" in the "criteriastatus" "table"

  @javascript
  Scenario: Activities with group or grouping restrictions are omitted from the completion view
    Given the following "activities" exist:
      | activity | name   | intro            | course | idnumber | section | visible | completion | completionview |
      | page     | task A | page description | C1     | page1    | 0       | 1       | 2          | 1              |
      | page     | task B | page description | C1     | page2    | 1       | 1       | 2          | 1              |
      | page     | task C | page description | C1     | page3    | 2       | 1       | 2          | 1              |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    # Set completion of the activities.
    And I set the following fields to these values:
      | Page - task A | 1 |
      | Page - task B | 1 |
      | Page - task C | 1 |
    And I press "Save changes"
    # Add groups and groupings.
    And the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | GI1      |
      | G2       | C1     | GI2      |
    And the following "groupings" exist:
      | name       | course | idnumber |
      | Grouping 1 | C1     | GG1      |
      | Grouping 2 | C1     | GG2      |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | GI1    |
      | GG2      | GI2    |
    # Add students to groups.
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
      | student2 | GI2   |
    # Add group restriction to "task B".
    And I am on the "task B" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G1"
    And I click on "Save and return to course" "button"
    # Add Grouping and 'Activity or resource' restriction to "task C".
    And I am on the "task C" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grouping" "button" in the "Add restriction..." "dialogue"
    And I set the field "Grouping" to "Grouping 2"
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I click on "Save and return to course" "button"
    When I am on the "Course 1" course page logged in as student1
    And I should see "Status: Not yet started"
    And I should see "0 of 2" in the "Activity completion" "table_row"
    And I follow "More details"
    Then I should see "task A" in the "criteriastatus" "table"
    And I should see "task B" in the "criteriastatus" "table"
    And I should not see "task C" in the "criteriastatus" "table"
    And I am on the "Course 1" course page logged in as student2
    And I should see "Status: Not yet started"
    And I should see "0 of 2" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "task A" in the "criteriastatus" "table"
    And I should not see "task B" in the "criteriastatus" "table"
    And I should see "task C" in the "criteriastatus" "table"
