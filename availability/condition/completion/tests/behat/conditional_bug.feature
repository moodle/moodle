@availability @availability_completion
Feature: Confirm that conditions on completion no longer cause a bug
  In order to use completion conditions
  As a teacher
  I need it to not break when I set up certain conditions on some modules

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Multiple completion conditions on glossary
    # Set up course.
    Given I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Enable completion tracking" to "Yes"
    And I press "Save and display"
    And I turn editing mode on
    # Add a couple of Pages with manual completion.
    And the following "activities" exist:
      | activity | course | name  | completion |
      | page     | C1     | Page1 | 1          |
      | page     | C1     | Page2 | 1          |

    # Add a Glossary.
    When I add a "Glossary" to section "1"
    And I set the following fields to these values:
      | Name | TestGlossary |
    And I expand all fieldsets

    # Add restrictions to the previous Pages being complete.
    And I press "Add restriction..."
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "Page1"
    And I press "Add restriction..."
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field with xpath "//div[contains(concat(' ', normalize-space(@class), ' '), ' availability-item ')][preceding-sibling::div]//select[@name='cm']" to "Page2"
    And I press "Save and return to course"
    And I click on "Show more" "button" in the "TestGlossary" "core_availability > Activity availability"
    Then I should see "Not available unless:" in the ".activity.glossary" "css_element"
    And I should see "The activity Page1 is marked complete" in the ".activity.glossary" "css_element"
    And I should see "The activity Page2 is marked complete" in the ".activity.glossary" "css_element"

    # Behat will automatically check there is no error on this page.
    And I am on the TestGlossary "glossary activity" page
    And I should see "TestGlossary"
