@mod @mod_data @core_tag
Feature: Edited data entries handle tags correctly
  In order to get data entries properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "tags" exist:
      | name | isstandard |
      | Tag1 | 1          |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name        | Test field name        |
      | Field description | Test field description |
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name        | Test field 2 name        |
      | Field description | Test field 2 description |
    # To generate the default templates.
    And I follow "Templates"
    And I wait until the page is ready
    And I log out

  @javascript
  Scenario: Data entry of custom tags works as expected
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I add an entry to "Test database name" database with:
      | Test field name   | Student original entry untagged   |
      | Test field 2 name | Student original entry untagged 2 |
    And I add an entry to "Test database name" database with:
      | Test field name   | Student original entry tagged   |
      | Test field 2 name | Student original entry tagged 2 |
    And I set the field with xpath "//div[@class='datatagcontrol']//input[@type='text']" to "Tag1"
    And I click on "[data-value='Tag1']" "css_element"
    And I press "Save and view"
    Then I should see "Student original entry"
    And I should see "Tag1" in the "div.tag_list" "css_element"
    And I follow "Edit"
    And I should see "Tag1" in the ".form-autocomplete-selection" "css_element"
    And I follow "View list"
    And I should see "Tag1" in the "div.tag_list" "css_element"
    And I follow "Search"
    And I set the field with xpath "//div[@class='datatagcontrol']//input[@type='text']" to "Tag1"
    And I click on "[data-value='Tag1']" "css_element"
    And I press "Save settings"
    And I should see "Student original entry tagged"
    And I should see "Student original entry tagged 2"
    And I should not see "Student original entry untagged"
    And I should not see "Student original entry untagged 2"
