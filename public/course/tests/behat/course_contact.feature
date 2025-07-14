@core @core_course
Feature: Test if displaying the course contacts works correctly:
  As a user I need to see the course contacts of a course.
  As an admin I need to be able to control the appearance of the course contacts.

  Background:
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | CAT1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | teacher3 | Teacher | 3 | teacher3@example.com |
      | manager1 | Manager | 1 | manager1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C1 | teacher |
      | teacher2 | C1 | teacher |
      | teacher3 | C1 | editingteacher |
      | manager1 | C1 | manager |

  Scenario: Test general course contacts functionality for all user roles
    Given I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | 0 |
      | Teacher | 1 |
      | Non-editing teacher | 0 |
      | Display all course contact roles | 0 |
    And I press "Save changes"

    When I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should not see "Teacher 2" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"

    When I log out
    And I log in as "manager1"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should not see "Teacher 2" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"

    When I log out
    And I log in as "teacher1"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should not see "Teacher 2" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"

    When I log out
    And I log in as "student1"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should not see "Teacher 2" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"

  Scenario: Test course contact roles without displaying all roles
    Given I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | 0 |
      | Teacher | 1 |
      | Non-editing teacher | 1 |
      | Display all course contact roles | 0 |
    And I press "Save changes"
    When I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should see "Teacher 2" in the ".teachers" "css_element"
    And I should see "Teacher 3" in the ".teachers" "css_element"
    And I should see "Teacher: Teacher 1" in the ".teachers" "css_element"
    And I should not see "Teacher, Non-editing teacher: Teacher 1" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"

  Scenario: Test course contact roles with displaying all roles and standard sorting
    Given I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | 0 |
      | Teacher | 1 |
      | Non-editing teacher | 1 |
      | Display all course contact roles | 1 |
    And I press "Save changes"
    When I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should see "Teacher 2" in the ".teachers" "css_element"
    And I should see "Teacher 3" in the ".teachers" "css_element"
    And I should see "Teacher, Non-editing teacher: Teacher 1" in the ".teachers" "css_element"
    And I should not see "Teacher: Teacher 1" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"
    And I should see teacher "Teacher 1" before "Teacher 3" in the course contact listing
    And I should see teacher "Teacher 3" before "Teacher 2" in the course contact listing
    And I should not see teacher "Teacher 1" after "Teacher 3" in the course contact listing
    And I should not see teacher "Teacher 3" after "Teacher 2" in the course contact listing

  Scenario: Test course contact roles with displaying all roles and modified sorting
    Given I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | 0 |
      | Teacher | 1 |
      | Non-editing teacher | 1 |
      | Display all course contact roles | 1 |
    And I press "Save changes"
    And I navigate to "Users > Permissions > Define roles" in site administration
    And I click on "Move up" "link" in the "//td[text()[contains(.,'Non-editing teacher')]]/parent::tr/td[contains(@class, 'lastcol')]" "xpath_element"
    When I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    Then I should see "Teacher 1" in the ".teachers" "css_element"
    And I should see "Teacher 2" in the ".teachers" "css_element"
    And I should see "Teacher 3" in the ".teachers" "css_element"
    And I should see "Non-editing teacher, Teacher: Teacher 1" in the ".teachers" "css_element"
    And I should not see "Non-editing teacher: Teacher 1" in the ".teachers" "css_element"
    And I should not see "Manager 1" in the ".teachers" "css_element"
    And I should see teacher "Teacher 1" before "Teacher 2" in the course contact listing
    And I should see teacher "Teacher 2" before "Teacher 3" in the course contact listing
    And I should not see teacher "Teacher 1" after "Teacher 2" in the course contact listing
    And I should not see teacher "Teacher 2" after "Teacher 3" in the course contact listing
