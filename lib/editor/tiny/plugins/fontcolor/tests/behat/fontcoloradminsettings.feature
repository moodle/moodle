@editor @editor_tiny @tiny @tiny_fontcolor @javascript
Feature: Tiny editor admin settings for text and background color
  To be able to choose colors in the tiny editor, I need to setup some colors in the admin settings.

  Scenario: Set few colors in the admin settings for text color.
    Given I log in as "admin"
    When I navigate to "Plugins > Text editors > Tiny text colour/text background colour settings" in site administration
    And I click on "+" "button"
    And I set the field "s_tiny_fontcolor_textcolors_name_1" to "Black"
    And I set the field "s_tiny_fontcolor_textcolors_value_1" to "#000000"
    And I set the field "s_tiny_fontcolor_textcolors_name_2" to "White"
    And I set the field "s_tiny_fontcolor_textcolors_value_2" to "#ffffff"
    And I click on "+" "button"
    And I set the field "s_tiny_fontcolor_textcolors_name_3" to "Magenta blur"
    And I set the field "s_tiny_fontcolor_textcolors_value_3" to "#FF00FF69"
    And I click on "Save changes" "button"
    And I open my profile in edit mode
    And I wait until the page is ready
    And I click on the "Format" menu item for the "Description" TinyMCE editor
    Then I should see "Text foreground colour"
    And I should not see "Text background colour"

  Scenario: Set few colors in the admin settings for background color.
    Given I log in as "admin"
    When I navigate to "Plugins > Text editors > Tiny text colour/text background colour settings" in site administration
    And I click on "+" "button" in the ".s_tiny_fontcolor_backgroundcolors" "css_element"
    And I set the field "s_tiny_fontcolor_backgroundcolors_name_1" to "Black"
    And I set the field "s_tiny_fontcolor_backgroundcolors_value_1" to "#000000"
    And I set the field "s_tiny_fontcolor_backgroundcolors_name_2" to "Gold"
    And I set the field "s_tiny_fontcolor_backgroundcolors_value_2" to "#DBAD14FF"
    And I click on "Save changes" "button"
    And I open my profile in edit mode
    And I wait until the page is ready
    And I click on the "Format" menu item for the "Description" TinyMCE editor
    Then I should not see "Text foreground colour"
    And I should see "Text background colour"

  Scenario: Set colors in the admin settings and check for errors.
    Given I log in as "admin"
    When I navigate to "Plugins > Text editors > Tiny text colour/text background colour settings" in site administration
    And I set the field "s_tiny_fontcolor_backgroundcolors_value_1" to "#000000"
    And I click on "Save changes" "button"
    Then I should see "Some settings were not changed due to an error."
    When I set the field "s_tiny_fontcolor_backgroundcolors_name_1" to "Black"
    And I set the field "s_tiny_fontcolor_backgroundcolors_value_1" to ""
    And I click on "Save changes" "button"
    Then I should see "Some settings were not changed due to an error."
    When I set the field "s_tiny_fontcolor_backgroundcolors_name_1" to "Black"
    And I set the field "s_tiny_fontcolor_backgroundcolors_value_1" to "#000000"
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
