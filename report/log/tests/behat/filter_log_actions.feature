@report @report_log
Feature: In a report, admin can filter log data by action
  In order to filter log data by action
  As an admin
  I need to view the logs and apply a filter

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    # Create Action.
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment 1 |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
    And I am on "Course 1" course homepage
    # View Action.
    And I follow "Test assignment 1"
    # Update Action.
    And I navigate to "Edit settings" in current page administration
    And I press "Save and return to course"
    # Delete Action.
    And I delete "Test assignment 1" activity
    And I log out

  Scenario: View only create actions.
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "menumodaction" to "Create"
    And I press "Get these logs"
    Then I should see "Course module created"
    And I should not see "Course module updated"
    And I should not see "The status of the submission has been viewed."
    And I should not see "Course module deleted"

  Scenario: View only update actions.
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "menumodaction" to "Update"
    And I press "Get these logs"
    Then I should see "Course module updated"
    And I should not see "Course module created"
    And I should not see "The status of the submission has been viewed."
    And I should not see "Course module deleted"

  Scenario: View only view actions.
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "menumodaction" to "View"
    And I press "Get these logs"
    Then I should see "The status of the submission has been viewed."
    And I should not see "Course module created"
    And I should not see "Course module updated"
    And I should not see "Course module deleted"

  Scenario: View only delete actions.
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "menumodaction" to "Delete"
    And I press "Get these logs"
    Then I should see "Course module deleted"
    And I should not see "Course module created"
    And I should not see "Course module updated"
    And I should not see "The status of the submission has been viewed."

  Scenario: View only changes.
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "menumodaction" to "All changes"
    And I press "Get these logs"
    Then I should see "Course module deleted"
    And I should see "Course module created"
    And I should see "Course module updated"
    And I should not see "The status of the submission has been viewed."

  Scenario: View all actions.
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "menumodaction" to "All actions"
    And I press "Get these logs"
    Then I should see "Course module deleted"
    And I should see "Course module created"
    And I should see "Course module updated"
    And I should see "The status of the submission has been viewed."
