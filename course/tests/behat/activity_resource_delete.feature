@core @core_course
Feature: Delete activity and resource works correctly
  As a teacher
  I want to be able to delete an activity and resource
  So that I can remove it from the course

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
      | activity | course | name       | intro      |
      | label    | C1     | Label 1    | Label 1    |
      | glossary | C1     | Glossary 1 | Glossary 1 |

  @javascript
  Scenario: Activity and resource can be deleted properly
    Given I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    And I open "Label 1" actions menu
    When I click on "Delete" "link" in the "Label 1" activity
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    # Confirm that label is successfully deleted
    Then I should not see "Label 1"
    And I open "Glossary 1" actions menu
    And I click on "Delete" "link" in the "Glossary 1" activity
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    # Confirm that glossary is successfully deleted
    And I should not see "Glossary 1"
    # Reload the page and confirm that both the label and glossary are really deleted
    And I reload the page
    And I should not see "Label 1"
    And I should not see "Glossary 1"
