@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Use atto to open non-formula images
In order to assert that MathType does not override the
behavior of non-formula images.
As an admin
I need to display the settings of a created non-formula images
when it is opened.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin  | C1     | editingteacher |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Post a chemistry formula
    # Set enabled plugins.
    And I navigate to "Site administration" in site administration
    And I follow "Site security settings"
    And I check enable trusted content
    And I press "Save changes"
    And I navigate to "Plugins" in site administration
    And I follow "Atto toolbar settings"
    And I set the field "Toolbar config" to multiline:
    """
    files = image
    math = wiris
    """
    And I press "Save changes"
    # Course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle chemistry formulas |
    # Insert formula.
    And I press "ChemType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><mi mathvariant="normal">H</mi><mn>2</mn><mi mathvariant="normal">O</mi></math>'
    And I press accept button in MathType Editor
    # Insert non-formula image.
    And I select the text in the "Page content" Atto editor
    And I click on "Insert or edit image" "button"
    And I set the field "Enter URL" to "https://i.ytimg.com/vi/MPV2METPeJU/maxresdefault.jpg"
    And I set the field "Describe this image for someone who cannot see it" to "Dog"
    And I click on "Save image" "button"
    # Assert that dbClick works
    And I dbClick on image with alt equals to "Dog"
    Then the field "Enter URL" matches value "https://i.ytimg.com/vi/MPV2METPeJU/maxresdefault.jpg"
    And I click on "Save image" "button"
    # Assert modal window
    And I dbClick on image with alt equals to "straight H 2 straight O"
    Then modal window is opened
