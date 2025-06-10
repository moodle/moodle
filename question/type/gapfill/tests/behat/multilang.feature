@core @qtype @qtype_gapfill @qtype_gapfill_multilang  @_switch_window
Feature: Test the queiton type works with multilanguage filters
  create and preview gapfill questions.

  Background:
    Given the following "users" exist:
        | username | firstname | lastname | email               |
        | teacher1 | Mark      | Allright | teacher@example.com |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
        | user     | course | role           |
        | teacher1 | C1     | editingteacher |
    And the "multilang2" filter is "on"
    And the "multilang" filter is "on"

  @javascript
  Scenario: Create a question and check mlang2 works as expected
    When the filter_multilang2 plugin is installed
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    # Create a new question with mlang2 tags
    # Then check that the french words are not displayed
    And I add a "Gapfill" question filling the form with:
        | Question name | Gapfill-001                                                    |
        | Question text | The {mlang fr}chat{mlang}{mlang en}cat{mlang} sat on the [mat] |
    Then I should see "Gapfill-001"

  # Preview it.
    And I am on the "Gapfill-001" "core_question > preview" page
    And I should see "cat"
    And I should not see "chat"

  @javascript
  Scenario: Create a question and check (core) mlang works as expected
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    And I add a "Gapfill" question filling the form with:
        | Question name | Gapfill-002                                                                                                    |
        | Question text | The <span lang="en" class="multilang">cat</span><span lang="fr" class="multilang">chat</span> sat on the [mat] |
    Then I should see "Gapfill-002"
    #Check that the french words are not displayed
    And I am on the "Gapfill-002" "core_question > preview" page

    And I should see "cat"
    And I should not see "chat"
