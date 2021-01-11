@qtype @qtype_essay
Feature: In an essay question, let the question author choose the min/max number of words for input text
In order to constrain student submissions for marking
As a teacher
I need to choose the appropriate minimum and/or maximum number of words for input text
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name          | template | minwordlimit | maxwordlimit |
      | Test questions   | essay | essay-min-max | editor   | null         | null         |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript
  Scenario: Minimum/Maximum word limit are enabled but not set.
    Given I choose "Edit question" action for "essay-min-max" in the question bank
    When I set the field "minwordenabled" to "1"
    And I click on "Save changes" "button"
    Then I should see "Minimum word limit is enabled but is not set"

  @javascript
  Scenario: Minimum/Maximum word limit cannot be set to a negative number.
    Given I choose "Edit question" action for "essay-min-max" in the question bank
    And I set the field "minwordenabled" to "1"
    When I set the field "id_minwordlimit" to "-10"
    And I click on "Save changes" "button"
    Then I should see "Minimum word limit cannot be a negative number"

  @javascript
  Scenario: Maximum word limit cannot be greater than minimum word limit.
    Given I choose "Edit question" action for "essay-min-max" in the question bank
    And I set the field "minwordenabled" to "1"
    And I set the field "id_minwordlimit" to "500"
    And I set the field "maxwordenabled" to "1"
    When I set the field "id_maxwordlimit" to "450"
    And I click on "Save changes" "button"
    Then I should see "Maximum world limit must be greater than minimum word limit"

  @javascript
  Scenario: Modify the question to see 'Minimum word limit' and  'Maximum word limit' are hidden when 'Require text' field is set to 'Text input is optional'
    Given I choose "Edit question" action for "essay-min-max" in the question bank
    And I should see "Minimum word limit"
    And I should see "Maximum word limit"
    When I set the field "Require text" to "Text input is optional"
    Then I should not see "Minimum word limit"
    And I should not see "Minimum word limit"
