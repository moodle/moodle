@mod @mod_feedback
Feature: Anonymous feedback
  In order to collect feedbacks
  As an admin
  I need to be able to allow anonymous feedbacks

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
      | feedback   | Site feedback   | Acceptance test site | feedback0 | 1         | 1             | 1       |
      | feedback   | Course feedback | C1                   | feedback1 | 1         | 1             | 0       |
    When I log in as "manager"
    And I am on site homepage
    And I follow "Site feedback"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like our site?              |
      | Label                          | multichoice2                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes\nNo\nI don't know              |
    And I log out

  Scenario: Guests can see anonymous feedback on front page but can not complete
    When I follow "Site feedback"
    Then I should not see "Answer the questions..."
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"

  Scenario: Complete anonymous feedback on the front page as an authenticated user
    And I log in as "user1"
    And I am on site homepage
    When I follow "Site feedback"
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | Yes | 1 |
    And I press "Submit your answers"
    And I should not see "Submitted answers"
    And I press "Continue"

  @javascript
  Scenario: Complete anonymous feedback and view analysis on the front page as an authenticated user
    And I log in as "admin"
    And I set the following system permissions of "Authenticated user on frontpage" role:
      | capability                   | permission |
      | mod/feedback:viewanalysepage | Allow      |
    And I log out
    And I log in as "user1"
    And I am on site homepage
    When I follow "Site feedback"
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | Yes | 1 |
    And I press "Submit your answers"
    And I log out
    And I log in as "user2"
    And I am on site homepage
    And I follow "Site feedback"
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | No | 1 |
    And I press "Submit your answers"
    And I follow "Submitted answers"
    And I should see "Submitted answers: 2"
    And I should see "Questions: 1"
    # And I should not see "multichoice2" # TODO MDL-29303 do not show labels to users who can not edit feedback
    And I show chart data for the "multichoice2" feedback
    And I should see "Do you like our site?"
    And I should see "1 (50.00 %)" in the "Yes" "table_row"
    And I should see "1 (50.00 %)" in the "No" "table_row"
    And I log out
    And I log in as "manager"
    And I am on site homepage
    And I follow "Site feedback"
    And I navigate to "Show responses" in current page administration
    And I should not see "Username"
    And I should see "Anonymous entries (2)"
    And I follow "Response number: 1"
    And I should not see "Username"
    And I should see "Response number: 1 (Anonymous)"
    And I log out

  Scenario: Complete fully anonymous feedback on the front page as a guest
    And I log in as "admin"
    And I set the following administration settings values:
      | feedback_allowfullanonymous | 1 |
    And I log out
    When I follow "Site feedback"
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | Yes | 1 |
    And I press "Submit your answers"
    And I should not see "Submitted answers"
    And I press "Continue"

  @javascript
  Scenario: Complete fully anonymous feedback and view analyze on the front page as a guest
    And I log in as "admin"
    And I set the following administration settings values:
      | feedback_allowfullanonymous | 1 |
    And I set the following system permissions of "Guest" role:
      | capability                   | permission |
      | mod/feedback:viewanalysepage | Allow      |
    And I log out
    When I follow "Site feedback"
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | Yes | 1 |
    And I press "Submit your answers"
    And I press "Continue"
    # Starting new feedback
    When I follow "Site feedback"
    And I follow "Preview"
    And I should see "Do you like our site?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like our site?"
    And I set the following fields to these values:
      | No | 1 |
    And I press "Submit your answers"
    And I follow "Submitted answers"
    And I should see "Submitted answers: 2"
    And I should see "Questions: 1"
    # And I should not see "multichoice2" # TODO MDL-29303
    And I show chart data for the "multichoice2" feedback
    And I should see "Do you like our site?"
    And I should see "1 (50.00 %)" in the "Yes" "table_row"
    And I should see "1 (50.00 %)" in the "No" "table_row"
    And I log in as "manager"
    And I am on site homepage
    And I follow "Site feedback"
    And I navigate to "Show responses" in current page administration
    And I should see "Anonymous entries (2)"
    And I follow "Response number: 1"
    And I should see "Response number: 1 (Anonymous)"
    And I log out

  @javascript
  Scenario: Anonymous feedback in a course
    # Teacher can not
    When I log in as "teacher"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like this course?           |
      | Label                          | multichoice1                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes\nNo\nI don't know              |
    And I log out
    And I log in as "user1"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I follow "Preview"
    Then I should see "Do you like this course?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like this course?"
    And I set the following fields to these values:
      | Yes | 1 |
    And I press "Submit your answers"
    And I log out
    And I log in as "user2"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I follow "Preview"
    And I should see "Do you like this course?"
    And I press "Continue"
    And I follow "Answer the questions..."
    And I should see "Do you like this course?"
    And I set the following fields to these values:
      | No | 1 |
    And I press "Submit your answers"
    And I follow "Submitted answers"
    And I should see "Submitted answers: 2"
    And I should see "Questions: 1"
    # And I should not see "multichoice2" # TODO MDL-29303
    And I show chart data for the "multichoice1" feedback
    And I should see "Do you like this course?"
    And I should see "1 (50.00 %)" in the "Yes" "table_row"
    And I should see "1 (50.00 %)" in the "No" "table_row"
    And I log out
    And I log in as "teacher"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I follow "Preview"
    And I should see "Do you like this course?"
    And I press "Continue"
    And I should not see "Answer the questions..."
    And I navigate to "Show responses" in current page administration
    And I should not see "Username"
    And I should see "Anonymous entries (2)"
    And I follow "Response number: 1"
    And I should not see "Username"
    And I should see "Response number: 1 (Anonymous)"
    And I should not see "Prev"
    And I follow "Next"
    And I should see "Response number: 2 (Anonymous)"
    And I should see "Prev"
    And I should not see "Next"
    And I click on "Back" "link" in the "[role=main]" "css_element"
    # Delete anonymous response
    And I click on "Delete entry" "link" in the "Response number: 1" "table_row"
    And I press "Yes"
    And I should see "Anonymous entries (1)"
    And I should not see "Response number: 1"
    And I should see "Response number: 2"
    And I log out

  Scenario: Collecting new non-anonymous feedback from a previously anonymous feedback activity
    When I log in as "teacher"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Allow multiple submissions | Yes |
    And I press "Save and display"
    And I follow "Edit questions"
    And I add a "Short text answer" question to the feedback with:
      | Question               | this is a short text answer |
      | Label                  | shorttext                   |
      | Maximum characters accepted | 200                    |
    And I log out
    When I log in as "user1"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | this is a short text answer  | anontext |
    And I press "Submit your answers"
    And I log out
    # Switch to non-anon responses.
    And I log in as "teacher"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
        | Record user names | User's name will be logged and shown with answers |
    And I press "Save and display"
    And I log out
    # Now leave a non-anon feedback as user1
    When I log in as "user1"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I follow "Answer the questions..."
    And I set the following fields to these values:
      | this is a short text answer  | usertext |
    And I press "Submit your answers"
    And I log out
    # Now check the responses are correct.
    When I log in as "teacher"
    And I follow "Course 1"
    And I follow "Course feedback"
    And I follow "Show responses"
    And I should see "Anonymous entries (1)"
    And I should see "Non anonymous entries (1)"
    And I click on "," "link" in the "Username 1" "table_row"
    And I should see "(Username 1)"
    And I should see "usertext"
    And I follow "Back"
    And I follow "Response number: 1"
    And I should see "Response number: 1 (Anonymous)"
    Then I should see "anontext"
