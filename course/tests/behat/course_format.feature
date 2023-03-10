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
    # Course format is initially set to Topics format
    And the following "courses" exist:
      | fullname | shortname | format | startdate       |
      | Course 1 | C1        | topics | ## 1 day ago ## |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    # Confirm that course format is Topics
    When I am on the "Course 1" course page logged in as teacher1
    Then I should see "Topic 1"
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    # Fields that appear for Topics format exist
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
    And I set the field "Format" to "Single activity format"
    And I expand all fieldsets
    # Confirm that fields that appear for Single activity format appears
    And I should see "Type of activity"
    And I set the field "Type of activity" to "Glossary"
    And I press "Save and display"
    And I set the field "Name" to "Glossary 1"
    And I press "Save and display"
    # Confirm that course page displays single activity of type Glossary
    And I should see "Browse the glossary using this index"
    And I should not see "Topic 1"
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    # Set course format to Weekly format
    And I set the field "Format" to "Weekly format"
    And I expand all fieldsets
    # Confirm that fields that appear for Weekly format appears
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
    And I should see "This week"
    And I should not see "Browse the glossary using this index"
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    # Set course format to Social format
    And I set the field "Format" to "Social format"
    # Confirm that fields that appear for Social format appears
    And I expand all fieldsets
    And I should see "Number of discussions"
    And the field "Number of discussions" matches value "10"
    And I press "Save and display"
    # Confirm that course page displays a forum
    And I should see "There are no discussion topics yet in this forum"
    And I should not see "This week"
