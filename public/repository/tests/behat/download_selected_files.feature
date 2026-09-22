@core @core_filepicker
Feature: Download files from the file manager
  In order to understand why nothing was downloaded
  As a user
  I need to be told when the file area has no files to download

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript
  Scenario: Downloading an empty file area reports that there is nothing to download
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I click on "Display folder as file tree" "link"
    When I press "Download"
    Then I should see "Cannot be downloaded because there is no files attached"