@mod @mod_feedback
Feature: As a teacher, I can see users who have responded or not responded to a feedback activity.
  As a non editing teacher not in a group I cannot see the responses.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | teacher2 | Teacher   | 2        |
      | teacher3 | Teacher   | 3        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
      | teacher3 | C1     | teacher        |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "groups" exist:
      | course | name | idnumber |
      | C1     | G1   | GI1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
      | student2 | GI1   |
      | teacher2 | GI1   |
    When I log in as "teacher1"
    And I add a feedback activity to course "Course 1" section "1" and I fill the form with:
      | Name              | Frogs                                             |
      | Description       | x                                                 |
      | Record user names | User's name will be logged and shown with answers |
    And I am on the Frogs "feedback activity" page
    And I navigate to "Questions" in current page administration
    And I add a "Short text answer" question to the feedback with:
      | Question | Y/N? |
    And I log out

    # Go in as student 1 and do the feedback.
    And I am on the Frogs "feedback activity" page logged in as student1
    And I follow "Answer the questions"
    And I set the field "Y/N?" to "Y"
    And I press "Submit your answers"
    And I log out

  Scenario Outline: If a teacher or non editing teacher is in a group, they can see the responses in separate group mode.
    # Go in as teacher and check the users who haven't completed it.
    Given I am on the Frogs "feedback activity" page logged in as <user>
    Then "Responses" "link" <existsornot> in current page administration
    Examples:
      | user     | existsornot      |
      | teacher1 | should exist     |
      | teacher2 | should exist     |
      | teacher3 | should not exist |

  Scenario Outline: Teachers and non editing teachers in a group can see the responses
    # Go in as teacher and check the users who haven't completed it.
    Given I am on the Frogs "feedback activity" page logged in as <user>
    And I navigate to "Responses" in current page administration
    And I select "Show non-respondents" from the "jump" singleselect
    # Should only show student 2; not student 1 (they did it) or 3 (not in grouping).
    Then I <studentshouldsee>
    And I <studentshouldnotsee>
    Examples:
      | user     | studentshouldsee       | studentshouldnotsee        |
      | teacher1 | should see "Student 2" | should see "Student 3"     |
      | teacher2 | should see "Student 2" | should not see "Student 3" |
