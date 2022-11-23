@editor @editor_atto @atto @atto_html
Feature: Atto edit HTML
  To write advanced HTML, I need to edit the HTML source code

  @javascript
  Scenario: Edit the html source
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "<p style=\"color: blue;\">Smurf</p>"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then the field "Description" matches value "<p style=\"color: blue;\">Smurf</p>"

  @javascript
  Scenario: Validation of empty string when the form is submitted with HTML source mode.
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name         | questiontext                  |
      | Test questions   | essay | Essay 01 new | Write about whatever you want |
    And I am on the "Essay 01 new" "core_question > edit" page logged in as admin
    And I click on "Show more buttons" "button" in the "Question text" "form_row"
    And I click on "HTML" "button" in the "Question text" "form_row"
    And I press the shift + end key
    And I press the delete key
    When I press "id_submitbutton"
    Then I should see "You must supply a value here." in the "Question text" "form_row"
