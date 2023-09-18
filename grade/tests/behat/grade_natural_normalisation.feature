@core @core_grades
Feature: We can use natural aggregation and weights will be normalised to a total of one hundred
  In order to override weights
  As a teacher
  I need to add assessments to the gradebook.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory | grade |
      | assign | C1 | a5 | Test assignment five | Submit something! | Sub category 1 | 20 |
      | assign | C1 | a6 | Test assignment six | Submit something! | Sub category 1 | 10 |
      | assign | C1 | a7 | Test assignment seven | Submit nothing! | Sub category 1 | 15 |
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"

  @javascript
  Scenario: Setting all weights in a category to exactly one hundred in total.

    And the field "Weight of Test assignment five" matches value "44.444"
    And the field "Weight of Test assignment six" matches value "22.222"
    And the field "Weight of Test assignment seven" matches value "33.333"
    When I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Override weight of Test assignment seven" to "1"
    And I set the field "Weight of Test assignment five" to "30"
    And I set the field "Weight of Test assignment six" to "50"
    And I set the field "Weight of Test assignment seven" to "20"
    And I press "Save changes"

    Then I should not see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "30.0"
    And the field "Weight of Test assignment six" matches value "50.0"
    And the field "Weight of Test assignment seven" matches value "20.0"

  @javascript
  Scenario: Setting all weights in a category to less than one hundred is normalised.

    When I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Override weight of Test assignment seven" to "1"
    And I set the field "Weight of Test assignment five" to "1"
    And I set the field "Weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment seven" to "2"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "25.0"
    And the field "Weight of Test assignment six" matches value "25.0"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Set one of the grade item weights to a figure over one hundred.

    When I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Weight of Test assignment five" to "120"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "100.0"
    And the field "Weight of Test assignment six" matches value "0.0"
    And the field "Weight of Test assignment seven" matches value "0.0"

  @javascript
  Scenario: Setting several but not all grade item weights to over one hundred each.

    When I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "150"
    And I set the field "Weight of Test assignment six" to "150"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "50.000"
    And the field "Weight of Test assignment six" matches value "50.000"
    And the field "Weight of Test assignment seven" matches value "0.0"

  @javascript
  Scenario: Grade items weights are not normalised when all grade item weights are overridden (sum exactly 100). Extra credit is set respectful to number of items.

    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "60"
    And I set the field "Weight of Test assignment six" to "40"
    And I press "Save changes"

    Then I should not see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "60.000"
    And the field "Weight of Test assignment six" matches value "40.000"
    And the field "Weight of Test assignment seven" matches value "50.0"
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Grade items weights are normalised when all grade item weights are overridden (sum over 100). Extra credit is set respectful to number of items.

    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "60"
    And I set the field "Weight of Test assignment six" to "50"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "54.545"
    And the field "Weight of Test assignment six" matches value "45.455"
    And the field "Weight of Test assignment seven" matches value "50.0"
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Grade items weights are normalised when all grade item weights are overridden (sum under 100). Extra credit is set respectful to number of items.

    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "40"
    And I set the field "Weight of Test assignment six" to "30"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "57.143"
    And the field "Weight of Test assignment six" matches value "42.857"
    And the field "Weight of Test assignment seven" matches value "50.0"
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Grade items weights are normalised when not all grade item weights are overridden. Extra credit is set respectful to number of items.

    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Weight of Test assignment five" to "40"
    And I press "Save changes"

    Then I should see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "40.00"
    And the field "Weight of Test assignment six" matches value "60.000"
    And the field "Weight of Test assignment seven" matches value "50.0"
    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: The extra credit grade item weight is overridden to a figure over one hundred and then
  the grade item is set to normal.

    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment seven" to "1"
    And I set the field "Weight of Test assignment seven" to "105"
    And I press "Save changes"
    Then I should not see "Your weights have been adjusted to total 100."
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "105.0"
    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 0 |
    And I should see "Your weights have been adjusted to total 100."

    And the field "Weight of Test assignment five" matches value "0.0"
    And the field "Weight of Test assignment six" matches value "0.0"
    And the field "Weight of Test assignment seven" matches value "100.0"

  @javascript
  Scenario: The extra credit grade item weight is overridden to a figure over one hundred and then
  the grade category is reset.

    When I set the following settings for grade item "Test assignment seven" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment seven" to "1"
    And I set the field "Weight of Test assignment seven" to "105"
    And I press "Save changes"

    And I reset weights for grade category "Sub category 1"
    And the field "Weight of Test assignment five" matches value "66.667"
    And the field "Weight of Test assignment six" matches value "33.333"
    And the field "Weight of Test assignment seven" matches value "50.0"

  @javascript
  Scenario: Two out of three grade items weights are overridden and one is not.
  The overridden grade item weights total over one hundred.

    Given I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Override weight of Test assignment seven" to "1"
    And I set the field "Weight of Test assignment six" to "55"
    And I set the field "Weight of Test assignment seven" to "65"
    And I press "Save changes"
    And I should see "Your weights have been adjusted to total 100."

    Then the field "Weight of Test assignment five" matches value "0.0"
    And the field "Weight of Test assignment six" matches value "45.833"
    And the field "Weight of Test assignment seven" matches value "54.167"

  @javascript
  Scenario: With one grade item set as extra credit, when I reset the weights for a category they return to the natural weights.
    When I set the following settings for grade item "Test assignment five" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Override weight of Test assignment seven" to "1"
    And I set the field "Weight of Test assignment six" to "55"
    And I set the field "Weight of Test assignment seven" to "40"
    And I press "Save changes"
    And I reset weights for grade category "Sub category 1"
    Then the field "Weight of Test assignment five" matches value "80.0"
    And the field "Weight of Test assignment six" matches value "40.0"
    And the field "Weight of Test assignment seven" matches value "60.0"

  @javascript
  Scenario: Overriding a grade item with a negative value results in the value being changed to zero.
    When I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Weight of Test assignment five" to "-15"
    And I press "Save changes"
    Then the field "Weight of Test assignment five" matches value "0.0"
    And the field "Weight of Test assignment six" matches value "40.0"
    And the field "Weight of Test assignment seven" matches value "60.0"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment six" to "-25"
    And I press "Save changes"
    And the field "Weight of Test assignment six" matches value "0.0"
    And the field "Weight of Test assignment seven" matches value "100.0"
    And I reset weights for grade category "Sub category 1"
    And I set the field "Override weight of Test assignment five" to "1"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment five" to "-10"
    And I set the field "Weight of Test assignment six" to "120"
    And I press "Save changes"
    And the field "Weight of Test assignment five" matches value "0.0"
    And the field "Weight of Test assignment six" matches value "100.0"
    And the field "Weight of Test assignment seven" matches value "0.0"
