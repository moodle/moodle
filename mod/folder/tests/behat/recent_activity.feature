@mod @mod_folder @block @block_recent_activity
Feature: Files added in folder activity are visible in the recent activity block
  In order to view and download folder activity from recent activity block
  As a teacher
  I should be able to create folder activity with contents

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion |
      | recent_activity | Course       | C1        | course-view-*   | side-pre      |
    And the following "activities" exist:
      | activity | course | name     |
      | folder   | C1     | Folder 1 |

  @_file_upload @javascript
  Scenario: Files added in folder activity are visible in recent activity block
    Given I am on the "Folder 1" "folder activity" page logged in as admin
    And I click on "Edit" "button"
    # Upload different file types in folder resource
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I upload "lib/tests/fixtures/gd-logo.png" file to "Files" filemanager
    And I press "Save changes"
    # Confirm folder activity and files within the folder are visible in Recent activity block
    When I am on the "Course 1" course page
    Then I should see "Folder 1" in the "Recent activity" "block"
    And I should see "empty.txt" in the "Recent activity" "block"
    And I should see "gd-logo.png" in the "Recent activity" "block"
    And I click on "Full report of recent activity..." "link"
    # Confirm files within folder activity are visible in the full report
    And "Folder 1" "link" should exist
    And "empty.txt" "link" should exist
    And "gd-logo.png" "link" should exist
    And "//img[@alt='empty.txt']" "xpath_element" should exist
    And "//img[contains(@src, 'preview=tinyicon')]" "xpath_element" should exist
    # Confirm files are downloadable
    And following "empty.txt" should download between "1" and "3000" bytes
    And following "gd-logo.png" should download between "1" and "3000" bytes
