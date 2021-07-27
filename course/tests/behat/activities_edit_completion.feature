@core @core_course
Feature: Edit completion settings of an activity
  In order to edit completion settings without accidentally breaking user data
  As a teacher
  I need to edit the activity and use the unlock button if required

  Background:
    Given the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 1                |
    And the following "activities" exist:
      | activity | course | idnumber | intro | name     | completion | completionview |
      | page     | C1     | p1       | x     | TestPage | 2          | 1              |

  Scenario: Completion is not locked when the activity has not yet been viewed
    Given I am on the TestPage "Page Activity editing" page logged in as admin
    When I expand all fieldsets
    Then I should see "Completion tracking"
    And I should not see "Completion options locked"

  Scenario: Completion is locked after the activity has been viewed
    Given I am on the TestPage "Page Activity" page logged in as admin
    When I am on the TestPage "Page Activity editing" page
    And I expand all fieldsets
    Then I should see "Completion options locked"

  @javascript
  Scenario: Pressing the unlock button allows the user to edit completion settings
    Given I am on the TestPage "Page Activity" page logged in as admin
    When I am on the TestPage "Page Activity editing" page
    And I expand all fieldsets
    And I press "Unlock completion options"
    Then I should see "Completion options unlocked"
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then the field "Completion tracking" matches value "Students can manually mark the activity as completed"

  @javascript
  Scenario: Even when completion is locked, the user can still set the date
    Given I am on the TestPage "Page Activity" page logged in as admin
    And I am on the TestPage "Page Activity editing" page
    And I expand all fieldsets
    When I click on "id_completionexpected_enabled" "checkbox"
    And I set the field "id_completionexpected_year" to "2013"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then the field "id_completionexpected_year" matches value "2013"
