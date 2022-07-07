@mod @mod_glossary @core_tag @javascript
Feature: Edited glossary entries handle tags correctly
  In order to get glossary entries properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name | Test glossary |
      | Description | A glossary about dreams! |
    And I log out

  Scenario: Glossary entry edition of custom tags works as expected
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test glossary"
    And I press "Add a new entry"
    And I set the following fields to these values:
      | Concept | Dummy first entry |
      | Definition | Dream is the start of a journey |
      | Tags | Example, Entry, Cool |
    And I press "Save changes"
    Then I should see "Example" in the ".glossary-tags" "css_element"
    And I should see "Entry" in the ".glossary-tags" "css_element"
    And I should see "Cool" in the ".glossary-tags" "css_element"
    And I click on "Edit" "link" in the ".entrylowersection" "css_element"
    And I expand all fieldsets
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Entry" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"

  Scenario: Glossary entry edition of standard tags works as expected
    Given I log in as "admin"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    And I follow "Add standard tags"
    And I set the field "Enter comma-separated list of new tags" to "OT1, OT2, OT3"
    And I press "Continue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test glossary"
    And I press "Add a new entry"
    And I expand all fieldsets
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | Concept | Dummy first entry |
      | Definition | Dream is the start of a journey |
      | Tags | OT1, OT3 |
    And I press "Save changes"
    Then I should see "OT1" in the ".glossary-tags" "css_element"
    And I should see "OT3" in the ".glossary-tags" "css_element"
    And I should not see "OT2" in the ".glossary-tags" "css_element"
    And I click on "Edit" "link" in the ".entrylowersection" "css_element"
    And I expand all fieldsets
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"
