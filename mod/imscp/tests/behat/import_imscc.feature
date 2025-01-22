@mod @mod_imscp @core_backup
Feature: IMS Common Cartridge package import
  In order to add a Common Cartridge package to a course
  As a teacher
  I need to be able to import IMS Common Cartridge package

  Background:
    Given the following "users" exist:
      | username | firtname | lastname | email                |
      | teacher1 | Teacher  | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript @_file_upload
  Scenario: Teacher can import an IMS Common Cartridge to a course
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course reuse" in current page administration
    And I follow "Restore"
    When I upload "mod/imscp/tests/packages/py4e_export.imscc" file to "Backup file" filemanager
    And I press "Restore"
    # Confirm that IMS Common Cartridge package can be restored.
    Then I should see "The selected file is not a standard Moodle backup file. The restore process will try to convert the backup file into the standard format and then restore it."
    And I should see "IMS Common Cartridge 1.1"
    # Start the restore procedure.
    And I press "Continue"
    # Merge imscc into the existing course, Course 1.
    And I press "Continue"
    # Proceed with default restore settings.
    And I press "Next"
    # Proceed with default course settings.
    And I press "Next"
    # Start the restore process.
    And I press "Perform restore"
    # Run cron to execute restore process.
    And I trigger cron
    # Confirm the imscc package was successfully restored and added to the existing course
    And I am on the "Course 1" course page
    And I should see "Installing Python"
