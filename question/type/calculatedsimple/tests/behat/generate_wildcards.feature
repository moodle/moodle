@qtype @qtype_calculatedsimple
Feature: Test creating sets of wildcard values
  As a teacher
  In order to be able to update my calculated simple questions
  I need to be able to create and re-create sets of wildcard values

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
    And the following "user preferences" exist:
      | user    | preference | value    |
      | teacher | htmleditor | textarea |

  Scenario: Add a calculated simple question and re-generate different wild card values
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Calculated simple" question filling the form with:
      | Question name         | calculatedsimple-001                |
      | Question text         | {x1} + {x2}                         |
      | Answer 1 formula =    | {x1} + {x2}                         |
      | Grade                 | 100%                                |
    And I should see "There must be at least one wild card"
    And I press "id_analyzequestion"
    And I should see "You must add at least one set of wild card(s) values before you can save this question."
    # Creating one set of wild card values by default.
    And I press "id_addbutton"
    And I should see "Wild card(s) values"
    # As the wild card values are random edit them to some known values.
    And I set the following fields to these values:
      | number[2]    | 3.5  |
      | number[1]    | 5.3  |
    And I press "id_updatedatasets"
    # Save changes and continue editing.
    And I press "id_updatebutton"
    Then I should see "3.5 + 5.3 = 8.80"
    And I should see "Correct answer : 8.80 inside limits of true value"
    # Now generate a new set of wild card values.
    And I press "id_addbutton"
    # Save changes and continue editing.
    And I press "id_updatebutton"
    # Make sure the old values are no longer stored.
    Then I should not see "3.5 + 5.3 = 8.80"
    And I should not see "Correct answer : 8.80 inside limits of true value"
