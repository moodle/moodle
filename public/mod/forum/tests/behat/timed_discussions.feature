@mod @mod_forum
Feature: Users can choose to set start and end time for display of their discussions
  In order to temporarly hide discussions to students
  As a teacher
  I need to set a discussion time start and time end

  Scenario: Student should not see the tooltip or the discussion
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name            | course | idnumber | type    |
      | forum      | Test forum name | C1     | forump1  | general |
    And the following "mod_forum > discussions" exist:
      | user     | forum   | name                           | message                              | timeend              | timestart |
      | admin    | forump1 | Discussion 1                   | Discussion contents 1, first message |                      |           |
      | admin    | forump1 | Discussion 2 timed not visible | Discussion contents 2, first message | ##1 Jan 2014 08:00## |           |
      | admin    | forump1 | Discussion 3 timed visible now | Discussion contents 3, first message |                      | 1         |
    And the following config values are set as admin:
      | forum_enabletimedposts | 1 |
    And I am on the "Test forum name" "forum activity" page logged in as admin
    And I should see "Discussion 2 timed"
    And I should see "Discussion 3 timed"
    And "[data-region=timed-label]" "css_element" should exist
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I should see "Discussion 1"
    And I should not see "Discussion 2 timed"
    And "[data-region=timed-label]" "css_element" should not exist
    And I should see "Discussion 3 timed"
