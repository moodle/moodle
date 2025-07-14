@core @editor_tiny @javascript
Feature: A user can insert script tag in TinyMCE using the default TinyMCE functionalities.

  Scenario: Allow script elements in the editor with the additional HTML plugin disabled.
    Given the following config values are set as admin:
      | config   | value | plugin |
      | disabled | 1     | tiny_html |
    And I am on the "Profile advanced editing" page logged in as "admin"
    And I set the field "Description" to "<p><script>alert('script in tiny');</script></p>"
    When I click on the "Tools > Source code" menu item for the "Description" TinyMCE editor
    And I click on "Save" "button"
    Then the field "Description" matches expression "@<script>.*alert\('script in tiny'\);.*</script>@s"
