@mod @mod_assign
Feature: Assign override deadlines
  In order to grant students deadline override
  As a teacher
  I need to create an override for the students.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam1 | Student1 | student1@example.com |
      | student2 | Sam2 | Student2 | student2@example.com |
      | student3 | Sam3 | Student3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1      |
      | student3 | G1      |
      | student2 | G2      |
      | student3 | G2      |
    And the following "activities" exist:
      | activity | name                 | intro                   | course | assignsubmission_onlinetext_enabled |
      | assign   | Test assignment name | Submit your online text | C1     | 1                                   |

  @javascript
  Scenario: Teacher can override assignment deadlines
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1              |
      | Due date       | ##1 Jan 2020 08:00## |
    And I press "Save"
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 2              |
      | Due date       | ##2 Jan 2020 08:00## |
    And I press "Save"
    And I select "User overrides" from the "jump" singleselect
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | student1                 |
      | Due date      | ##3 January 2031 08:00## |
    And I press "Save"
    And I navigate to "Submissions" in current page administration
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I should see "Thursday, 2 January 2020, 8:00"
    And I should see "Friday, 3 January 2031, 8:00"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Move down"
    And I navigate to "Submissions" in current page administration
    And "Sam3 Student3" row "Due date" column of "submissions" table should contain "Thursday, 2 January 2020, 8:00"
    And I am on "Course 1" course homepage with editing mode on
    When I open "Test assignment name" actions menu
    And I choose "Hide" in the open action menu
    And I am on the "Test assignment name" Activity page
    And I navigate to "Overrides" in current page administration
    Then I should see "* This override is inactive because the user's access to the activity is restricted. This can be due to group or role assignments, other access restrictions, or the activity being hidden."
