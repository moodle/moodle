@core @core_course
Feature: Rename roles within a course
  In order to set course roles names according to their responsabilities
  As a teacher
  I need to edit the course role names

  @javascript
  Scenario: Rename roles within a course
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | teacher2 | Teacher | 2 | teacher2@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Your word for 'Non-editing teacher' | Tutor |
      | Your word for 'Student' | Learner |
    And I press "Save changes"
    And I expand "Switch role to..." node
    Then I should see "Tutor"
    And I should see "Learner"
    And I follow "Participants"
    And the "roleid" select box should contain "Tutor"
    And the "roleid" select box should contain "Learner"
    And the "roleid" select box should not contain "Student"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Your word for 'Non-editing teacher' | |
      | Your word for 'Student' | |
    And I press "Save changes"
    And I expand "Switch role to..." node
    And I should see "Teacher"
    And I should see "Student"
    And I should not see "Learner"
    And I follow "Participants"
    And the "roleid" select box should contain "Non-editing teacher"
    And the "roleid" select box should contain "Student"
