@core @core_availability
Feature: edit_availability
  In order to control which students can see activities
  As a teacher
  I need to set up availability options for activities and sections

  # PURPOSE OF THIS TEST FEATURE:
  #
  # This test covers the user interface around editing availability conditions,
  # especially the JavaScript code which is not tested elsewhere (e.g. does the
  # 'Add restriction' dialog work). It tests both forms and also the admin
  # setting interface.
  #
  # This test does not check the detailed behaviour of the availability system,
  # which is mainly covered in PHPUnit (and, from the user interface
  # perspective, in the other Behat tests for each type of condition).

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

  Scenario: Confirm the 'enable availability' option is working
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Page" to section "1"
    Then "Restrict access" "fieldset" should not exist

    Given I follow "C1"
    When I edit the section "1"
    Then "Restrict access" "fieldset" should not exist

    When I log out
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable conditional access | 1 |

    When I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Page" to section "1"
    Then "Restrict access" "fieldset" should exist

    Given I follow "C1"
    When I edit the section "1"
    Then "Restrict access" "fieldset" should exist

  @javascript
  Scenario: Edit availability using settings in activity form
    # Set up.
    Given I log in as "admin"
    And I set the following administration settings values:
      | Enable conditional access | 1 |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"

    # Add a Page and check it has None in so far.
    And I turn editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    Then I should see "None" in the "Restrict access" "fieldset"

    # Add a Date restriction and check it appears.
    When I click on "Add restriction..." "button"
    Then "Add restriction..." "dialogue" should be visible
    When I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then "Add restriction..." "dialogue" should not exist
    And I should not see "None" in the "Restrict access" "fieldset"
    And "Restriction type" "select" should be visible
    And I should see "Date" in the "Restrict access" "fieldset"
    And ".availability-item .availability-eye img" "css_element" should be visible
    And ".availability-item .availability-delete img" "css_element" should be visible
    And the "alt" attribute of ".availability-item .availability-eye img" "css_element" should contain "Displayed greyed-out"

    # Toggle the eye icon.
    When I click on ".availability-item .availability-eye img" "css_element"
    Then the "alt" attribute of ".availability-item .availability-eye img" "css_element" should contain "Hidden entirely"
    When I click on ".availability-item .availability-eye img" "css_element"
    Then the "alt" attribute of ".availability-item .availability-eye img" "css_element" should contain "Displayed greyed-out"

    # Click the delete button.
    When I click on ".availability-item .availability-delete img" "css_element"
    Then I should not see "Date" in the "Restrict access" "fieldset"

    # Add a nested restriction set and check it appears.
    When I click on "Add restriction..." "button"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    Then ".availability-children .availability-list" "css_element" should be visible
    And I should see "None" in the ".availability-children .availability-list" "css_element"
    And I should see "Please set" in the ".availability-children .availability-list" "css_element"
    And I should see "Add restriction" in the ".availability-children .availability-list" "css_element"

    # Click on the button to add a restriction inside the nested set.
    When I click on "Add restriction..." "button" in the ".availability-children .availability-list" "css_element"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then I should not see "None" in the ".availability-children .availability-list" "css_element"
    And I should not see "Please set" in the ".availability-children .availability-list" "css_element"
    And I should see "Date" in the ".availability-children .availability-list" "css_element"

    # OK, let's delete the date inside the nested set...
    When I click on ".availability-item .availability-delete img" "css_element" in the ".availability-item" "css_element"
    Then I should not see "Date" in the ".availability-children .availability-list" "css_element"
    And I should see "None" in the ".availability-children .availability-list" "css_element"

    # ...and the nested set itself.
    When I click on ".availability-none .availability-delete img" "css_element"
    Then ".availability-children .availability-list" "css_element" should not exist

    # Add two dates so we can check the connectors.
    When I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then I should see "and" in the "Restrict access" "fieldset"
    And "Required restrictions" "select" should be visible

    # Try changing the connector type.
    When I set the field "Required restrictions" to "any"
    Then I should not see "and" in the "Restrict access" "fieldset"
    And I should see "or" in the "Restrict access" "fieldset"

    # Now delete one of the dates and check the connector goes away.
    When I click on ".availability-item .availability-delete img" "css_element"
    Then I should not see "or" in the "Restrict access" "fieldset"

    # Add a nested restriction set with two dates so there will be inner connector.
    When I click on "Add restriction..." "button"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button" in the ".availability-children .availability-list" "css_element"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button" in the ".availability-children .availability-list" "css_element"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then I should see "and" in the ".availability-children .availability-list .availability-connector" "css_element"

    # Check changing the outer one does not affect the inner one.
    When I set the field "Required restrictions" to "all"
    Then I should not see "or" in the "Restrict access" "fieldset"
    When I set the field "Required restrictions" to "any"
    Then I should see "or" in the "Restrict access" "fieldset"
    And I should not see "or" in the ".availability-children .availability-list .availability-connector" "css_element"

  @javascript
  Scenario: Edit availability using settings in section form
    # Set up.
    Given I log in as "admin"
    And I set the following administration settings values:
      | Enable conditional access | 1 |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

    # Edit a section
    When I edit the section "1"
    And I expand all fieldsets
    Then I should see "None" in the "Restrict access" "fieldset"

    # Add a Date restriction and check it appears.
    When I click on "Add restriction..." "button"
    When I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I should not see "None" in the "Restrict access" "fieldset"
    And "Restriction type" "select" should be visible
    And I should see "Date" in the "Restrict access" "fieldset"
