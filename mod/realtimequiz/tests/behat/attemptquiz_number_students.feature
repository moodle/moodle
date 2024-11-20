@mod @mod_realtimequiz
Feature: The teacher waits for a sufficient number of students

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | Teacher   | 1        | teacher1@moodle.com |
      | student1 | Student   | 1        | student1@moodle.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity     | course | name               | idnumber | intro                             | questiontime |
      | realtimequiz | C1     | Test realtime quiz | RTQ01    | Test the realtime quiz is working | 25           |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test realtime quiz" "realtimequiz activity" page
    And I press "Add question"
    And I set the following fields to these values:
      | Question text | Which UK city is known as the Steel City? |
      | answertext[1] | Sheffield                                 |
      | answertext[2] | Manchester                                |
      | answertext[3] | London                                    |
    And I press "Save question"

  @javascript
  Scenario: The teacher waits for at least one student before sending the first question
    When I am on the "Test realtime quiz" "realtimequiz activity" page
    And I set the field "sessionname" to "Test session"
    And I press "Start quiz"
    And I should see "0 students connected"
    And I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "student1"
    And I press "Join"
    And I log out
    And I am on the "Test realtime quiz" "realtimequiz activity" page logged in as "teacher1"
    And I press "Reconnect to quiz"
    Then I should see "1 student connected"
