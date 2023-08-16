@core @editor_tiny @source_code @javascript
Feature: A user can insert script tag in TinyMCE using the default TinyMCE functionalities.

  Scenario: Allow script elements in the editor with the additional HTML plugin disabled.
    Given the following config values are set as admin:
      | config   | value | plugin |
      | disabled | 1     | tiny_html |
    And I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Tools > Source code" menu item for the "Description" TinyMCE editor
    Then I set the field with xpath "//textarea[@class='tox-textarea']" to "<p><script>alert('script in tiny');</script></p>"
    And I click on "Save" "button"
    When I click on the "Tools > Source code" menu item for the "Description" TinyMCE editor
    Then the field with xpath "//textarea[@class='tox-textarea']" matches value "<p><script>alert('script in tiny');</script></p>"
