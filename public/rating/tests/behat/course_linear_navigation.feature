@core @core_rating
Feature: Hide the course linear navigation in the rating pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want the course linear navigation to be hidden in rating pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student  | Student   | 1        |
      | teacher  | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name        |
      | forum    | C1     | Forum1      |
    And the following "mod_forum > discussions" exist:
      | user    | forum  | name        | message                                                                           |
      | student | Forum1 | Discussion1 | Those long days passing by from that door, like late summer they slowly fade away |
    And I am on the "Forum1" "forum activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type           | Average of ratings |
      | scale[modgrade_type]     | Point              |
    And I press "Save and display"

  @javascript
  Scenario: As a user I should not see the course linear navigation in rating pages
    When I am on the "Forum1" Activity page logged in as teacher
    And I follow "Discussion1"
    And I set the field "rating" to "67"
    And I follow "67 (1)"
    And I switch to "ratings" window
    Then the course linear navigation should not be visible
    And I switch to the main window
    And the course linear navigation should be visible
