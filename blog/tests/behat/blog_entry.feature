@core @core_blog @_file_upload @javascript
Feature: Blog entries can be added, modified and deleted
  In order to modify or delete a blog entry
  As a user
  I need to be able to add a blog entry

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | testuser | Test      | User     | moodle@example.com |
    And I am on the "testuser" "user > profile" page logged in as testuser
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I should see "Blogs: Add a new entry"
    And I set the following fields to these values:
      | Entry title     | Entry 1         |
      | Blog entry body | Entry 1 content |
      | Attachment      | lib/tests/fixtures/gd-logo.png |
    And I press "Save changes"

  Scenario: Modify a blog entry
    When I click on "Edit" "link"
    And I set the following fields to these values:
      | Entry title | Blog entry 1 |
    And I press "Save changes"
    Then I should see "Blog entry 1"

  Scenario: Delete a blog entry
    When I click on "Delete" "link"
    And I press "Continue"
    Then I should not see "Entry 1"
    And I should see "Add a new entry"
