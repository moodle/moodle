@core @core_course @_cross_browser
Feature: Show/hide course sections
  In order to delay sections availability
  As a teacher
  I need to show or hide sections

  @javascript
  Scenario: Show / hide section icon functions correctly
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test hidden forum 11 name |
      | Description | Test hidden forum 11 description |
      | Visible | Hide |
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test hidden forum 12 name |
      | Description | Test hidden forum 12 description |
      | Visible | Show |
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name | Test hidden forum 21 name |
      | Description | Test hidden forum 21 description |
      | Visible | Hide |
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name | Test hidden forum 22 name |
      | Description | Test hidden forum 22 description |
      | Visible | Show |
    And I add a "Forum" to section "3" and I fill the form with:
      | Forum name | Test hidden forum 31 name |
      | Description | Test hidden forum 31 description |
      | Visible | Hide |
    And I add a "Forum" to section "3" and I fill the form with:
      | Forum name | Test hidden forum 32 name |
      | Description | Test hidden forum 32 description |
      | Visible | Show |
    And I follow "Course 1"
    When I hide section "1"
    Then section "1" should be hidden
    And section "2" should be visible
    And section "3" should be visible
    And I hide section "2"
    And section "2" should be hidden
    And I show section "2"
    And section "2" should be visible
    And I hide section "3"
    And I show section "3"
    And I hide section "3"
    And section "3" should be hidden
    And I reload the page
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And section "2" should be visible
    And section "3" should be hidden
    And all activities in section "1" should be hidden
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And section "2" should be visible
    And section "3" should be hidden
    And all activities in section "1" should be hidden
