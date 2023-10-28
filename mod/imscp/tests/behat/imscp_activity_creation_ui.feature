@mod @mod_imscp @javascript @_file_upload
Feature: Create an IMSCP activity through UI
  In order to confirm that IMSCP activity creation via UI works correctly

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |

  Scenario: IMS activity is created using UI
    Given I am on the "Course 1" course page logged in as admin
    And I turn editing mode on
    And I add a "IMS content package" to section "1"
    And I set the following fields to these values:
      | Name        | Test IMS content package 2           |
      | Description | Test IMS content package description |
    And I upload "mod/imscp/tests/packages/singlescobasic.zip" file to "Package file" filemanager
    When I press "Save and return to course"
    Then I should see "Test IMS content package 2"
