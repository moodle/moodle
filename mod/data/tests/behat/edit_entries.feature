@mod @mod_data @javascript @editor_tiny
Feature: Edit existing database entries
  In order to modify a database entry
  As a teacher
  I need to successfully have changes recorded for each entry

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro      | course | idnumber |
      | data     | Test database name | Intro text | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type     | name        | required | description |
      | data1    | text     | headline    | 1        | Headline    |
      | data1    | textarea | description | 0        | Description |
    And the following "mod_data > entries" exist:
      | database | user     | headline   | description   |
      | data1    | teacher1 | Headline 1 | Original text |
      | data1    | teacher1 | Headline 2 |               |

  Scenario: Text areas are filled correctly when editing datasets
    Given I am on the "Test database name" "data activity" page logged in as "teacher1"
    And I select "Single view" from the "jump" singleselect
    And I should see "Headline 1" in the "region-main" "region"
    And I should see "Original text" in the "region-main" "region"
    And I click on ".defaulttemplate-single-body .action-menu" "css_element"
    # Edit fields and observe changes.
    And I click on "Edit" "link" in the ".defaulttemplate-single-body .dropdown-menu" "css_element"
    And I wait until the page is ready
    And I set the field "headline" to "New headline"
    And I set the field "description" to "New text"
    When I click on "Save" "button" in the "sticky-footer" "region"
    Then I should not see "Headline 1" in the "region-main" "region"
    And I should not see "Original text" in the "region-main" "region"
    And I should see "New headline" in the "region-main" "region"
    And I should see "New text" in the "region-main" "region"

  Scenario: Text areas are filled correctly when triggering autosave without making changes
    Given I am on the "Test database name" "data activity" page logged in as "teacher1"
    And I select "Single view" from the "jump" singleselect
    # Edit the the first DB entry textfield, but don't save the changes.
    And I click on ".defaulttemplate-single-body .action-menu" "css_element"
    And I click on "Edit" "link" in the ".defaulttemplate-single-body .dropdown-menu" "css_element"
    And I wait until the page is ready
    And I set the field "description" to "Unsaved changes"
    # Trigger autosave.
    And I press tab
    And I wait "1" seconds
    And I click on "Cancel" "button" in the "sticky-footer" "region"
    # Edit the second DB entry headline only.
    And I select "Single view" from the "jump" singleselect
    And I click on "2" "link" in the "sticky-footer" "region"
    And I click on ".defaulttemplate-single-body .action-menu" "css_element"
    And I click on "Edit" "link" in the ".defaulttemplate-single-body .dropdown-menu" "css_element"
    And I wait until the page is ready
    And I set the field "headline" to "New headline"
    And I click on "Save" "button" in the "sticky-footer" "region"
    And I select "Single view" from the "jump" singleselect
    And I click on "2" "link" in the "sticky-footer" "region"
    # Only the new heading is updated and the description field autosave did not carry over from the first entry.
    And I should see "New headline" in the "region-main" "region"
    And I should not see "Unsaved changes" in the "region-main" "region"
