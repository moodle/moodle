@qtype @qtype_essay
Feature: In an essay question, let the question author choose the min/max number of words for input text
  In order to constrain student submissions for marking
  As a teacher
  I need to choose the appropriate minimum and/or maximum number of words for input text

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name          | template | minwordlimit | maxwordlimit |
      | Test questions   | essay | essay-min-max | editor   | 0            | 0            |

  @javascript
  Scenario: Minimum/Maximum word limit are enabled but not set.
    When I am on the "essay-min-max" "core_question > edit" page logged in as teacher
    And I set the field "minwordenabled" to "1"
    And I click on "Save changes" "button"
    Then I should see "Minimum word limit is enabled but is not set"

  @javascript
  Scenario: Minimum/Maximum word limit cannot be set to a negative number.
    When I am on the "essay-min-max" "core_question > edit" page logged in as teacher
    And I set the field "minwordenabled" to "1"
    And I set the field "id_minwordlimit" to "-10"
    And I click on "Save changes" "button"
    Then I should see "Minimum word limit cannot be a negative number"

  @javascript
  Scenario: Maximum word limit cannot be greater than minimum word limit.
    When I am on the "essay-min-max" "core_question > edit" page logged in as teacher
    And I set the field "minwordenabled" to "1"
    And I set the field "id_minwordlimit" to "500"
    And I set the field "maxwordenabled" to "1"
    And I set the field "id_maxwordlimit" to "450"
    And I click on "Save changes" "button"
    Then I should see "Maximum word limit must be greater than minimum word limit"

  @javascript
  Scenario: Modify the question to see 'Minimum word limit' and  'Maximum word limit' are hidden when 'Require text' field is set to 'Text input is optional'
    When I am on the "essay-min-max" "core_question > edit" page logged in as teacher
    And I should see "Minimum word limit"
    And I should see "Maximum word limit"
    And I set the field "Require text" to "Text input is optional"
    Then I should not see "Minimum word limit"
    And I should not see "Minimum word limit"

  @javascript
  Scenario: Minimum/Maximum word limit can be unset after being set.
    When I am on the "essay-min-max" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | minwordenabled  | 1   |
      | id_minwordlimit | 100 |
      | maxwordenabled  | 1   |
      | id_maxwordlimit | 200 |
    And I click on "Save changes and continue editing" "button"
    Then the following fields match these values:
      | minwordenabled  | 1   |
      | id_minwordlimit | 100 |
      | maxwordenabled  | 1   |
      | id_maxwordlimit | 200 |
    And I set the following fields to these values:
      | minwordenabled  | 0 |
      | maxwordenabled  | 0 |
    And I click on "Save changes and continue editing" "button"
    And the following fields match these values:
      | minwordenabled  | 0 |
      | id_minwordlimit |   |
      | maxwordenabled  | 0 |
      | id_maxwordlimit |   |
