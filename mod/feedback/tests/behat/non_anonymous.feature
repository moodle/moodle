@mod @mod_feedback
Feature: Non anonymous feedback
  In order to collect feedbacks
  As an teacher
  I need to be able to create and collect feedbacks

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Username  | 1        |
      | user2    | Username  | 2        |
      | teacher  | Teacher   | 3        |
      | manager  | Manager   | 4        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |
      | teacher | C1   | editingteacher |
    And the following "system role assigns" exist:
      | user    | course               | role    |
      | manager | Acceptance test site | manager |
    And the following "activities" exist:
      | activity   | name            | course               | idnumber  | anonymous | publish_stats | section |
      | feedback   | Site feedback   | Acceptance test site | feedback0 | 2         | 1             | 1       |
      | feedback   | Course feedback | C1                   | feedback1 | 2         | 1             | 0       |
    When I am on the "Site feedback" "feedback activity" page logged in as manager
    And I navigate to "Questions" in current page administration
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like our site?              |
      | Label                          | multichoice2                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes of course\nNot at all\nI don't know |
    And I log out

  Scenario: Guests can see non anonymous feedback on front page but can not complete
    When I am on the "Site feedback" "feedback activity" page
    Then I should not see "Answer the questions"
    And I should not see "Preview questions"

  Scenario: Complete non anonymous feedback on the front page as an authenticated user
    When I am on the "Site feedback" "feedback activity" page logged in as user1
    And I should not see "Preview questions"
    And I follow "Answer the questions"
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | Yes of course | 1 |
    And I press "Submit your answers"
    And I should not see "Analysis"
    And I press "Continue"

  @javascript
  Scenario: Complete non anonymous feedback and view analysis on the front page as an authenticated user
    Given the following "role capability" exists:
      | role                         | frontpage |
      | mod/feedback:viewanalysepage | allow     |
    When I am on the "Site feedback" "feedback activity" page logged in as user1
    And I follow "Answer the questions"
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | Yes of course | 1 |
    And I press "Submit your answers"
    And I log out
    And I am on the "Site feedback" "feedback activity" page logged in as user2
    And I follow "Answer the questions"
    And I set the following fields to these values:
      | Not at all | 1 |
    And I press "Submit your answers"
    And I follow "Analysis"
    And I should see "Submitted answers: 2"
    And I should see "Questions: 1"
    # And I should not see "multichoice2" # TODO MDL-29303 do not show labels to users who can not edit feedback
    And I show chart data for the "multichoice2" feedback
    And I should see "Do you like our site?"
    And I should see "1 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (50.00 %)" in the "Not at all" "table_row"
    And I log out
    And I am on the "Site feedback" "feedback activity" page logged in as manager
    And I navigate to "Responses" in current page administration
    And I should see "Username"
    And I should see "Non anonymous entries (2)"
    And I should not see "Anonymous entries"
    And I click on "," "link" in the "Username 1" "table_row"
    And I should see "(Username 1)"
    And I should see "Yes of course"
    And I log out

  @javascript
  Scenario: Non anonymous feedback in a course
    When I am on the "Course feedback" "feedback activity" page logged in as teacher
    And I navigate to "Questions" in current page administration
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like this course?           |
      | Label                          | multichoice1                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes of course\nNot at all\nI don't know |
    And I log out
    When I am on the "Course feedback" "feedback activity" page logged in as user1
    And I follow "Answer the questions"
    And I should see "Do you like this course?"
    And I set the following fields to these values:
      | Yes of course | 1 |
    And I press "Submit your answers"
    And I log out
    When I am on the "Course feedback" "feedback activity" page logged in as user2
    And I follow "Answer the questions"
    And I should see "Do you like this course?"
    And I set the following fields to these values:
      | Not at all | 1 |
    And I press "Submit your answers"
    And I follow "Analysis"
    And I should see "Submitted answers: 2"
    And I should see "Questions: 1"
    # And I should not see "multichoice2" # TODO MDL-29303
    And I show chart data for the "multichoice1" feedback
    And I should see "Do you like this course?"
    And I should see "1 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (50.00 %)" in the "Not at all" "table_row"
    And I log out
    When I am on the "Course feedback" "feedback activity" page logged in as teacher
    And I follow "Preview"
    And I should see "Do you like this course?"
    And I press "Continue"
    And I should not see "Answer the questions"
    And I navigate to "Responses" in current page administration
    And I should see "Non anonymous entries (2)"
    And I should not see "Anonymous"
    And I click on "," "link" in the "Username 1" "table_row"
    And I should see "(Username 1)"
    And I should see "Yes of course"
    And I should not see "Prev"
    And I follow "Next"
    And I should see "(Username 2)"
    And I should not see "Next"
    And I should see "Prev"
    And I click on "Back" "link" in the "region-main" "region"
    # Sort the feedback responses.
    And I click on "Do you like this course?" "link" in the "generaltable" "table"
    And "Username 1" "table_row" should appear before "Username 2" "table_row"
    # Now sort descending.
    And I click on "Do you like this course?" "link" in the "generaltable" "table"
    And "Username 2" "table_row" should appear before "Username 1" "table_row"
    # Delete non anonymous response
    And I click on "Delete entry" "link" in the "Username 1" "table_row"
    And I press "Yes"
    And I should see "Non anonymous entries (1)"
    And I should not see "Username 1"
    And I should see "Username 2"
