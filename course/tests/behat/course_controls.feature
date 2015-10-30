@core @core_course
Feature: Course activity controls works as expected
  In order to manage my course's activities
  As a teacher
  I need to edit, hide, show and indent activities inside course sections

  # The difference between these two scenario outlines is that one is with
  # JS enabled and the other one with JS disabled; we can not use Background
  # sections when using Scenario Outlines because of Behat framework restrictions.

  # We are testing:
  # * Javascript on and off
  # * Topics and weeks course formats
  # * Course controls without paged mode
  # * Course controls with paged mode in the course home page
  # * Course controls with paged mode in a section's page

  @javascript @_cross_browser
  Scenario Outline: General activities course controls using topics and weeks formats, and paged mode and not paged mode works as expected
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1 | <courseformat> | <coursedisplay> | 5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I follow <targetpage>
    And I press "Turn editing on"
    Then I should see "Turn editing off"
    And I press "Turn editing off"
    And "Turn editing on" "button" should exist
    And I follow "Turn editing on"
    And "Turn editing off" "button" should exist
    And I follow "Turn editing off"
    And I should see "Turn editing on"
    And "Turn editing on" "button" should exist
    And I turn editing mode on
    And I click on "Actions" "link" in the "Recent activity" "block"
    And I click on "Delete Recent activity block" "link"
    And I press "Yes"
    And <belowpage> "section" <should_see_other_sections> exist
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 1 |
      | Description | Test forum description 1 |
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 2 |
      | Description | Test forum description 2 |
    And <belowpage> "section" <should_see_other_sections> exist
    And I indent right "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I indent left "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I open "Test forum name 1" actions menu
    And I click on "Edit settings" "link" in the "Test forum name 1" activity
    And I should see "Updating Forum"
    And I should see "Display description on course page"
    And I set the following fields to these values:
      | Forum name | Just to check that I can edit the name |
      | Description | Just to check that I can edit the description |
      | Display description on course page | 1 |
    And I click on "Cancel" "button"
    And <belowpage> "section" <should_see_other_sections> exist
    And I open "Test forum name 1" actions menu
    And I click on "Hide" "link" in the "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I delete "Test forum name 1" activity
    And I should not see "Test forum name 1" in the "#region-main" "css_element"
    And I duplicate "Test forum name 2" activity editing the new copy with:
      | Forum name | Edited test forum name 2 |
    And <belowpage> "section" <should_see_other_sections> exist
    And I should see "Test forum name 2"
    And I should see "Edited test forum name 2"
    And I hide section "1"
    And <belowpage> "section" <should_see_other_sections> exist
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And I show section "1"
    And <belowpage> "section" <should_see_other_sections> exist
    And section "1" should be visible
    And I add the "Section links" block
    And <belowpage> "section" <should_see_other_sections> exist
    And I should see "1 2 3 4 5" in the "Section links" "block"
    And I click on "2" "link" in the "Section links" "block"
    And I <should_see_other_sections_following_block_sections_links> see "Test forum name 2"

    Examples:
      | courseformat | coursedisplay | targetpage              | should_see_other_sections | should_see_other_sections_following_block_sections_links | belowpage                |
      | topics       | 0             | "Course 1"              | should                    | should                                                   | "Topic 2"                |
      | topics       | 1             | "Topic 1"               | should not                | should not                                               | "Topic 2"                |
      | topics       | 1             | "Course 1"              | should                    | should not                                               | "Topic 2"                |
      | weeks        | 0             | "Course 1"              | should                    | should                                                   | "8 January - 14 January" |
      | weeks        | 1             | "1 January - 7 January" | should not                | should not                                               | "8 January - 14 January" |
      | weeks        | 1             | "Course 1"              | should                    | should not                                               | "8 January - 14 January" |

  Scenario Outline: General activities course controls using topics and weeks formats, and paged mode and not paged mode works as expected
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1 | <courseformat> | <coursedisplay> | 5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I follow <targetpage>
    And I press "Turn editing on"
    Then I should see "Turn editing off"
    And I press "Turn editing off"
    And "Turn editing on" "button" should exist
    And I follow "Turn editing on"
    And "Turn editing off" "button" should exist
    And I follow "Turn editing off"
    And I should see "Turn editing on"
    And "Turn editing on" "button" should exist
    And I turn editing mode on
    And I click on "Actions" "link" in the "Recent activity" "block"
    And I click on "Delete Recent activity block" "link"
    And I press "Yes"
    And <belowpage> "section" <should_see_other_sections> exist
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 1 |
      | Description | Test forum description 1 |
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name 2 |
      | Description | Test forum description 2 |
    And <belowpage> "section" <should_see_other_sections> exist
    And I indent right "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I indent left "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I click on "Edit settings" "link" in the "Test forum name 1" activity
    And I should see "Updating Forum"
    And I should see "Display description on course page"
    And I press "Save and return to course"
    And <belowpage> "section" <should_see_other_sections> exist
    And I click on "Hide" "link" in the "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I delete "Test forum name 1" activity
    And <belowpage> "section" <should_see_other_sections> exist
    And I should not see "Test forum name 1" in the "#region-main" "css_element"
    And I duplicate "Test forum name 2" activity editing the new copy with:
      | Forum name | Edited test forum name 2 |
    And <belowpage> "section" <should_see_other_sections> exist
    And I should see "Test forum name 2"
    And I should see "Edited test forum name 2"
    And I hide section "1"
    And <belowpage> "section" <should_see_other_sections> exist
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And I show section "1"
    And <belowpage> "section" <should_see_other_sections> exist
    And section "1" should be visible
    And I add the "Section links" block
    And <belowpage> "section" <should_see_other_sections> exist
    And I should see "1 2 3 4 5" in the "Section links" "block"
    And I click on "2" "link" in the "Section links" "block"
    And I <should_see_other_sections_following_block_sections_links> see "Test forum name 2"

    Examples:
      | courseformat | coursedisplay | targetpage              | should_see_other_sections | should_see_other_sections_following_block_sections_links | belowpage                |
      | topics       | 0             | "Course 1"              | should                    | should                                                   | "Topic 2"                |
      | topics       | 1             | "Topic 1"               | should not                | should not                                               | "Topic 2"                |
      | topics       | 1             | "Course 1"              | should                    | should not                                               | "Topic 2"                |
      | weeks        | 0             | "Course 1"              | should                    | should                                                   | "8 January - 14 January" |
      | weeks        | 1             | "1 January - 7 January" | should not                | should not                                               | "8 January - 14 January" |
      | weeks        | 1             | "Course 1"              | should                    | should not                                               | "8 January - 14 January" |
