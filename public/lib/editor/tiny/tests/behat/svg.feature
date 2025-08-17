@editor @editor_tiny @javascript
Feature: Use SVG elements in TinyMCE
  In order to use SVG elements in TinyMCE
  As a user
  I need to be able to specify how they appear

  Scenario: SVG attributes are respected when saving source code
    Given I am on the "Profile advanced editing" page logged in as "admin"
    When I set the field "Description" to multiline:
    """
    <svg width="200" height="200">
      <rect width="100" height="100" x="50" y="50" fill="red" />
    </svg>
    """
    And I click on the "Tools > Source code" menu item for the "Description" TinyMCE editor
    And I click on "Save" "button"
    Then the field "Description" matches expression "#<svg width=\"200\" height=\"200\">.*<rect[^>]+>.*</svg>#s"
