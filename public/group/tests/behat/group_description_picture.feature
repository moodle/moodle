@core @core_group
Feature: The description and picture of a group can be viewed by students and teachers
  In order to view the description and picture of a group
  As a teacher
  I need to create groups and add descriptions and picture to them.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  @javascript @_file_upload
  Scenario: A student can see the group description and picture when visible groups are set. Teachers can see group details.
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "Course 1" "course editing" page logged in as "teacher1"
    And I set the following fields to these values:
      | Group mode | Visible groups |
    And I press "Save and display"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name        | <span lang="en" class="multilang">Group A & < > " '</span><span lang="de" class="multilang">Gruppe A </span> |
      | Group description | Description for Group A                                                                                      |
    # Upload group picture
    And I upload "lib/tests/fixtures/gd-logo.png" file to "New picture" filemanager
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group B |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group A" group members
    And I add "Student 2 (student2@example.com)" user to "Group B" group members
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    And I should see "Description for Group A"
    # As teacher, confirm that group picture is displayed
    And "//img[@class='grouppicture']" "xpath_element" should exist
    And ".groupinfobox" "css_element" should exist
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group B"
    And I click on "Apply filters" "button"
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And I should see "Student 2" in the "participants" "table"
    And ".groupinfobox" "css_element" should not exist
    When I am on the "Course 1" course page logged in as student1
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    # As student, confirm that group description and picture is displayed
    And I should see "Description for Group A"
    And "//img[@class='grouppicture']" "xpath_element" should exist
    And I am on the "Course 1" course page logged in as student2
    And I navigate to course participants
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And I should see "Student 2" in the "participants" "table"
    And ".groupinfobox" "css_element" should not exist
    # As teacher, confirm that the group picture in the edit form has the correct alt text
    And I am on the "Course 1" "groups" page logged in as teacher1
    And I set the field "groups" to "Group A"
    And I press "Edit group settings"
    Then the "alt" attribute of "//*[contains(@data-name, 'currentpicture')]//img" "xpath_element" should contain "Group A & <"
    But the "alt" attribute of "//*[contains(@data-name, 'currentpicture')]//img" "xpath_element" should not contain "span"
    And the "title" attribute of "//*[contains(@data-name, 'currentpicture')]//img" "xpath_element" should contain "Group A & <"
    But the "title" attribute of "//*[contains(@data-name, 'currentpicture')]//img" "xpath_element" should not contain "span"

  @javascript @_file_upload
  Scenario: A student can not see the group description and picture when separate groups are set. Teachers can see group details.
    Given I am on the "Course 1" "course editing" page logged in as "teacher1"
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group A |
      | Group description | Description for Group A |
    # Upload group picture
    And I upload "lib/tests/fixtures/gd-logo.png" file to "New picture" filemanager
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group B |
    And I press "Save changes"
    And I add "Student 1 (student1@example.com)" user to "Group A" group members
    And I add "Student 2 (student2@example.com)" user to "Group B" group members
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    And I should see "Description for Group A"
    # As teacher, confirm that group picture is displayed
    And "//img[@class='grouppicture']" "xpath_element" should exist
    And ".groupinfobox" "css_element" should exist
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group B"
    And I click on "Apply filters" "button"
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And ".groupinfobox" "css_element" should not exist
    When I am on the "Course 1" course page logged in as student1
    And I navigate to course participants
    And I click on "Student 1" "link" in the "participants" "table"
    And I click on "Group A" "link"
    And I should see "Student 1" in the "participants" "table"
    # As student, confirm that group description and picture are not displayed
    Then I should not see "Description for Group A"
    And "//img[@class='grouppicture']" "xpath_element" should not exist
    And ".groupinfobox" "css_element" should not exist
    When I am on the "Course 1" course page logged in as student2
    And I navigate to course participants
    And I click on "Student 2" "link" in the "participants" "table"
    And I click on "Group B" "link"
    And I should see "Student 2" in the "participants" "table"
    And ".groupinfobox" "css_element" should not exist
