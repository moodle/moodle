@mod @mod_data @core_grades @core_form
Feature: Using the database activities which support point scale
  validate if we can change the maximum grade when users are graded
  As a teacher
  I need to know whether I can not edit value of Maximum grade input field

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |

  @javascript
  Scenario: Database rescale grade should not be possible when users are graded
    Given the following "mod_data > fields" exist:
      | database | type | name            | description            |
      | data1    | text | Test field name | Test field description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user     | Test field name          |
      | data1    | student1 | Student original entry   |
      | data1    | student1 | Student original entry 2 |
    And I am on the "Test database name" "data activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Ratings > Aggregate type" to "Count of ratings"
    And I set the field "Ratings > Type" to "Point"
    And I press "Save and display"
    And I select "Single view" from the "jump" singleselect
    And I set the field "rating" to "51"
    And I am on the "Test database name" "data activity editing" page
    When I expand all fieldsets
    Then the "Maximum grade" "field" should be disabled
