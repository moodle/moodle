@core @core_courseformat
Feature: Verify that courseindex is usable with the keyboard
  In order to use the course index
  As a user
  I need to be able to navigate it without a mouse

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 3        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | book     | Activity sample 2 |                             | C1     | sample2  | 2       |
      | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 3       |
    Given I am on the "C1" "Course" page logged in as "admin"
    And I change window size to "large"
    And I click on "Close course index" "button"
    And I click on "Open course index" "button"
    And I should see "Topic 1" in the "courseindex-content" "region"
    And the focused element is "[data-preference='drawer-open-index'] .drawertoggle" "css_element"
    And I press the tab key
    And I press the tab key
    And the focused element is ".courseindex-section" "css_element"

  @javascript
  Scenario: General focus on open course index.
    When I press the shift tab key
    And I press the shift tab key
    And the focused element is "[data-preference='drawer-open-index'] .drawertoggle" "css_element"
    And I press enter
    Then I should not see "Topic 1" in the "courseindex-content" "region"

  @javascript @accessibility
  Scenario: Course index should be accessible.
    When I press the shift tab key
    And I press the shift tab key
    And I press enter
    Then the page should meet accessibility standards with "wcag143" extra tests
    And I press enter
    And the page should meet accessibility standards with "wcag143" extra tests

  @javascript
  Scenario: Opening and closing sections.
    When I press the down key
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    # Close section with left key.
    Then I press the left key
    And I should not see "Activity sample 1" in the "courseindex-content" "region"
    # Open a section with right key
    And I press the right key
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    # Key down to focus the module and close the section with two left keys.
    And I press the down key
    And I press the left key
    And I press the left key
    And I should not see "Activity sample 1" in the "courseindex-content" "region"
    # Open a section using enter key.
    And I press the down key
    And I press the left key
    And I should not see "Activity sample 2" in the "courseindex-content" "region"
    And I press enter
    And I should see "Activity sample 2" in the "courseindex-content" "region"

  @javascript
  Scenario: Enter key should not collapse sections.
    When I press the down key
    And I press enter
    And I should see "Activity sample 1" in the "courseindex-content" "region"

  @javascript
  Scenario: Navigate to an activity.
    When I press the down key
    And I press the right key
    And I press enter
    Then I should see "Activity sample 1" in the "page-header" "region"

  @javascript
  Scenario: Navigate to first and last element.
    # Close sections 1 and 3.
    Given I press the down key
    And I press the left key
    And I should not see "Activity sample 1" in the "courseindex-content" "region"
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the left key
    And I should not see "Activity sample 3" in the "courseindex-content" "region"
    # Use end key to go to the last element.
    When I press the end key
    And I press the right key
    And I should see "Activity sample 3" in the "courseindex-content" "region"
    And I press the left key
    Then I should not see "Activity sample 3" in the "courseindex-content" "region"
    # Use home key to go to the first element.
    And I press the home key
    And I press the down key
    And I press the right key
    And I should see "Activity sample 1" in the "courseindex-content" "region"

  @javascript
  Scenario: Asterisc to open all sections.
    # Close sections 1 and 2.
    Given I press the down key
    And I press the left key
    And I should not see "Activity sample 1" in the "courseindex-content" "region"
    And I press the down key
    And I press the left key
    And I should not see "Activity sample 2" in the "courseindex-content" "region"
    And I should see "Activity sample 3" in the "courseindex-content" "region"
    When I press the multiply key
    Then  I should see "Activity sample 1" in the "courseindex-content" "region"
    And I should see "Activity sample 2" in the "courseindex-content" "region"
    And I should see "Activity sample 3" in the "courseindex-content" "region"
