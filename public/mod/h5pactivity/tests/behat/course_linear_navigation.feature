@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Display the course linear navigation in the H5P activity pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in H5P activity pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course 1 | C1        | topics | 1               |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activity" exists:
      | activity        | h5pactivity                   |
      | course          | C1                            |
      | name            | Awesome H5P package           |
      | packagefilepath | h5p/tests/fixtures/ipsums.h5p |
    And the following "mod_h5pactivity > attempts" exist:
      | user    | h5pactivity         | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student | Awesome H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |

  @javascript
  Scenario: As a student I should see the course linear navigation in H5P activity pages that allow it
    When I am on the "Awesome H5P package" "h5pactivity activity" page logged in as "student"
    Then the course linear navigation should be visible
    But I switch to "h5p-player" class iframe
    And the course linear navigation should not be visible
    And I switch to the main frame
    And I navigate to "Attempts report" in current page administration
    And the course linear navigation should be visible
    And I should not see "Go to all attempts" in the "sticky-footer" "region"
    And I follow "View report"
    And the course linear navigation should be visible
    And I click on "Go to all attempts" "link" in the "sticky-footer" "region"
    And I should see "My attempts"

  @javascript
  Scenario: As a teacher I should see the course linear navigation in H5P activity pages that allow it
    Given I am on the "Awesome H5P package" "h5pactivity activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    But I navigate to "Attempts report" in current page administration
    And the course linear navigation should not be visible
    And I should not see "Go to all attempts"
    And I follow "View (1)"
    And the course linear navigation should not be visible
    And I should not see "Go to all attempts"
    And I follow "View report"
    And the course linear navigation should not be visible
    And I click on "Go to all attempts" "link" in the "sticky-footer" "region"
    And I should see "Attempts (1)"
