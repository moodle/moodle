@mod @mod_checklist @checklist
Feature: Multiple autopopulate items can be shown/hidden at once

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity  | course | section | idnumber   | name            | intro                 |
      | assign    | C1     | 1       | assign1    | Test assignment | This is an assignment |
      | data      | C1     | 1       | data1      | Test database   | This is a database    |
      | checklist | C1     | 2       | checklist1 | Test checklist  | This is a checklist   |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Checklist" to section "1" and I fill the form with:
      | Checklist                        | Test auto-pop checklist             |
      | Introduction                     | This is an auto-populated checklist |
      | Show course modules in checklist | Whole course                        |
    And I log out

  Scenario: When viewing an auto-populated checklist, a student should see items corresponding to the course modules
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test auto-pop checklist"
    Then I should see "Test auto-pop checklist"
    And I should see "This is an auto-populated checklist"
    And I should see "Test assignment" in the "ol.checklist" "css_element"
    And I should see "Test database" in the "ol.checklist" "css_element"
    And I should see "Test checklist" in the "ol.checklist" "css_element"

  Scenario: When I select multiple items and click the 'Show/hide' button, the items' visibility should toggle
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test auto-pop checklist"
    And I follow "Edit checklist"
    And I set the field with xpath "//input[@type='checkbox' and @title='Test assignment']" to "1"
    And I set the field with xpath "//input[@type='checkbox' and @title='Test database']" to "1"
    And I press "Show/hide selected items"
    And I set the field with xpath "//input[@type='checkbox' and @title='Test database']" to "1"
    And I set the field with xpath "//input[@type='checkbox' and @title='Test checklist']" to "1"
    And I press "Show/hide selected items"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test auto-pop checklist"
    Then I should not see "Test assignment" in the "ol.checklist" "css_element"
    And I should see "Test database" in the "ol.checklist" "css_element"
    And I should not see "Test checklist" in the "ol.checklist" "css_element"
