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
    Given I am on the "Course 1" course page logged in as teacher1
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Test field name |
      | Field description | Test field description |
    And I navigate to "Templates" in current page administration
    And I wait until the page is ready
    And I am on the "Test database name" "data activity editing" page
    And I expand all fieldsets
    And I set the field "Ratings > Aggregate type" to "Count of ratings"
    And I set the field "Ratings > Type" to "Point"
    And I press "Save and return to course"
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I add an entry to "Test database name" database with:
      | Test field name | Student original entry |
      | Test field name | Student original entry 2 |
    And I press "Save"
    And I log out
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I select "Single view" from the "jump" singleselect
    And I set the field "rating" to "51"
    And I am on the "Test database name" "data activity editing" page
    And I expand all fieldsets
    Then the "Maximum grade" "field" should be disabled
