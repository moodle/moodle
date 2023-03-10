@core @core_course @core_courseformat
Feature: Collapse course sections
  In order to quickly access the course structure
  As a user
  I need to collapse/extend sections for Topics/Weeks formats.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course" exists:
      | fullname         | Course 1  |
      | shortname        | C1        |
      | category         | 0         |
      | enablecompletion | 1         |
      | numsections      | 5         |
      | startdate        | 957139200 |
      | enablecompletion | 1         |
      | hiddensections   | 0         |
    And the following "activities" exist:
      | activity | name         | intro                        | course | idnumber | section | completion |
      | assign   | Assignment 1 | Test assignment description1 | C1     | assign1  | 1       | 1          |
      | assign   | Assignment 2 | Test assignment description2 | C1     | assign2  | 2       | 1          |
      | book     | Book 2       |                              | C1     | book2    | 2       | 1          |
      | book     | Book 3       |                              | C1     | book3    | 3       | 1          |
      | forum    | Forum 4      | Test forum description4      | C1     | forum4   | 4       | 1          |
      | forum    | Forum 5      | Test forum description5      | C1     | forum5   | 5       | 1          |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I edit the section "4"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "direction" to "until"
    And I set the field "x[year]" to "2013"
    And I press "Save changes"
    And I hide section "5"
    And I log out

  @javascript
  Scenario: No chevron on site home
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name  | Test forum post backup name        |
      | Description | Test forum post backup description |
    And I click on "Edit summary" "link" in the "region-main" "region"
    And I click on "Custom" "checkbox"
    And I set the field "New value for Section name" to "New section name"
    When I press "Save changes"
    Then "[data-toggle=collapse]" "css_element" should not exist in the "region-main" "region"

  @javascript
  Scenario: Expand/collapse sections for Topics format.
    Given I am on the "Course 1" course page logged in as student1
    And "[data-toggle=collapse]" "css_element" should exist in the "region-main" "region"
    And I should see "Assignment 1" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"
    And I should see "Book 3" in the "region-main" "region"
    And I should see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-4 .availabilityinfo" "css_element"
    And I should not see "Forum 4"
    And I should see "Not available" in the "#section-5" "css_element"
    And I should not see "Forum 5"
    When I click on "#collapssesection3" "css_element"
    And I should see "Assignment 1" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"
    And I should not see "Book 3" in the "region-main" "region"
    And I click on "#collapssesection1" "css_element"
    And I click on "#collapssesection2" "css_element"
    And I click on "#collapssesection4" "css_element"
    And I click on "#collapssesection5" "css_element"
    Then I should not see "Assignment 1" in the "region-main" "region"
    And I should not see "Assignment 2" in the "region-main" "region"
    And I should not see "Book 2" in the "region-main" "region"
    And I should not see "Book 3" in the "region-main" "region"
    And I should not see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I click on "#collapssesection1" "css_element"
    And I click on "#collapssesection2" "css_element"
    And I click on "#collapssesection3" "css_element"
    And I click on "#collapssesection4" "css_element"
    And I click on "#collapssesection5" "css_element"
    And I should see "Assignment 1" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"
    And I should see "Book 3" in the "region-main" "region"
    And I should see "Available until" in the "#section-4 .availabilityinfo" "css_element"

  @javascript
  Scenario: Expand/collapse sections for Weeks format.
    Given I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Format      | Weekly format     |
    And I press "Save and display"
    And I should see "Assignment 1" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"
    And I should see "Book 3" in the "region-main" "region"
    And I should see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "Forum 4"
    And I should see "Hidden from students" in the "#section-5" "css_element"
    And I should see "Forum 5"
    When I click on "#collapssesection3" "css_element"
    And I should see "Assignment 1" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"
    And I should not see "Book 3" in the "region-main" "region"
    And I click on "#collapssesection1" "css_element"
    And I click on "#collapssesection2" "css_element"
    And I click on "#collapssesection4" "css_element"
    And I click on "#collapssesection5" "css_element"
    Then I should not see "Assignment 1" in the "region-main" "region"
    And I should not see "Assignment 2" in the "region-main" "region"
    And I should not see "Book 2" in the "region-main" "region"
    And I should not see "Book 3" in the "region-main" "region"
    And I should not see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I should not see "Not available" in the "#section-5" "css_element"
    And I click on "#collapssesection1" "css_element"
    And I click on "#collapssesection2" "css_element"
    And I click on "#collapssesection3" "css_element"
    And I click on "#collapssesection4" "css_element"
    And I click on "#collapssesection5" "css_element"
    And I should see "Assignment 1" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And I should see "Book 2" in the "region-main" "region"
    And I should see "Book 3" in the "region-main" "region"
    And I should see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "Forum 4"
    And I should see "Hidden from students" in the "#section-5" "css_element"
    And I should see "Forum 5"

  @javascript
  Scenario: Users don't see chevron on one section per page for Topics format
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    And "[data-toggle=collapse]" "css_element" should not exist in the "region-main" "region"
    And I click on "Topic 2" "link" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And "[data-toggle=collapse]" "css_element" should not exist in the "region-main" "region"
    Then "Topic 1" "section" should not exist
    And "Topic 3" "section" should not exist
    And I am on "Course 1" course homepage with editing mode on
    And I should see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "Forum 4"
    And I should see "Hidden from students" in the "#section-5" "css_element"
    And I should see "Forum 5"

  @javascript
  Scenario: Users don't see chevron on one section per page for Weeks format
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Course layout | Show one section per page |
      | Format        | Weekly format             |
    And I press "Save and display"
    And "[data-toggle=collapse]" "css_element" should not exist in the "region-main" "region"
    And I click on "8 May - 14 May" "link" in the "region-main" "region"
    And I should see "Assignment 2" in the "region-main" "region"
    And "[data-toggle=collapse]" "css_element" should not exist in the "region-main" "region"
    Then "1 May - 7 May" "section" should not exist
    And "15 May - 21 May" "section" should not exist
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "Available until" in the "#section-4 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-4 .availabilityinfo" "css_element"
    And I should not see "Forum 4"
    And I should see "Not available" in the "#section-5" "css_element"
    And I should not see "Forum 5"
