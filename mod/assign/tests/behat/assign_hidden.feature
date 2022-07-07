@mod @mod_assign @javascript
Feature: When a Teacher hides an assignment from view for students it should consistently indicate it is hidden.

  Scenario: Grade multiple students on one page
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test hidden assignment |
    And I open "Test hidden assignment" actions menu
    And I choose "Hide" in the open action menu
    And I follow "Test hidden assignment"
    And I should see "Test hidden assignment"
    And I should see "Yes" in the "Hidden from students" "table_row"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "2" and I fill the form with:
      | Assignment name | Test visible assignment |
    And I follow "Test visible assignment"
    And I should see "Test visible assignment"
    And I should see "No" in the "Hidden from students" "table_row"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Test hidden assignment"
    And I should see "Test visible assignment"
