@core @core_course @core_courseformat
Feature: Move activity using keyboard
  In order to move activities without a mouse
  As a user
  I need to select the activity destination with the keyboard.

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 4        |
      | initsections     | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | book     | Activity sample 2 |                             | C1     | sample2  | 2       |
      | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 3       |
      | choice   | Other sample 3    | Test choice description     | C1     | sample31 | 3       |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Move activity to another section selecting the section name
    Given I open "Activity sample 3" actions menu
    And I click on "Move" "link" in the "Activity sample 3" activity
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Select the section 2.
    And I press the down key
    And I press the down key
    And I press enter
    Then I should see "Activity sample 3" in the "Section 2" "section"

  @javascript
  Scenario: Move activity to another section selecting an inner activity
    Given I open "Activity sample 3" actions menu
    And I click on "Move" "link" in the "Activity sample 3" activity
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Open section 2.
    And I press the down key
    And I press the down key
    And I press the right key
    # Select first activity.
    And I press the down key
    And I press enter
    Then I should see "Activity sample 3" in the "Section 2" "section"

  @javascript
  Scenario: Close a section in the move modal
    Given I open "Activity sample 3" actions menu
    And I click on "Move" "link" in the "Activity sample 3" activity
    And I should see "Activity sample 3" in the ".modal-body" "css_element"
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Close section 3.
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the left key
    # Move to section 4.
    And I press the down key
    And I press enter
    Then I should see "Activity sample 3" in the "Section 4" "section"

  @javascript
  Scenario: Move activity using open all sections
    Given I open "Activity sample 3" actions menu
    And I click on "Move" "link" in the "Activity sample 3" activity
    And I should see "Activity sample 3" in the ".modal-body" "css_element"
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Open all sections.
    And I press the multiply key
    # Move down to section 4
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And I press enter
    Then I should see "Activity sample 3" in the "Section 4" "section"

  @javascript
  Scenario: Move activity using go to the last element
    Given I open "Activity sample 3" actions menu
    And I click on "Move" "link" in the "Activity sample 3" activity
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Go to the last section.
    And I press the end key
    # Move down to section 4
    And I press enter
    Then I should see "Activity sample 3" in the "Section 4" "section"

  @javascript
  Scenario: Move activity using go to the first element
    Given I open "Activity sample 3" actions menu
    And I click on "Move" "link" in the "Activity sample 3" activity
    And I should see "Activity sample 3" in the ".modal-body" "css_element"
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Move some sections down.
    And I press the down key
    And I press the down key
    And I press the down key
    # Go to the first section.
    And I press the home key
    # Move down to general section
    And I press enter
    Then I should see "Activity sample 3" in the "General" "section"
