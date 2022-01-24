@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Sort content in the content bank
  In order to temporarily organise the content of the content bank
  As an admin
  I need to be able to sort the content bank in various ways

  Background:
    Given I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And the following "users" exist:
      | username    | firstname | lastname | email              |
      | manager     | Max       | Manager  | man@example.com    |
    And the following "role assigns" exist:
      | user        | role      | contextlevel  | reference     |
      | manager     | manager       | System    |               |
    And the following "contentbank content" exist:
        | contextlevel | reference | contenttype       | user    | contentname          | filepath                                |
        | System       |           | contenttype_h5p   | admin   | Dragon_santjordi.h5p | /h5p/tests/fixtures/filltheblanks.h5p   |
        | System       |           | contenttype_h5p   | admin   | mathsbook.h5p        | /h5p/tests/fixtures/filltheblanks.h5p   |
        | System       |           | contenttype_h5p   | manager | historybook.h5p      | /h5p/tests/fixtures/filltheblanks.h5p   |
        | System       |           | contenttype_h5p   | admin   | santjordi.h5p        | /h5p/tests/fixtures/filltheblanks.h5p   |
        | System       |           | contenttype_h5p   | admin   | santjordi_rose.h5p   | /h5p/tests/fixtures/filltheblanks.h5p   |
        | System       |           | contenttype_h5p   | admin   | SantJordi_book       | /h5p/tests/fixtures/filltheblanks.h5p   |

  Scenario: Admins can order content in the content bank
    Given I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
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

  Scenario: Admins can order content depending on the author
    Given I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    When I click on "Display content bank with file details" "button"
    Then I click on "Sort by Author ascending" "button"
    And "Dragon_santjordi.h5p" "text" should appear before "historybook.h5p" "text"
    And "santjordi_rose.h5p" "text" should appear before "historybook" "text"
    And I click on "Sort by Author descending" "button"
    And "historybook.h5p" "text" should appear before "Dragon_santjordi.h5p" "text"
    And "historybook.h5p" "text" should appear before "santjordi_rose" "text"
