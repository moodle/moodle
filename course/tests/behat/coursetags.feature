@core @core_course @core_tag @javascript
Feature: Tagging courses
  In order to search courses
  As a teacher
  I need to be able to tag courses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | user1    | User    | 1 | user1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
      | Course 2  | c2        |
    And the following "tags" exist:
      | name         | isstandard  |
      | Neverusedtag | 1           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
      | teacher2 | c1     | teacher        |
      | teacher1 | c2     | editingteacher |
      | teacher2 | c2     | teacher        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Tags | Mathematics |
    And I press "Save and display"
    And I log out

  Scenario: Set course tags using the course edit form
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then "Mathematics" "autocomplete_suggestions" should exist
    And I set the following fields to these values:
      | Tags | Algebra |
    And I press "Save and display"
    And I am on "Course 2" course homepage
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Tags | Mathematics, Geometry |
    And I press "Save and display"
    And I log out
    And I log in as "user1"
    And I press "Customise this page"
    # TODO MDL-57120 "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Mathematics"
    Then I should see "Course 1"
    And I should see "Course 2"
    And I follow "Tags"
    And I follow "Algebra"
    And I should see "Course 1"
    And I should not see "Course 2"
    And I follow "Tags"
    And I follow "Geometry"
    And I should not see "Course 1"
    And I should see "Course 2"
    And I log out

  Scenario: User can set course tags using separate form
    Given I log in as "admin"
    And I set the following system permissions of "Non-editing teacher" role:
      | moodle/course:tag | Allow |
    And I log out
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Course tags" in current page administration
    Then I should see "Mathematics" in the ".form-autocomplete-selection" "css_element"
    And I set the following fields to these values:
      | Tags | Algebra |
    And I press "Save changes"
    And I am on "Course 2" course homepage
    And I navigate to "Course tags" in current page administration
    And I set the following fields to these values:
      | Tags | Mathematics, Geometry |
    And I press "Save changes"
    And I log out
    And I log in as "user1"
    And I press "Customise this page"
    # TODO MDL-57120 "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Mathematics"
    Then I should see "Course 1"
    And I should see "Course 2"
    And I follow "Tags"
    And I follow "Algebra"
    And I should see "Course 1"
    And I should not see "Course 2"
    And I follow "Tags"
    And I follow "Geometry"
    And I should not see "Course 1"
    And I should see "Course 2"
    And I log out
