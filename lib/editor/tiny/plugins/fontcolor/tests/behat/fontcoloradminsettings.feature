@editor @editor_tiny @tiny @tiny_fontcolor
Feature: Tiny editor admin settings for text and background color
  To be able to choose colors in the tiny editor, I need to setup some colors in the admin settings.

  @javascript
  Scenario: Set few colors in the admin settings for text color.
    Given I log in as "admin"
    When I navigate to "Plugins > Text editors > Tiny text colour/text background colour settings" in site administration
    And I click on "+" "button"
    And I set the field "s_tiny_fontcolor_textcolors_name_1" to "Black"
    And I set the field "s_tiny_fontcolor_textcolors_value_1" to "#000000"
    And I set the field "s_tiny_fontcolor_textcolors_name_2" to "White"
    And I set the field "s_tiny_fontcolor_textcolors_value_2" to "#ffffff"
    And I click on "Save changes" "button"
    And I open my profile in edit mode
    And I wait until the page is ready
    And I click on the "Format" menu item for the "Description" TinyMCE editor
    Then I should see "Text foreground colour"
    And I should not see "Text background colour"
