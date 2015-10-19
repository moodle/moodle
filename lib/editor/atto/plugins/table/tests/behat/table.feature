@editor @editor_atto @atto @atto_table @_bug_phantomjs
Feature: Atto tables
  To format text in Atto, I need to create tables

  @javascript
  Scenario: Create a table
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    And I set the field "Caption" to "Dinner"
    And I press "Create table"
    And I press "Save changes"
    Then ".blog_entry table caption" "css_element" should be visible

  @javascript
  Scenario: Edit a table
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    And I click on "Edit table" "link"
    And I set the field "Caption" to "Dinner"
    And I press "Update table"
    And I press "Save changes"
    Then ".blog_entry table caption" "css_element" should be visible

  @javascript
  Scenario: Create a table with background colour and width with border settings off
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 0 | atto_table |
    | allowborderstyles | 0 | atto_table |
    | allowbordersize | 0 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I press "Create table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"background-color:#FFFFFF;width:100%;\""

  @javascript
  Scenario: Edit a table with background colour and width with border settings off
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 0 | atto_table |
    | allowborderstyles | 0 | atto_table |
    | allowbordersize | 0 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    When I click on "Edit table" "link"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I press "Update table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"width:100%;background-color:rgb(255,255,255);\""

  @javascript
  Scenario: Create a table with background colour and width with borders on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 0 | atto_table |
    | allowbordersize | 0 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I press "Create table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:1px solid #FFFFFF;background-color:#FFFFFF;width:100%;\""

  @javascript
  Scenario: Edit a table with background colour and width with borders on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 0 | atto_table |
    | allowbordersize | 0 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    When I click on "Edit table" "link"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I press "Update table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:1px solid rgb(255,255,255);width:100%;background-color:rgb(255,255,255);\""

  @javascript
  Scenario: Create a table with background colour and width with borders and border styling on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 1 | atto_table |
    | allowbordersize | 0 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I set the field "Style of borders" to "dashed"
    And I press "Create table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:1px dashed #FFFFFF;background-color:#FFFFFF;width:100%;\""

  @javascript
  Scenario: Edit a table with background colour and width with borders and border styling on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 1 | atto_table |
    | allowbordersize | 0 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    When I click on "Edit table" "link"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I set the field "Style of borders" to "dashed"
    And I press "Update table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:1px dashed rgb(255,255,255);width:100%;background-color:rgb(255,255,255);\""

  @javascript
  Scenario: Create a table with background colour and width with borders, border styling, and border size on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 1 | atto_table |
    | allowbordersize | 1 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I set the field "Style of borders" to "dashed"
    And I set the field "Size of borders" to "2"
    And I press "Create table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:2px dashed #FFFFFF;background-color:#FFFFFF;width:100%;\""

  @javascript
  Scenario: Edit a table with background colour and width with borders, border styling, and border size on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 1 | atto_table |
    | allowbordersize | 1 | atto_table |
    | allowbordercolour | 0 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    When I click on "Edit table" "link"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should not exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I set the field "Style of borders" to "dashed"
    And I set the field "Size of borders" to "2"
    And I press "Update table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:2px dashed rgb(255,255,255);width:100%;background-color:rgb(255,255,255);\""

  @javascript
  Scenario: Create a table with all settings on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 1 | atto_table |
    | allowbordersize | 1 | atto_table |
    | allowbordercolour | 1 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "Table test"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Table" "button"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I set the field "Style of borders" to "dashed"
    And I set the field "Size of borders" to "2"
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .bordercolour" "css_element"
    And I press "Create table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:2px dashed #FFFFFF;background-color:#FFFFFF;width:100%;\""

  @javascript
  Scenario: Edit a table with background colour and width with borders, border styling, and border size on
    Given the following config values are set as admin:
    | config | value | plugin |
    | allowborders | 1 | atto_table |
    | allowborderstyles | 1 | atto_table |
    | allowbordersize | 1 | atto_table |
    | allowbordercolour | 1 | atto_table |
    | allowbackgroundcolour | 1 | atto_table |
    | allowwidth | 1 | atto_table |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Entry title" to "How to make a table"
    And I set the field "Blog entry body" to "<table><tr><td>Cell</td></tr></table>"
    And I select the text in the "Blog entry body" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Table" "button"
    When I click on "Edit table" "link"
    Then ".moodle-dialogue-base .atto_form .borders" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .borderstyle" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordersize" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .bordercolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element" should exist
    Then ".moodle-dialogue-base .atto_form .customwidth" "css_element" should exist
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .backgroundcolour" "css_element"
    And I set the field "Table width (in %)" to "100"
    And I set the field "Borders" to "Around table"
    And I set the field "Style of borders" to "dashed"
    And I set the field "Size of borders" to "2"
    And I click on "#FFFFFF" "radio" in the ".moodle-dialogue-base .atto_form .bordercolour" "css_element"
    And I press "Update table"
    And I press "Save changes"
    And I follow "Edit"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "style=\"border:2px dashed rgb(255,255,255);width:100%;background-color:rgb(255,255,255);\""
