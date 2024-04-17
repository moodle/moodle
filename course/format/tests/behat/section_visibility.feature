@core @core_courseformat
Feature: Varify section visibility interface
  In order to edit the course sections visibility
  As a teacher
  I need to be able to see the updated visibility information

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | numsections      | 3        |
      | initsections     | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on

  @javascript
  Scenario: Activities available but not shown on course page only apply to hidden sections.
    Given I hide section "1"
    And I open "Activity sample 1" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    And I should see "Available but not shown on course page" in the "Activity sample 1" "activity"
    When I show section "1"
    Then I should not see "Available but not shown on course page" in the "Activity sample 1" "activity"

  @javascript
  Scenario: Hide a section also hides the activities.
    When I hide section "1"
    Then I should see "Hidden from students" in the "Section 1" "section"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I show section "1"
    And I should not see "Hidden from students" in the "Section 1" "section"
    And I should not see "Hidden from students" in the "Activity sample 1" "activity"

  @javascript
  Scenario: Hidden activities in hidden sections stay hidden when the section is shown.
    Given I open "Activity sample 1" actions menu
    And I choose "Hide" in the open action menu
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    And I hide section "1"
    And I should see "Hidden from students" in the "Activity sample 1" "activity"
    When I show section "1"
    Then I should see "Hidden from students" in the "Activity sample 1" "activity"

  @javascript
  Scenario: Hidden sections can be shown and hidden using the drop down menu in the activity card.
    Given I hide section "1"
    When I click on "Hidden from students" "button" in the "Section 1" "section"
    And I should see "Show on course page" in the "Section 1" "section"
    And I should see "Hide on course page" in the "Section 1" "section"
    And I click on "Show on course page" "link" in the "Section 1" "section"
    Then I should not see "Hidden from students" in the "Section 1" "section"

  @javascript
  Scenario: Hidden sections are shown as "not available" to student when
  the course is set to show hidden sections "not available". They are shown
  as "hidden from students" to editing teachers.
    Given the following "courses" exist:
      | fullname | shortname | format | hiddensections | numsections | initsections |
      | Course 2 | C2        | topics | 0              | 3           | 1            |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C2     | student        |
      | teacher1 | C2     | editingteacher |
    And I am on the "C2" "Course" page logged in as "admin"
    And I turn editing mode on
    And I hide section "1"
    And I log out
    When I am on the "C2" "Course" page logged in as "student1"
    Then I should see "Not available" in the "Section 1" "section"
    And I log out
    And I am on the "C2" "Course" page logged in as "teacher1"
    And I should see "Hidden from students" in the "Section 1" "section"

  @javascript
  Scenario: The visibility badge can show a hidden section in a the section page
    Given I hide section "1"
    When I am on the "C1 > Section 1" "course > section" page
    And I click on "Hidden from students" "button" in the "[data-region='sectionbadges']" "css_element"
    And I should see "Show on course page" in the "[data-region='sectionbadges']" "css_element"
    And I should see "Hide on course page" in the "[data-region='sectionbadges']" "css_element"
    And I click on "Show on course page" "link" in the "[data-region='sectionbadges']" "css_element"
    Then I should not see "Hidden from students" in the "[data-region='sectionbadges']" "css_element"
    And I open the action menu in "page-header" "region"
    And I choose "Hide" in the open action menu
    And I should see "Hidden from students" in the "[data-region='sectionbadges']" "css_element"
