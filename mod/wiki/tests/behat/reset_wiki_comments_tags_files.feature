@mod @mod_wiki @javascript @_file_upload
Feature: Teachers can reset wiki pages, tags and files
  In order to remove wiki pages, tags and files
  As a teacher
  I need to be able to reset the pages, tags and files on the course level

  Background: Create a wiki, add a page, tag and file, and reset them
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Test wiki |
      | Description | Test wiki description |
      | First page name | Test wiki page |
    And I follow "Test wiki"
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | Test wiki content |
      | Tags | Test tag 1, Test tag 2, |
    And I press "Save"
    And I should see "Test tag 1" in the ".wiki-tags" "css_element"
    And I should see "Test tag 2" in the ".wiki-tags" "css_element"
    And I follow "Comments"
    And I follow "Add comment"
    And I set the following fields to these values:
      | Comment | Test comment |
    And I press "Save changes"
    And I should see "Test comment"
    And I follow "Files"
    And I press "Edit wiki files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I should see "empty.txt"
    And I navigate to "Reset" node in "Course administration"

  Scenario: Reset page, tags and files
    And I set the following fields to these values:
      | Delete all wiki pages | 1 |
      | Remove all wiki tags | 1 |
      | reset_wiki_comments | 1 |
    And I press "Reset course"
    And I should see "Delete all wiki pages"
    And I should see "Wiki tags have been deleted"
    And I should see "Delete all comments"
    And I press "Continue"
    And I follow "Test wiki"
    And I press "Create page"
    When I follow "View"
    Then I should not see "Test tag 1"
    And I should not see "Test tag 2"
    And I follow "Comments"
    And I should not see "Test comment"
    And I follow "Files"
    And I should not see "empty.txt"

  Scenario: Reset only tags
    And I set the following fields to these values:
      | Remove all wiki tags | 1 |
    And I press "Reset course"
    And I should not see "Delete all wiki pages"
    And I should see "Wiki tags have been deleted"
    And I should not see "Delete all comments"
    And I press "Continue"
    And I follow "Test wiki"
    Then I should not see "Test tag 1"
    And I should not see "Test tag 2"
    And I follow "Comments"
    And I should see "Test comment"
    And I follow "Files"
    And I should see "empty.txt"

  Scenario: Reset only comments
    And I set the following fields to these values:
      | reset_wiki_comments | 1 |
    And I press "Reset course"
    And I should not see "Delete all wiki pages"
    And I should not see "Wiki tags have been deleted"
    And I should see "Delete all comments"
    And I press "Continue"
    When I follow "Test wiki"
    Then I should see "Test tag 1"
    And I should see "Test tag 2"
    And I follow "Comments"
    And I should not see "Test comment"
    And I follow "Files"
    And I should see "empty.txt"