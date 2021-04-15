@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Search content in the content bank
  In order to find easily content in the content bank
  As an admin
  I need to be able to search content in the content bank

  Background:
    Given I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname          | filepath                                |
      | System       |           | contenttype_h5p | admin    | santjordi.h5p        | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | santjordi_rose.h5p   | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | SantJordi_book       | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | Dragon_santjordi.h5p | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | princess.h5p         | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | mathsbook.h5p        | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | historybook.h5p      | /h5p/tests/fixtures/filltheblanks.h5p   |
      | System       |           | contenttype_h5p | admin    | santvicenc.h5p       | /h5p/tests/fixtures/filltheblanks.h5p   |

  Scenario: Admins can search content in the content bank
    Given I am on site homepage
    And I turn editing mode on
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should see "santjordi.h5p"
    And "Clear search input" "button" should not be visible
    And I should not see "items found"
    When I set the field "Search" to "book"
    # Waiting for the animation to show the button to finish.
    And I wait "1" seconds
    Then "Clear search input" "button" should be visible
    And I should see "3 items found"
    And I should see "SantJordi_book"
    And I should see "mathsbook.h5p"
    And I should see "historybook.h5p"
    And I set the field "Search" to "sant"
    And "Clear search input" "button" should be visible
    And I should see "5 items found"
    And I set the field "Search" to "santjordi"
    And I should see "4 items found"
    And I should see "santjordi.h5p"
    And I should see "santjordi_rose.h5p"
    And I should see "SantJordi_book"
    And I should see "Dragon_santjordi.h5p"
    And I click on "Clear search input" "button"
    # Waiting for the animation to hide the button to finish.
    And I wait "1" seconds
    And "Clear search input" "button" should not be visible
    And I should not see "items found"
    And I set the field "Search" to ".h5p"
    # Waiting for the animation to show the button to finish.
    And I wait "1" seconds
    And "Clear search input" "button" should be visible
    And I should see "7 items found"
    And I set the field "Search" to "friend"
    And I should see "0 items found"
