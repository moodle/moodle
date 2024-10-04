@mod @mod_assign
Feature: Submissions are unlocked when a new attempt is given
  In order to allow students to reattempt a locked submission
  As a teacher
  I need to use quick grading to grant a new submission

  Background:
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

  @javascript
  Scenario: A locked submission should unlock when a new attempt is automatically given.
    Given the following "activity" exists:
      | activity                             | assign                |
      | course                               | C1                    |
      | name                                 | Test assignment name  |
      | submissiondrafts                     | 0                     |
      | assignsubmission_onlinetext_enabled  | 1                     |
      | maxattempts                          | -1                    |
      | attemptreopenmethod                  | untilpass             |
      | gradepass                            | 50                    |
      | submissiondrafts                     | 0                     |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    And I open the action menu in "Student 1" "table_row"
    And I change window size to "medium"
    And I follow "Prevent submission changes"
    And I should see "Submission changes not allowed"
    And I click on "Quick grading" "checkbox"
    And I set the field "User grade" to "49.0"
    And I click on "Save" "button" in the "sticky-footer" "region"
    And I should see "The grade changes were saved"
    And I press "Continue"
    Then I should see "Reopened"
    And I should not see "Submission changes not allowed"

  @javascript
  Scenario: A locked submission should unlock when a new attempt is manually given.
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | submissiondrafts                    | 0                       |
      | assignsubmission_onlinetext_enabled | 1                       |
      | maxattempts                         | -1                      |
      | attemptreopenmethod                 | manual                  |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    When I open the action menu in "Student 1" "table_row"
    And I change window size to "medium"
    And I follow "Prevent submission changes"
    Then I should see "Submission changes not allowed"
    And I change window size to "large"
    And I open the action menu in "Student 1" "table_row"
    And I change window size to "medium"
    And I follow "Allow another attempt"
    And I should see "Reopened"
    And I should not see "Submission changes not allowed"
