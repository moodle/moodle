@mod @mod_imscp @javascript @_file_upload
Feature: Create an IMSCP activity through UI
  In order to confirm that IMSCP activity creation via UI works correctly

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |

  Scenario: IMS activity is created using UI
    Given I log in as "admin"
    And I add a imscp activity to course "Course 1" section "1"
    And I set the following fields to these values:
      | Name        | Test IMS content package 2           |
      | Description | Test IMS content package description |
    And I upload "mod/imscp/tests/packages/singlescobasic.zip" file to "Package file" filemanager
    When I press "Save and return to course"
    Then I should see "Test IMS content package 2"
