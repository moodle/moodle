@mod @mod_choicegroup @core_completion
Feature: View activity completion information in the choicegroup activity
  In order to have visibility of choicegroup completion requirements
  As a student
  I need to be able to view my choicegroup completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Ann       | Student2 | student2@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | C1G1     |
      | Group B | C1     | C1G2     |
      | Group C | C1     | C1G3     |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking          | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And I log out

  @javascript
  Scenario: View automatic completion items for view
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Group choice" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
      | Group choice name | Choose your group        |
      | Description       | Group choice description |
      | completion        | 2                        |
      | completionview    | 1                        |
      | completionsubmit  | 0                        |
    And I set the field "availablegroups" to "Group A"
    And I press "Add Group"
    And I set the field "availablegroups" to "Group B"
    And I press "Add Group"
    And I press "Save and return to course"
    # Teacher view.
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And "Choose your group" should have the "View" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Vinnie Student1, Choose your group: Not completed" "icon" should exist in the "Vinnie Student1" "table_row"
    And "Ann Student2, Choose your group: Not completed" "icon" should exist in the "Ann Student2" "table_row"
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And I log out
    # Student 1 view.
    And I log in as "student1"
    And I am on the "Choose your group" "choicegroup activity" page logged in as student1
    And the "View" completion condition of "Choose your group" is displayed as "done"
    # Teacher view.
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And "Choose your group" should have the "View" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    Then "Vinnie Student1, Choose your group: Completed" "icon" should exist in the "Vinnie Student1" "table_row"
    And "Ann Student2, Choose your group: Not completed" "icon" should exist in the "Ann Student2" "table_row"

  @javascript
  Scenario: View automatic completion items for choose
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Group choice" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
      | Group choice name | Choose your group        |
      | Description       | Group choice description |
      | completion        | 2                        |
      | completionview    | 0                        |
      | completionsubmit  | 1                        |
    And I set the field "availablegroups" to "Group A"
    And I press "Add Group"
    And I set the field "availablegroups" to "Group B"
    And I press "Add Group"
    And I press "Save and return to course"
    # Teacher view.
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And "Choose your group" should have the "Choose a group" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Vinnie Student1, Choose your group: Not completed" "icon" should exist in the "Vinnie Student1" "table_row"
    And "Ann Student2, Choose your group: Not completed" "icon" should exist in the "Ann Student2" "table_row"
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And I log out
    # Student 1 choose.
    And I log in as "student1"
    And I am on the "Choose your group" "choicegroup activity" page logged in as student1
    And the "Choose a group" completion condition of "Choose your group" is displayed as "todo"
    And I set the field "Group A" to "1"
    And I press "Save my choice"
    And the "Choose a group" completion condition of "Choose your group" is displayed as "done"
    # Teacher view.
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And "Choose your group" should have the "Choose a group" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    Then "Vinnie Student1, Choose your group: Completed" "icon" should exist in the "Vinnie Student1" "table_row"
    And "Ann Student2, Choose your group: Not completed" "icon" should exist in the "Ann Student2" "table_row"

  @javascript
  Scenario: View automatic completion items for both choose and view
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Group choice" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
      | Group choice name | Choose your group        |
      | Description       | Group choice description |
      | completion        | 2                        |
      | completionview    | 1                        |
      | completionsubmit  | 1                        |
    And I set the field "availablegroups" to "Group A"
    And I press "Add Group"
    And I set the field "availablegroups" to "Group B"
    And I press "Add Group"
    And I press "Save and return to course"
    # Teacher view.
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And "Choose your group" should have the "View" completion condition
    And "Choose your group" should have the "Choose a group" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Vinnie Student1, Choose your group: Not completed" "icon" should exist in the "Vinnie Student1" "table_row"
    And "Ann Student2, Choose your group: Not completed" "icon" should exist in the "Ann Student2" "table_row"
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And I log out
    # Student 1 choose.
    And I log in as "student1"
    And I am on the "Choose your group" "choicegroup activity" page logged in as student1
    And the "View" completion condition of "Choose your group" is displayed as "done"
    And the "Choose a group" completion condition of "Choose your group" is displayed as "todo"
    And I set the field "Group A" to "1"
    And I press "Save my choice"
    And the "Choose a group" completion condition of "Choose your group" is displayed as "done"
    And I log out
    # Student 2 view.
    And I log in as "student2"
    And I am on the "Choose your group" "choicegroup activity" page logged in as student2
    And the "View" completion condition of "Choose your group" is displayed as "done"
    And the "Choose a group" completion condition of "Choose your group" is displayed as "todo"
    And I log out
    # Teacher view.
    And I am on the "Choose your group" "choicegroup activity" page logged in as teacher1
    And "Choose your group" should have the "Choose a group" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    Then "Vinnie Student1, Choose your group: Completed" "icon" should exist in the "Vinnie Student1" "table_row"
    And "Ann Student2, Choose your group: Not completed" "icon" should exist in the "Ann Student2" "table_row"
