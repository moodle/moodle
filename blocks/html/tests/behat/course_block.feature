@block @block_html
Feature: Text blocks in a course
  In order to have one or multiple Text blocks in a course
  As a teacher
  I need to be able to create and change such blocks

  Scenario: Adding Text block in a course
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Terry1    | Teacher1 | teacher@example.com  |
      | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the field "Content" to "First block content"
    And I set the field "Text block title" to "First block header"
    And I press "Save changes"
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the field "Content" to "Second block content"
    And I set the field "Text block title" to "Second block header"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "First block content" in the "First block header" "block"
    And I should see "Second block content" in the "Second block header" "block"
