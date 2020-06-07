@core @core_contentbank @contentbank_h5p @javascript
Feature: Sort content in the content bank
  In order to temporarily organise the content of the content bank
  As an admin
  I need to be able to sort the content bank in various ways

  Background:
    Given the following "contentbank content" exist:
        | contextlevel | reference | contenttype       | user  | contentname          |
        | System       |           | contenttype_h5p   | admin | Dragon_santjordi.h5p |
        | System       |           | contenttype_h5p   | admin | mathsbook.h5p        |
        | System       |           | contenttype_h5p   | admin | historybook.h5p      |
        | System       |           | contenttype_h5p   | admin | santjordi.h5p        |
        | System       |           | contenttype_h5p   | admin | santjordi_rose.h5p   |
        | System       |           | contenttype_h5p   | admin | SantJordi_book       |

  Scenario: Admins can order content in the content bank
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    When I click on "Display content bank with file details" "button"
    And I click on "Sort by Content name ascending" "button"
    And "Dragon_santjordi.h5p" "text" should appear before "historybook.h5p" "text"
    And "historybook.h5p" "text" should appear before "mathsbook.h5p" "text"
    And "SantJordi_book" "text" should appear before "santjordi_rose.h5p" "text"
    And I click on "Sort by Content name descending" "button"
    And "historybook.h5p" "text" should appear before "Dragon_santjordi.h5p" "text"
    And "mathsbook.h5p" "text" should appear before "historybook.h5p" "text"
    Then "santjordi_rose.h5p" "text" should appear before "SantJordi_book" "text"
