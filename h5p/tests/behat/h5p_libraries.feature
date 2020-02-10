@editor @core_h5p @_file_upload
Feature: Upload and list H5P libraries and content types installed

  @javascript
  Scenario: No library installed in new installations.
    Given I log in as "admin"
    When I navigate to "H5P > Manage H5P content types" in site administration
    Then I should see "Upload H5P content types"
    And I should not see "Installed H5P"

  @javascript
  Scenario: Upload an invalid content type.
    Given I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    When I upload "h5p/tests/fixtures/h5ptest.zip" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I wait until the page is ready
    Then I should see "Invalid H5P content type"
    And I should not see "Installed H5P"

  @javascript
  Scenario: Upload a valid content type.
    Given I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    When I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I wait until the page is ready
    Then I should see "H5P content types uploaded successfully"
    And I should see "Installed H5P"
    And I should see "Installed H5P content types"
    And I should see "Fill in the Blanks"
    And I should not see "Essay"
    And I should see "Installed H5P libraries"
    And I click on "Installed H5P libraries" "link"
    And I should see "Question"
    And I should see "1.4" in the "Question" "table_row"
    And I should not see "1.3" in the "Question" "table_row"
    And I upload "h5p/tests/fixtures/essay.zip" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I wait until the page is ready
#   Existing content types are kept and new added
    And I should see "Fill in the Blanks"
    And I should see "Essay"
    And I click on "Installed H5P libraries" "link"
    And I should see "1.3" in the "Question" "table_row"
    And I should see "1.4"
