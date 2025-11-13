@ou @ou_vle @qtype @qtype_oumultiresponse
Feature: Test editing  an OU multiple response question
  As a teacher
  In order to be able to update my OU multiple response question
  I need to edit them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype           | name                             | template    |
      | Test questions   | oumultiresponse | OU multiple response for editing | two_of_four |

  @javascript
  Scenario: Edit an OU multiple response question
    When I am on the "OU multiple response for editing" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
