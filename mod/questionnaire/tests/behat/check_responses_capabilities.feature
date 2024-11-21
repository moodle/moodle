@mod @mod_questionnaire
Feature: Review responses with different capabilities
  In order to review and manage questionnaire responses
  As a user
  I need proper capabilities to access the view responses features

  @javascript
  Scenario: A teacher with mod/questionnaire:readallresponseanytime can see all responses.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability           | permission |
      | mod/questionnaire:readallresponseanytime | Allow |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 |
    And "Test questionnaire" has questions and responses
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    Then I should see "View all responses"
    And I navigate to "View all responses" in current page administration
    Then I should see "View all responses."
    And I should see "All participants."
    And I should see "View Default order"
    And I should see "Responses: 6"
    And I log out

  @javascript
  Scenario: A teacher denied mod/questionnaire:readallresponseanytime cannot see all responses.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability           | permission |
      | mod/questionnaire:readallresponseanytime | Prohibit |
      | mod/questionnaire:readallresponses | Allow |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 |
    And "Test questionnaire" has questions and responses
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    Then I should not see "View all responses"
    And I log out

  @javascript
  Scenario: A teacher with mod/questionnaire:readallresponses can see responses after appropriate time rules.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability           | permission |
      | mod/questionnaire:readallresponseanytime | Prohibit |
      | mod/questionnaire:readallresponses | Allow |
    And the following "activities" exist:
      | activity | name | description | course | idnumber | resp_view |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 | 0 |
      | questionnaire | Test questionnaire 2 | Test questionnaire 2 description | C1 | questionnaire2 | 3 |
    And "Test questionnaire" has questions and responses
    And "Test questionnaire 2" has questions and responses
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    Then I should not see "View all responses"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire 2"
    Then I should see "View all responses"
    And I log out
