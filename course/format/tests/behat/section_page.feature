@core @core_courseformat
Feature: Single section course page
  In order to improve the course page
  As a user
  I need to be able to see a section in a single page

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | numsections      | 3        |
      | initsections     | 1        |
    And the following "activities" exist:
      | activity | name                | course | idnumber | section |
      | assign   | Activity sample 0.1 | C1     | sample1  | 0       |
      | assign   | Activity sample 1.1 | C1     | sample1  | 1       |
      | assign   | Activity sample 1.2 | C1     | sample2  | 1       |
      | assign   | Activity sample 1.3 | C1     | sample3  | 1       |
      | assign   | Activity sample 2.1 | C1     | sample3  | 2       |
      | assign   | Activity sample 2.2 | C1     | sample3  | 2       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given I am on the "C1" "Course" page logged in as "teacher1"

  @javascript
  Scenario: Collapsed sections are always expanded in the single section page
    Given I press "Collapse all"
    And I should not see "Activity sample 1.1" in the "region-main" "region"
    When I am on the "Course 1 > Section 1" "course > section" page
    Then I should see "Activity sample 1.1"
    And I should see "Activity sample 1.2"
    And I should see "Activity sample 1.3"
    And I should not see "Activity sample 2.1" in the "region-main" "region"
    And I should not see "Activity sample 2.1" in the "region-main" "region"

  Scenario: General section is not displayed in the single section page
    When I am on the "Course 1 > Section 1" "course > section" page
    Then I should not see "General" in the "#section-1" "css_element"
    And I should not see "Activity sample 0.1" in the "region-main" "region"
    And I should see "Activity sample 1.1"
    And I should see "Activity sample 1.2"
    And I should see "Activity sample 1.3"
    And I should not see "Activity sample 2.1" in the "region-main" "region"
    And I should not see "Activity sample 2.1" in the "region-main" "region"

  @javascript
  Scenario: The view action for sections displays the single section page
    Given I turn editing mode on
    And I open section "1" edit menu
    When I click on "View" "link" in the "Section 1" "section"
    Then I should not see "General" in the "#section-1" "css_element"
    And I should not see "Activity sample 0.1" in the "region-main" "region"
    And I should see "Activity sample 1.1"
    And I should see "Activity sample 1.2"
    And I should see "Activity sample 1.3"
    And I should not see "Activity sample 2.1" in the "region-main" "region"
    And I should not see "Activity sample 2.1" in the "region-main" "region"
    And I am on "Course 1" course homepage
    And I open section "2" edit menu
    And I click on "View" "link" in the "Section 2" "section"
    And I should not see "General" in the "#section-2" "css_element"
    And I should not see "Activity sample 0.1" in the "region-main" "region"
    And I should not see "Activity sample 1.1"
    And I should not see "Activity sample 1.2"
    And I should not see "Activity sample 1.3"
    And I should see "Activity sample 2.1" in the "region-main" "region"
    And I should see "Activity sample 2.1" in the "region-main" "region"
    # The General section is also displayed in isolation.
    But I am on "Course 1" course homepage
    And I open section "0" edit menu
    And I click on "View" "link" in the "General" "section"
    And I should see "General" in the "page" "region"
    And I should see "Activity sample 0.1" in the "region-main" "region"
    And I should not see "Activity sample 1.1" in the "region-main" "region"
    And I should not see "Activity sample 1.2" in the "region-main" "region"
    And I should not see "Activity sample 1.3" in the "region-main" "region"
    And I should not see "Activity sample 2.1" in the "region-main" "region"
    And I should not see "Activity sample 2.1" in the "region-main" "region"
    # The section viewed has been trigered.
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Live logs" in current page administration
    And I should see "Section viewed"

  Scenario: The add section button is not displayed in the single section page
    Given I turn editing mode on
    When I click on "View" "link" in the "Section 1" "section"
    Then "Add section" "link" should not exist in the "region-main" "region"

  @javascript
  Scenario: Change the section name inline
    # The course index is hidden by default in small devices.
    Given I change window size to "large"
    And I turn editing mode on
    And I open section "1" edit menu
    And I click on "View" "link" in the "Section 1" "section"
    When I set the field "Edit section name" in the "page-header" "region" to "Custom section name"
    Then "Custom section name" "text" should exist in the ".breadcrumb" "css_element"

  @javascript
  Scenario: Copy section page permalink URL to clipboard
    Given I am on the "Course 1 > Section 1" "course > section" page
    And I turn editing mode on
    When I choose the "Permalink" item in the "Edit" action menu of the "page-header" "region"
    And I click on "Copy to clipboard" "link" in the "Permalink" "dialogue"
    Then I should see "Text copied to clipboard"

  Scenario: Blocks are displayed in section page too
    Given I log out
    And the following "blocks" exist:
      | blockname    | contextlevel | reference | pagetypepattern | defaultregion |
      | online_users | Course       | C1        | course-view-*   | site-pre      |
    When I am on the "C1" "Course" page logged in as "teacher1"
    Then I should see "Online users"
    And I am on the "Course 1 > Section 1" "course > section" page
    And I should see "Online users"

  @javascript
  Scenario: Delete a section from the section page redirects to the main course page
    Given I am on the "C1 > Section 1" "course > section" page
    And I turn editing mode on
    When I choose the "Delete" item in the "Edit" action menu of the "page-header" "region"
    And I click on "Delete" "button" in the "Delete section?" "dialogue"
    # Section 1 should be removed.
    Then I should not see "Section 1"
    # The user should be redirected to the course page.
    And I should see "General" in the "page" "region"
