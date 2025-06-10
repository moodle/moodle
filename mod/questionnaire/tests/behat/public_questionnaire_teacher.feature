@mod @mod_questionnaire
Feature: Public questionnaires gather all instance responses in one master course, but not in the instance courses.
  When teachers view a course instance they will not see any student responses.
  When teachers view the main public course questionnaire, they will see all instances' responses.

  Background: Add a public questionnaire and use it in two different course.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | teacher3 | Teacher | 3 | teacher3@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
      | Course 3 | C3 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | manager |
      | teacher2 | C2 | editingteacher |
      | student1 | C2 | student |
      | teacher3 | C3 | editingteacher |
      | student2 | C3 | student |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Public questionnaire | Anonymous questionnaire description | C1 | questionnaire0 |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Public questionnaire"
    And I navigate to "Advanced settings" in current page administration
    And I set the field "realm" to "public"
    And I press "Save and display"
    And I navigate to "Questions" in current page administration
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Question Text | Enter a number |
    And I log out

    And I log in as "teacher2"
    And I add a questionnaire activity to course "Course 2" section "1" and I fill the form with:
      | Name | Questionnaire instance 1 |
      | Description | Description |
      | Use public | Public questionnaire [Course 1] |
    Then I should see "Questionnaire instance 1"
    And I log out

    And I log in as "teacher3"
    And I add a questionnaire activity to course "Course 3" section "1" and I fill the form with:
      | Name | Questionnaire instance 2 |
      | Description | Description |
      | Use public | Public questionnaire [Course 1] |
    Then I should see "Questionnaire instance 2"
    And I log out

    And I am on the "Questionnaire instance 1" "mod_questionnaire > view" page logged in as "student1"
    And I follow "Questionnaire instance 1"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Questionnaire instance 1"
    And I set the field "Enter a number" to "1"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I log out

    And I am on the "Questionnaire instance 2" "mod_questionnaire > view" page logged in as "student2"
    And I should see "Answer the questions..."
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Questionnaire instance 2"
    And I set the field "Enter a number" to "2"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I log out

  @javascript
  Scenario: Teacher should not see responses for a questionnaire using a public instance
    And I am on the "Questionnaire instance 1" "mod_questionnaire > view" page logged in as "teacher2"
    And I should not see "View your response(s)"
    And I should not see "View all responses"
    And I log out

  # Scenario: Teacher in course with main public questionnaire should see all responses
    And I am on the "Public questionnaire" "mod_questionnaire > view" page logged in as "teacher1"
    Then I should see "View all responses"
    And I navigate to "View all responses" in current page administration
    Then I should see "Responses: 2"
