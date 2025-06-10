@block @block_ues_meta_viewer @javascript @wip
Feature: Verify behavior of UES Data Viewer results
  In order to search UES enrollment metadata, as a privileged user,
    the UES Data Viewer needs to return accurate results.

Background:
    Given the following "courses" exist:
        | fullname | shortname | category | groupmode |
        | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
        | username | firstname | lastname | email |
        | teacher1 | Teacher | 1 | teacher1@asd.com |
        | student1 | Student | 1 | student1@asd.com |
        | student2 | Student | 2 | student2@asd.com |
    And the following "course enrolments" exist:
        | user | course | role |
        | teacher1 | C1 | editingteacher |
        | student1 | C1 | student |
        | student2 | C1 | student |
    And I log in as "admin"
    And I am on "homepage"
    And I turn editing mode on
    When I add the "UES Data Viewer" block
    Then I should see "User Data Viewer" in the "UES Data Viewer" "block"