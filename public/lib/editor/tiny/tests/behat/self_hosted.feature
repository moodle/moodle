@core @editor_tiny @javascript
Feature: An administrator can make Moodle to use self-hosted TinyMCE

  Scenario: An administrator can make Moodle to use self-hosted TinyMCE
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    And I should see "TinyMCE source"
    And I should not see "TinyMCE path"
    And I should see "Built-in" in the "TinyMCE source" "select"
    And I should see "Self-hosted" in the "TinyMCE source" "select"
    When I set the field "TinyMCE source" to "Self-hosted"
    Then I should see "TinyMCE path"
    And I set the field "TinyMCE path" to "lib/abc"
    And I click on "Save changes" "button"
    And I should see "Some settings were not changed due to an error."
    And I should see "TinyMCE files not found. Make sure the path is correct and the folder includes tinymce.min.js."
    And I set the field "TinyMCE path" to "lib/editor/tiny/js/tinymce"
    And I click on "Save changes" "button"
    And I should not see "TinyMCE files not found. Make sure the path is correct and the folder includes tinymce.min.js."
