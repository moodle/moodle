@core @core_course
Feature: Test we can see coursecontacts.
  As a student I need to see coursecontacts
  As a admin I need to test we can resort coursecontacts.
  As a admin I need to test we can show duplicate course contacts
  Scenario: Test coursecontacts functionality
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | CAT1 | topics |
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher 1 | T | teacher1@example.com |
      | teacher2 | Teacher 2 | T | teacher2@example.com |
      | teacher3 | Teacher 3 | T | teacher3@example.com |
      | manager1 | Manager 1 | M | manager1@example.com |
      | student1 | Student 1 | S | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C1 | teacher |
      | teacher2 | C1 | teacher |
      | teacher3 | C1 | editingteacher |
      | manager1 | C1 | manager |
    And I log in as "admin"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should not see "Teacher 2" in the "#region-main" "css_element"
    And I log out
    And I log in as "manager1"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should not see "Teacher 2" in the "#region-main" "css_element"
    And I log out
    And I log in as "teacher1"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should not see "Teacher 2" in the "#region-main" "css_element"
    And I log out
    And I log in as "student1"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should not see "Teacher 2" in the "#region-main" "css_element"
    And I should see teacher "Teacher 1 T" before "Teacher 3 T" in the course contact listing
    And I should not see teacher "Teacher 1 T" after "Teacher 3 T" in the course contact listing
    And I log out
  Scenario: Test selection and duplicates of coursecontact roles
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | CAT1 | topics |
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher 1 | T | teacher1@example.com |
      | teacher2 | Teacher 2 | T | teacher2@example.com |
      | teacher3 | Teacher 3 | T | teacher3@example.com |
      | manager1 | Manager 1 | M | manager1@example.com |
      | student1 | Student 1 | S | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C1 | teacher |
      | teacher2 | C1 | teacher |
      | teacher3 | C1 | editingteacher |
      | manager1 | C1 | manager |
    And I log in as "admin"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should not see "Teacher 2" in the "#region-main" "css_element"
    And I should not see "Manager 1" in the "#region-main" "css_element"
    And I am on site homepage
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | 1 |
      | Non-editing teacher | 1 |
      | Show duplicate course contacts | 1 |
    And I press "Save changes"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should see "Teacher 2" in the "#region-main" "css_element"
    And I should see "Teacher 3" in the "#region-main" "css_element"
    And I should see "Manager 1" in the "#region-main" "css_element"
    And I should see teacher "Manager 1 M" before "Teacher 1 T" in the course contact listing
    And I should see teacher "Teacher 1 T" before "Teacher 3 T" in the course contact listing
    And I should see teacher "Teacher 3 T" before "Teacher 2 T" in the course contact listing
    And I am on site homepage
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | 0 |
    And I press "Save changes"
    And I log out
    And I log in as "admin"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should not see "Manager 1" in the "#region-main" "css_element"
  Scenario: Test selection of coursecontact roles
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | CAT1 | topics |
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher 1 | T | teacher1@example.com |
      | teacher2 | Teacher 2 | T | teacher2@example.com |
      | teacher3 | Teacher 3 | T | teacher3@example.com |
      | manager1 | Manager 1 | M | manager1@example.com |
      | manager2 | Manager 2 | M | manager1@example.com |
      | student1 | Student 1 | S | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C1 | teacher |
      | teacher2 | C1 | teacher |
      | teacher3 | C1 | editingteacher |
      | manager1 | C1 | manager |
    And I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Manager | Yes |
      | Non-editing teacher | Yes |
      | Show duplicate course contacts | Yes |
    And I press "Save changes"
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should see "Teacher 2" in the "#region-main" "css_element"
    And I should see "Teacher 3" in the "#region-main" "css_element"
    And I should see "Manager 1" in the "#region-main" "css_element"
    And I should see teacher "Manager 1 M" before "Teacher 1 T" in the course contact listing
    And I should see teacher "Teacher 1 T" before "Teacher 3 T" in the course contact listing
    And I should see teacher "Teacher 3 T" before "Teacher 2 T" in the course contact listing
    And I navigate to "Appearance > Courses" in site administration
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I move up role "teacher" in the global role sortorder
    And I press "Save changes"
    And I am on site homepage
    And I follow "Course 1"
    And I log out
    And I log in as "admin"
    And I am on course index
    And I follow "Purge all caches"
    And I wait until the page is ready
    And I am on course index
    And I should see "Cat 1" in the "#region-main" "css_element"
    And I follow "Cat 1"
    And I wait until the page is ready
    And I should see "Course 1" in the "#region-main" "css_element"
    And I should see "Teacher 1" in the "#region-main" "css_element"
    And I should see "Teacher 2" in the "#region-main" "css_element"
    And I should see "Teacher 3" in the "#region-main" "css_element"
    And I should not see teacher "Teacher 2 T" after "Manager 1 M" in the course contact listing
    And I should not see teacher "Teacher 2 T" after "Teacher 3 T" in the course contact listing
    And I should see teacher "Teacher 1 T" before "Manager 1 M" in the course contact listing
    And I should not see teacher "Teacher 2 T" after "Manager 1 M" in the course contact listing
    And I should not see teacher "Teacher 2 T" after "Teacher 3 T" in the course contact listing
