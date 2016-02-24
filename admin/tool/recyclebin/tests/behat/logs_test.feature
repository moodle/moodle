@local_recyclebin
Feature: Recycle bin refinements
  As a teacher
  I want the log to reflect the recycle bin's actions

  Background: Course with teacher exists.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Delete an assignment and check if it got logged.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Page" to section "1" and I fill the form with:
      | Name                | Test page |
      | Description         | Test   |
      | Page content        | Test   |
    And I delete "Test page" activity
    And I follow "Recycle bin"
    When I click on "Delete" "link"
    # Uncomment if running with javascript.
    #And I press "Yes"
    And I wait to be redirected
    And I follow "C1"
    And I expand "Reports" node
    And I follow "Logs"  
    And I click on "Get these logs" "link_or_button"
    Then I should see "Item stored" 
    And I should see "Item purged"
