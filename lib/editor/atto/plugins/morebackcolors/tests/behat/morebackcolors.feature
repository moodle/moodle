@editor @editor_atto @atto @atto_morebackcolors
Feature: Atto more font background colours button
  To format text in Atto, I need to spray random colours all over my text like some maniacal Monet.

  Background:
    # Set up toolbar to add this button
    Given the following config values are set as admin:
      | toolbar         | style1 = bold, morebackcolors | editor_atto         |
      | availablecolors | #123456 #654321               | atto_morebackcolors |

  @javascript
  Scenario: Change colour of some text
    # Go to an Atto editor
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Edit profile"

    # Set up initial text and select it
    And I set the field "Description" to "Water lillies"
    And I select the text in the "Description" Atto editor

    # Click button and check the menu appears and lists both colours
    When I click on ".atto_morebackcolors_button" "css_element"
    Then ".atto_morebackcolors_button.atto_menu" "css_element" should be visible
    And "//div[@data-color='#123456']" "xpath_element" should exist in the ".atto_morebackcolors_button.atto_menu" "css_element"
    And "//div[@data-color='#654321']" "xpath_element" should exist in the ".atto_morebackcolors_button.atto_menu" "css_element"

    # Click on a menu option, save it and verify the HTML code was updated to add the colour.
    When I click on "//div[@data-color='#123456']" "xpath_element"
    And I press "Update profile"
    Then "//span[normalize-space(.)='Water lillies' and contains(@style, '18,52,86')]" "xpath_element" should exist

