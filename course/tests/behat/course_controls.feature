@core @core_course
Feature: Course activity controls works as expected
  In order to manage my course's activities
  As a teacher
  I need to edit, hide, show and indent activities inside course sections

  # The difference between these two scenario outlines is that one is with
  # JS enabled and the other one with JS disabled, also with JS disabled we
  # add the delete activity checking; we can not use Background sections
  # when using Scenario Outlines because of Behat framework restrictions.

  # We are testing:
  # * Javascript on and off
  # * Topics and weeks course formats
  # * Course controls without paged mode
  # * Course controls with paged mode in the course home page
  # * Course controls with paged mode in a section's page

  @javascript @_cross_browser
  Scenario Outline: General activities course controls using topics and weeks formats, and paged mode and not paged mode works as expected
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1 | <courseformat> | <coursedisplay> | 5 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I follow <targetpage>
    And I press "Turn editing on"
    Then I should see "Turn editing off"
    And I press "Turn editing off"
    And "Turn editing on" "button" should exists
    And I follow "Turn editing on"
    And "Turn editing off" "button" should exists
    And I follow "Turn editing off"
    And I should see "Turn editing on"
    And "Turn editing on" "button" should exists
    And I turn editing mode on
    And I click on "Delete Recent activity block" "link"
    And I press "Yes"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 1 |
      | Description | Test forum description 1 |
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 2 |
      | Description | Test forum description 2 |
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I indent right "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I indent left "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I click on "Update" "link" in the "Test forum name 1" activity
    And I should see "Updating Forum"
    And I should see "Display description on course page"
    And I press "Save and return to course"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I click on "Hide" "link" in the "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I duplicate "Test forum name 2" activity editing the new copy with:
      | Forum name | Edited test forum name 2 |
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I should see "Test forum name 2"
    And I should see "Edited test forum name 2"
    And I hide section "1"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And section "1" should be hidden
    And I show section "1"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And section "1" should be visible
    And I add the "Section links" block
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I should see "1 2 3 4 5" in the "Section links" "block"
    And I click on "2" "link" in the "Section links" "block"
    And I <should_see_other_sections_following_block_sections_links> see "Test forum name 2"

    Examples:
      | courseformat | coursedisplay | targetpage              | should_see_other_sections | should_see_other_sections_following_block_sections_links |
      | topics       | 0             | "Course 1"              | should                    | should                                                   |
      | topics       | 1             | "Topic 1"               | should not                | should not                                               |
      | topics       | 1             | "Course 1"              | should                    | should not                                               |
      | weeks        | 0             | "Course 1"              | should                    | should                                                   |
      | weeks        | 1             | "1 January - 7 January" | should not                | should not                                               |
      | weeks        | 1             | "Course 1"              | should                    | should not                                               |


  Scenario Outline: General activities course controls using topics and weeks formats, and paged mode and not paged mode works as expected
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1 | <courseformat> | <coursedisplay> | 5 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I follow <targetpage>
    And I press "Turn editing on"
    Then I should see "Turn editing off"
    And I press "Turn editing off"
    And "Turn editing on" "button" should exists
    And I follow "Turn editing on"
    And "Turn editing off" "button" should exists
    And I follow "Turn editing off"
    And I should see "Turn editing on"
    And "Turn editing on" "button" should exists
    And I turn editing mode on
    And I click on "Delete Recent activity block" "link"
    And I press "Yes"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 1 |
      | Description | Test forum description 1 |
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 2 |
      | Description | Test forum description 2 |
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I indent right "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I indent left "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I click on "Update" "link" in the "Test forum name 1" activity
    And I should see "Updating Forum"
    And I should see "Display description on course page"
    And I press "Save and return to course"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I click on "Hide" "link" in the "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I delete "Test forum name 1" activity
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I should not see "Test forum name 1" in the ".region-content" "css_element"
    And I duplicate "Test forum name 2" activity editing the new copy with:
      | Forum name | Edited test forum name 2 |
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I should see "Test forum name 2"
    And I should see "Edited test forum name 2"
    And I hide section "1"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And section "1" should be hidden
    And I show section "1"
    And "#section-2" "css_element" <should_see_other_sections> exists
    And section "1" should be visible
    And I add the "Section links" block
    And "#section-2" "css_element" <should_see_other_sections> exists
    And I should see "1 2 3 4 5" in the "Section links" "block"
    And I click on "2" "link" in the "Section links" "block"
    And I <should_see_other_sections_following_block_sections_links> see "Test forum name 2"

    Examples:
      | courseformat | coursedisplay | targetpage              | should_see_other_sections | should_see_other_sections_following_block_sections_links |
      | topics       | 0             | "Course 1"              | should                    | should                                                   |
      | topics       | 1             | "Topic 1"               | should not                | should not                                               |
      | topics       | 1             | "Course 1"              | should                    | should not                                               |
      | weeks        | 0             | "Course 1"              | should                    | should                                                   |
      | weeks        | 1             | "1 January - 7 January" | should not                | should not                                               |
      | weeks        | 1             | "Course 1"              | should                    | should not                                               |
