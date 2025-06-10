@mod @mod_forum @core_grades @core_form
Feature: Using the forum activities which support point scale
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
    And the following "activity" exists:
      | course      | C1                     |
      | activity    | forum                  |
      | name        | Test forum name        |
      | idnumber    | forum1                 |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name               | message              |
      | student1 | forum1 | Discussion subject | Test post in forum 1 |

  @javascript
  Scenario: Forum rescale grade should not be possible when users are graded
    Given I am on the "Test forum name" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Ratings > Aggregate type" to "Count of ratings"
    And I set the field "Ratings > Type" to "Point"
    And I press "Save and return to course"
    And I am on the "Test forum name" "forum activity" page
    And I follow "Discussion subject"
    And I set the field "rating" to "30"
    When I am on the "Test forum name" "forum activity editing" page
    And I expand all fieldsets
    Then the "Maximum grade" "field" should be disabled
