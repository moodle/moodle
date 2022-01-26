@core @core_course @core_courseformat
Feature: Move a section using keyboard
  In order to move sections without a mouse
  As a user
  I need to select the section destination with the keyboard.

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 4        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | book     | Activity sample 2 | Test book description       | C1     | sample2  | 2       |
      | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 3       |
      | choice   | Other sample 3    | Test choice description     | C1     | sample31 | 3       |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Move section above another section
    Given I open section "3" edit menu
    And I click on "Move" "link" in the "Topic 3" "section"
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Select the section 2.
    And I press the down key
    And I press the down key
    And I press enter
    Then I should see "Activity sample 3" in the "Topic 2" "section"

  @javascript
  Scenario: Move section using go to the last element
    Given I open section "2" edit menu
    And I click on "Move" "link" in the "Topic 2" "section"
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Go to the last section.
    And I press the end key
    # Move down to section 4
    And I press enter
    Then I should see "Activity sample 2" in the "Topic 4" "section"

  @javascript
  Scenario: Move section using go to the first element
    Given I open section "3" edit menu
    And I click on "Move" "link" in the "Topic 3" "section"
    # Focus on the modal content tree.
    When I press the tab key
    And I press the tab key
    # Move some sections down.
    And I press the down key
    And I press the down key
    And I press the down key
    # Go to the first section.
    And I press the home key
    # Move down to Topic 1
    And I press the down key
    And I press enter
    Then I should see "Activity sample 3" in the "Topic 1" "section"
