@mod @mod_subsection @_file_upload
Feature: Subsection restore backup with descriptions
  In order to manage subsection descriptions
  As an administrator
  I want to be able to restore backups containing subsection descriptions ignoring them

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |
    And I am on the "Course 1" "restore" page logged in as "admin"
    And I press "Manage course backups"
    And I upload "mod/subsection/tests/fixtures/subsections_with_descriptions.mbz" file to "Files" filemanager
    And I press "Save changes"

  @javascript
  Scenario: Check subsection descriptions are not restored
    Given I restore "subsections_with_descriptions.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    When I navigate to "Plugins > Activity modules > Subsection" in site administration
    # If this message appears, it means that the descriptions were restored.
    Then I should not see "This site has 2 subsection descriptions that are no longer visible to users."
