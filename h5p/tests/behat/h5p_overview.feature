@editor @core_h5p
Feature: Check H5P tools information is correct

  @javascript
  Scenario: Display H5P filter information.
    Given I log in as "admin"
    When I navigate to "H5P > H5P overview" in site administration
    Then I should see "Enable" in the "Display H5P filter" "table_row"
    And I click on "Display H5P filter" "link"
    And I set the field "newstate" in the "Display H5P" "table_row" to "Off, but available"
    And I navigate to "H5P > H5P overview" in site administration
    And I should see "Off, but available" in the "Display H5P filter" "table_row"

  @javascript
  Scenario: 'Download available H5P content types from h5p.org' scheduled task.
    Given I log in as "admin"
    When I navigate to "H5P > H5P overview" in site administration
    Then I should see "Enable" in the "H5P scheduled task" "table_row"
    And I click on "H5P scheduled task" "link"
    And I set the field "disabled" to "1"
    And I click on "Save changes" "button"
    And I navigate to "H5P > H5P overview" in site administration
    And I should see "Disable" in the "H5P scheduled task" "table_row"

  @javascript
  Scenario: H5P atto button.
    Given I log in as "admin"
    When I navigate to "H5P > H5P overview" in site administration
    Then I should see "Enable" in the "Insert H5P button" "table_row"
    And I click on "Insert H5P button" "link"
    And I set the field "Toolbar config" to "style1 = title, bold, italic"
    And I click on "Save changes" "button"
    When I navigate to "H5P > H5P overview" in site administration
    Then I should see "Disable" in the "Insert H5P button" "table_row"
