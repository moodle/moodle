@core @core_course
Feature: Edit course settings
  In order to set the course according to my teaching needs
  As a teacher
  I need to edit the course settings

  @javascript
  Scenario: Edit course settings
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | summary | format |
      | Course 1 | C1 | <p>Course summary</p> | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Course full name | Edited course fullname |
      | Course short name | Edited course shortname |
      | Course summary | Edited course summary |
    And I press "Save and display"
    And I follow "Edited course fullname"
    Then I should not see "Course 1"
    And I should not see "C1"
    And I should see "Edited course fullname"
    And I should see "Edited course shortname"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And the field "Course full name" matches value "Edited course fullname"
    And the field "Course short name" matches value "Edited course shortname"
    And the field "Course summary" matches value "Edited course summary"
    And I am on homepage
    And I should see "Edited course fullname"

  Scenario: Edit course settings and return to the management interface
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | category | fullname | shortname | idnumber |
      | CAT1 | Course 1 | Course 1 | C1 |
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Categories" management page
    And I click on category "Cat 1" in the management interface
    And I should see the "Course categories and courses" management page
    When I click on "edit" action for "Course 1" in management course listing
    And I set the following fields to these values:
      | Course full name | Edited course fullname |
      | Course short name | Edited course shortname |
      | Course summary | Edited course summary |
    And I press "Save and return"
    Then I should see the "Course categories and courses" management page