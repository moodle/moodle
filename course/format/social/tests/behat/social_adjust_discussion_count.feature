@format @format_social
Feature: Change number of discussions displayed
  In order to change the number of discussions displayed
  As a teacher
  I need to edit the course and change the number of sections displayed.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | social |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "mod_forum > discussions" exist:
      | user     | forum        | subject       | message                  | created             | timemodified        |
      | teacher1 | Social forum | Forum Post 10 | This is forum post ten   | ##now +1 second##   | ##now +1 second##   |
      | teacher1 | Social forum | Forum Post 9  | This is forum post nine  | ##now +2 seconds##  | ##now +2 seconds##  |
      | teacher1 | Social forum | Forum Post 8  | This is forum post eight | ##now +3 seconds##  | ##now +3 seconds##  |
      | teacher1 | Social forum | Forum Post 7  | This is forum post seven | ##now +4 seconds##  | ##now +4 seconds##  |
      | teacher1 | Social forum | Forum Post 6  | This is forum post six   | ##now +5 seconds##  | ##now +5 seconds##  |
      | teacher1 | Social forum | Forum Post 5  | This is forum post five  | ##now +6 seconds##  | ##now +6 seconds##  |
      | teacher1 | Social forum | Forum Post 4  | This is forum post four  | ##now +7 seconds##  | ##now +7 seconds##  |
      | teacher1 | Social forum | Forum Post 3  | This is forum post three | ##now +8 seconds##  | ##now +8 seconds##  |
      | teacher1 | Social forum | Forum Post 2  | This is forum post two   | ##now +9 seconds##  | ##now +9 seconds##  |
      | teacher1 | Social forum | Forum Post 1  | This is forum post one   | ##now +10 seconds## | ##now +10 seconds## |
    And I am on the "C1" "course editing" page logged in as teacher1

  Scenario: When number of discussions is decreased fewer discussions appear
    Given I set the following fields to these values:
      | numdiscussions | 5 |
    When I press "Save and display"
    Then I should see "This is forum post one"
    And I should see "This is forum post five"
    And I should not see "This is forum post six"

  Scenario: When number of discussions is decreased to less than 1 only 1 discussion should appear
    Given I set the following fields to these values:
      | numdiscussions | -1 |
    When I press "Save and display"
    Then I should see "This is forum post one"
    And I should not see "This is forum post two"
    And I should not see "This is forum post ten"

  Scenario: When number of discussions is increased more discussions appear
    Given I set the following fields to these values:
      | numdiscussions | 9 |
    When I press "Save and display"
    Then I should see "This is forum post one"
    And I should see "This is forum post five"
    And I should see "This is forum post nine"
    And I should not see "This is forum post ten"
