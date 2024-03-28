@core @core_backup
Feature: Backup and restore of the question that was tagged

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |

  @javascript @_file_upload
  Scenario: Restore the quiz containing the question that was tagged
    Given I am on the "Course 1" "restore" page logged in as "admin"
    And I press "Manage course backups"
    And I upload "backup/moodle2/tests/fixtures/test_tags_backup.mbz" file to "Files" filemanager
    And I press "Save changes"
    And I restore "test_tags_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    When I am on the "TF1" "core_question > edit" page logged in as admin
    And I expand all fieldsets
    Then I should see "Tag1-TF1"
    And I should see "Tag2-TF1"
    And I am on the "TF2" "core_question > edit" page logged in as admin
    And I expand all fieldsets
    And I should see "Tag1-TF2"
