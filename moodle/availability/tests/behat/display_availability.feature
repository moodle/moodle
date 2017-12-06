@core @core_availability
Feature: display_availability
  In order to know which activities are available
  As a user
  I need to see appropriate availability restrictions for activities and sections

  # PURPOSE OF THIS TEST FEATURE:
  #
  # This test is to do a basic check of the user interface relating to display
  # of availability conditions - i.e. if there's a condition, does it show up;
  # are we doing the HTML correctly; does it correctly hide an activity where
  # the options are set to not show it at all.
  #
  # Things this test is not:
  # - It is not a test of the date condition specifically. The date condition is
  #   only used as an example in order to get the availability information to
  #   display. (The date condition has its own Behat test in
  #   /availability/condition/date/tests/behat.)
  # - It is not a complete test of the logic. This is supposed to be a shallow
  #   check of the user interface parts and doesn't cover all logical
  #   possibilities. The logic is tested in PHPUnit tests instead, which are
  #   much more efficient. (Again there are unit tests for the overall system
  #   and for each condition type.)

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario: Activity availability display
    # Set up.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Add a Page with 1 restriction.
    When I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | Page 1 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "direction" to "until"
    And I set the field "x[year]" to "2013"
    And I set the field "x[month]" to "March"
    And I press "Save and return to course"

    # Add a Page with 2 restrictions - one is set to hide from students if failed.
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | Page 2 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "direction" to "until"
    And I set the field "x[year]" to "2013"
    And I set the field "x[month]" to "March"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I press "Add restriction..."
    And I click on "User profile" "button" in the "Add restriction..." "dialogue"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "email@example.com"
    And I set the field "Method of comparison" to "is equal to"
    And I press "Save and return to course"

    # Add another Page with no restrictions.
    And I add a "Page" to section "3" and I fill the form with:
      | Name         | Page 3 |
      | Description  | Test   |
      | Page content | Test   |

    # Page 1 should show in single-line format, showing the date
    Then I should see "Available until" in the "#section-1 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-1 .availabilityinfo" "css_element"
    And I should see "2013" in the "#section-1 .availabilityinfo" "css_element"
    And "li" "css_element" should not exist in the "#section-1 .availabilityinfo" "css_element"

    # Page 2 should show in list format.
    And "li" "css_element" should exist in the "#section-2 .availabilityinfo" "css_element"
    And I should see "Not available unless:" in the "#section-2 .availabilityinfo" "css_element"
    And I should see "It is before" in the "#section-2 .availabilityinfo" "css_element"
    And I should see "hidden otherwise" in the "#section-2 .availabilityinfo" "css_element"
    And I should see "Email address" in the "#section-2 .availabilityinfo" "css_element"

    # Page 3 should not have available info.
    And "#section-3 .availabilityinfo" "css_element" should not exist

    # Change to student view.
    Given I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # Page 1 display still there but should be dimmed and not a link.
    Then I should see "Page 1" in the "#section-1 .dimmed_text" "css_element"
    And ".activityinstance a" "css_element" should not exist in the "Topic 1" "section"

    # Date display should be present.
    And I should see "Available until" in the "Topic 1" "section"

    # Page 2 display not there at all
    And I should not see "Page 2" in the "region-main" "region"

    # Page 3 display and link
    And I should see "Page 3" in the "region-main" "region"
    And ".activityinstance a" "css_element" should exist in the "Topic 3" "section"

  @javascript
  Scenario: Section availability display
    # Set up.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Add a restriction to section 1 (visible to students).
    When I edit the section "1"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "direction" to "until"
    And I set the field "x[year]" to "2013"
    And I press "Save changes"

    # Section 2 is the same but hidden from students
    When I edit the section "2"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "direction" to "until"
    And I set the field "x[year]" to "2013"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I press "Save changes"

    # This is necessary because otherwise it fails in Chrome, see MDL-44959
    And I am on "Course 1" course homepage

    # Add Pages to each section.
    And I add a "Page" to section "1" and I fill the form with:
      | Name         | Page 1 |
      | Description  | Test   |
      | Page content | Test   |
    And I add a "Page" to section "2" and I fill the form with:
      | Name         | Page 2 |
      | Description  | Test   |
      | Page content | Test   |
    And I add a "Page" to section "3" and I fill the form with:
      | Name         | Page 3 |
      | Description  | Test   |
      | Page content | Test   |

    # Check display
    Then I should see "Available until" in the "#section-1 .availabilityinfo" "css_element"
    And I should see "Available until" in the "#section-2 .availabilityinfo" "css_element"
    And I should see "hidden otherwise" in the "#section-2 .availabilityinfo" "css_element"

    # Change to student view.
    Given I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # The contents of both sections should be hidden.
    Then I should not see "Page 1" in the "region-main" "region"
    And I should not see "Page 2" in the "region-main" "region"
    And I should see "Page 3" in the "region-main" "region"

    # Section 1 should be visible and show info.
    And I should see "Topic 1" in the "region-main" "region"
    And I should see "Available until" in the "#section-1 .availabilityinfo" "css_element"

    # Section 2 should not be available at all
    And I should not see "Topic 2" in the "region-main" "region"
