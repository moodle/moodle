@core @core_course
Feature: Section 0 default/custom title
  In order to set up a course
  As a teacher
  I need to be able to use/change default section 0 title

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "activities" exist:
      | activity | name               | intro                        | course | idnumber   | section |
      | data     | Test database name | Test database description    | C1     | database1  | 2       |
      | forum    | Test forum name    |                              | C1     | forum1     | 1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Default section 0 title is General
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    Then I should see "General" in the "li#section-0" "css_element"

  @javascript
  Scenario: Editing section 0 title
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "0" and I fill the form with:
      | Custom                          | 1                |
      | New value for Section name      | Edited section 0 |
    And I should see "Edited section 0" in the "li#section-0" "css_element"
    When I set the field "Edit topic name" in the "li#section-0" "css_element" to ""
    Then I should not see "Edited section 0" in the "li#section-0" "css_element"
    And I should see "General" in the "li#section-0" "css_element"
    And "New name for topic" "field" should not exist
    And I set the field "Edit topic name" in the "li#section-0" "css_element" to "Edited section 0"
    And I should see "Edited section 0" in the "li#section-0" "css_element"
    And I edit the section "0" and I fill the form with:
      | Custom | 0                      |
    And I should not see "Edited section 0" in the "li#section-0" "css_element"
    And I should see "General" in the "li#section-0" "css_element"
