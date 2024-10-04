@mod @mod_assign
Feature: Prevent or allow assignment submission changes
  In order to control when a student can change his/her submission
  As a teacher
  I need to prevent or allow student submission at any time

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  @javascript
  Scenario: Preventing changes and allowing them again
    Given the following "activity" exists:
      | activity                                      | assign                  |
      | course                                        | C1                      |
      | name                                          | Test assignment name    |
      | intro                                         | Submit your online text |
      | submissiondrafts                              | 0                       |
      | assignsubmission_onlinetext_enabled           | 1                       |
      | assignsubmission_file_enabled                 | 0                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                  |
      | Test assignment name  | student1  | I'm the student submission  |

    And I am on the "Test assignment name" Activity page logged in as student1
    And I press "Edit submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission and he/she edited me |
    And I press "Save changes"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Submissions" in current page administration
    And I open the action menu in "Student 1" "table_row"
    And I follow "Prevent submission changes"
    Then I should see "Submission changes not allowed"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student1
    And "Edit submission" "button" should not exist
    And I should see "This assignment is not accepting submissions"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I open the action menu in "Student 1" "table_row"
    And I follow "Allow submission changes"
    And I should not see "Submission changes not allowed"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student1
    And I should not see "This assignment is not accepting submissions"
    And I press "Edit submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission edited again |
    And I press "Save changes"
    And I should see "I'm the student submission edited again"

  @javascript @_alert
  Scenario: Preventing changes and allowing them again (batch action)
    Given the following "activities" exist:
      | activity | course | name                 | intro                       | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled |
      | assign   | C1     | Test assignment name | Test assignment description | 1                                   | 0                             |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                  |
      | Test assignment name  | student1  | I'm the student submission  |
      | Test assignment name  | student2  | I'm the student2 submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Lock" "button" in the "sticky-footer" "region"
    And I click on "Lock" "button" in the "Lock submissions" "dialogue"
    Then I should see "Submission changes not allowed" in the "Student 1" "table_row"
    And I should see "Submission changes not allowed" in the "Student 2" "table_row"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I should not see "Edit submission"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I set the field "selectall" to "1"
    And I click on "Unlock" "button" in the "sticky-footer" "region"
    And I click on "Unlock" "button" in the "Unlock submissions" "dialogue"
    And I should not see "Submission changes not allowed" in the "Student 1" "table_row"
    And I should not see "Submission changes not allowed" in the "Student 2" "table_row"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I press "Edit submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission and he/she edited me |
    And I press "Save changes"
