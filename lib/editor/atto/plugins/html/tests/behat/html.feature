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

