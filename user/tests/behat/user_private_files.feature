@core @core_user @_file_upload @javascript
Feature: The private files page allows users to store files privately in moodle.
  In order to store a private file in moodle
  As an authenticated user
  I can upload the file to my private files area from the private files page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |

  Scenario: Upload a file to the private files area from the private files page
    Given I log in as "user1"
    And I follow "Private files" in the user menu
    And I should see "User 1" in the ".page-context-header" "css_element"
    And I should see "Private files" in the "region-main" "region"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    When I press "Save changes"
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-file" "css_element"
