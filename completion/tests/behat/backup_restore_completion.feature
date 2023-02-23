@core @core_completion
Feature: Backup and restore the activity with the completion

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | First    | student1@example.com |
      | student2 | Student   | Second   | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | student2 | C1 | student        |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | idnumber                            | a1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | completion                          | 2                       |
      | completionview                      | 1                       |
      | completionusegrade                  | 1                       |
      | gradepass                           | 50                      |
      | completionpassgrade                 | 1                       |
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I log out

  @javascript @_file_upload
  Scenario: Restore the legacy assignment with completion condition.
    Given I am on the "Course 1" "restore" page logged in as "admin"
    And I press "Manage backup files"
    And I upload "completion/tests/fixtures/legacy_course_completion.mbz" file to "Files" filemanager
    And I press "Save changes"
    And I restore "legacy_course_completion.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    When I am on the "Course 2" course page logged in as student1
    Then the "View" completion condition of "Test assignment name" is displayed as "done"
    And I am on the "Course 2" course page logged in as student2
    And the "View" completion condition of "Test assignment name" is displayed as "todo"

  @javascript @_file_upload
  Scenario: Backup and restore the assignment with the viewed and not-viewed completion condition
    Given I am on the "Course 1" course page logged in as admin
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    When I am on the "Course 2" course page logged in as student1
    Then the "View" completion condition of "Test assignment name" is displayed as "done"
    And I am on the "Course 2" course page logged in as student2
    And the "View" completion condition of "Test assignment name" is displayed as "todo"
