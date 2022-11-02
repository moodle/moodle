@core @core_quiz @core_customfield @qbank_customfields @javascript
Feature: The visibility of question custom fields control where they are displayed
  In order to display custom fields in a quiz

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "custom field categories" exist:
      | name              | component          | area     | itemid |
      | Category for test | qbank_customfields | question | 0      |
    And the following "custom fields" exist:
      | name    | category          | type | shortname | configdata                                    |
      | Field 1 | Category for test | text | f1        | {"visibility":"2"}                            |
      | Field 2 | Category for test | text | f2        | {"visibility":"2"}                            |
      | Field 3 | Category for test | text | f3        | {"visibility":"2","defaultvalue":"secret"}    |
    And the following "activity" exists:
      | activity | quiz                  |
      | course   | C1                    |
      | idnumber | 00001                 |
      | name     | Test quiz name        |
      | intro    | Test quiz description |
      | section  | 1                     |
      | grade    | 10                    |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "True/False" question to the "Test quiz name" quiz with:
      | Question name                      | First question                          |
      | Question text                      | Answer the first question               |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I am on the "Test quiz name" "mod_quiz > question bank" page

  @javascript
  Scenario: Display custom question fields to teachers based on their visibility.
    When I choose "Edit question" action for "First question" in the question bank
    And I should see "Category for test"
    And I click on "Expand all" "link"
    And I should see "Field 1"
    And I should see "Field 2"
    And I should see "Field 3"
    And I set the following fields to these values:
      | Field 1 | custom field text one|
      | Field 2 | custom field text two|
    And I press "id_submitbutton"
    And I should see "Field 1"
    And I should see "custom field text one"
    And I should see "Field 2"
    And I should see "custom field text two"
    And I should see "Field 3"
    And I should see "secret"
    And I choose "Preview" action for "First question" in the question bank
    And I should see "Field 1"
    And I should see "custom field text one"
    And I should see "Field 2"
    And I should see "custom field text two"
    And I should see "Field 3"
    Then I should see "secret"
