@core @core_course
Feature: Teacher can change the course format
  In order to change course format
  As a teacher
  I should be able to edit a course

  @javascript
  Scenario: Teacher can change the course format
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    # Course format is initially set to Custom sections format
    And the following "course" exists:
      | fullname     | Course 1        |
      | shortname    | C1              |
      | format       | topics          |
      | startdate    | ## 1 day ago ## |
      | initsections | 1               |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    # Confirm that course format is Custom sections.
    When I am on the "Course 1" course page logged in as teacher1
    Then I should see "Section 1"
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    # Fields that appear for Custom sections format exist
    # Also confirm contents of Hidden sections and Course layout select box
    And I should see "Hidden sections"
    And the "Hidden sections" select box should contain "Hidden sections are shown as not available"
    And the "Hidden sections" select box should contain "Hidden sections are completely invisible"
    # Hidden sections default value is 1 (Hidden sections are completely invisible)
    And the field "Hidden sections" matches value "1"
    And I should see "Course layout"
    And the "Course layout" select box should contain "Show all sections on one page"
    And the "Course layout" select box should contain "Show one section per page"
    # Course layout default value is 0 (Show all sections on one page)
    And the field "Course layout" matches value "0"
    # Set course format to Single activity format
    And I set the field "Format" to "Single activity"
    And I expand all fieldsets
    # Confirm that fields that appear for Single activity format appears
    And I should see "Type of activity"
    And I set the field "Type of activity" to "Glossary"
    And I press "Save and display"
    And I set the field "Name" to "Glossary 1"
    And I press "Save and display"
    # Confirm that course page displays single activity of type Glossary
    And I should see "Browse the glossary using this index"
    And I should not see "Section 1"
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    # Set course format.
    And I set the field "Format" to "Weekly sections"
    And I expand all fieldsets
    # Confirm that fields that appear for Weekly sections format appears
    # Also confirm contents of Hidden sections and Course layout select box
    And I should see "Hidden sections"
    And the "Hidden sections" select box should contain "Hidden sections are shown as not available"
    And the "Hidden sections" select box should contain "Hidden sections are completely invisible"
    # Hidden sections default value is 1 (Hidden sections are completely invisible)
    And the field "Hidden sections" matches value "1"
    And I should see "Course layout"
    And the "Course layout" select box should contain "Show all sections on one page"
    And the "Course layout" select box should contain "Show one section per page"
    # Course layout default value is 0 (Show all sections on one page)
    And the field "Course layout" matches value "0"
    And I press "Save and display"
    # Confirm that course page displays weekly sections
    And I should see "Current week"
    And I should not see "Browse the glossary using this index"
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    # Set course format to Social format
    And I set the field "Format" to "Social"
    # Confirm that fields that appear for Social format appears
    And I expand all fieldsets
    And I should see "Number of discussions"
    And the field "Number of discussions" matches value "10"
    And I press "Save and display"
    # Confirm that course page displays a forum
    And I should see "There are no discussion topics yet in this forum"
    And I should not see "Current week"
