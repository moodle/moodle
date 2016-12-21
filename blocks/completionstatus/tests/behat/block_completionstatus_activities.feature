@block @block_completionstatus
Feature: Block Completion in a course details view
  In order to view the details of course completion in a course
  As a student,
  I can see some hidden/unavailable resources.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | numsections |
      | Course 1 | C1        | 0        | 1                | 4           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name   | intro                    | course | idnumber    | section | visible | completion | completionview |
      | page       | task A | page description         | C1     | page1       | 0       | 1       | 2          | 1              |
      | page       | task B | page description         | C1     | page2       | 0       | 1       | 2          | 1              |
      | assign     | task C | assignment  description  | C1     | assign1     | 1       | 1       | 2          | 1              |
      | assign     | task D | assignment description   | C1     | assign2     | 1       | 0       | 2          | 1              |
      | assign     | task E | assignment description   | C1     | assign3     | 1       | 1       | 2          | 1              |
      | assign     | task F | ssignment description    | C1     | assign4     | 1       | 1       | 2          | 1              |
      | book       | task G | book description         | C1     | book1       | 2       | 1       | 2          | 1              |
      | book       | task H | book description         | C1     | book2       | 2       | 0       | 2          | 1              |
      | book       | task I | book description         | C1     | book3       | 2       | 1       | 2          | 1              |
      | book       | task J | book description         | C1     | book4       | 2       | 1       | 2          | 1              |
      | chat       | task K | chat description         | C1     | chat1       | 3       | 1       | 2          | 1              |
      | chat       | task L | chat description         | C1     | chat2       | 3       | 0       | 2          | 1              |
      | chat       | task M | chat description         | C1     | chat3       | 3       | 1       | 2          | 1              |
      | chat       | task N | chat description         | C1     | chat4       | 3       | 1       | 2          | 1              |
      | choice     | task O | choice description       | C1     | choice1     | 4       | 1       | 2          | 1              |
      | choice     | task P | choice description       | C1     | choice2     | 4       | 0       | 2          | 1              |
      | choice     | task Q | choice description       | C1     | choice3     | 4       | 1       | 2          | 1              |
      | choice     | task R | choice description       | C1     | choice4     | 4       | 1       | 2          | 1              |

  @javascript
  Scenario: As a student, I see all resource titles in the detailed view of a course completion status block
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Course completion status" block
    And I navigate to "Course completion" node in "Course administration"
    And I expand all fieldsets
    # Select all only 3.2+
    And I set the field "Page - task A" to "1"
    And I set the field "Page - task B" to "1"
    And I set the field "Assignment - task C" to "1"
    And I set the field "Assignment - task D" to "1"
    And I set the field "Assignment - task E" to "1"
    And I set the field "Assignment - task F" to "1"
    And I set the field "Book - task G" to "1"
    And I set the field "Book - task H" to "1"
    And I set the field "Book - task I" to "1"
    And I set the field "Book - task J" to "1"
    And I set the field "Chat - task K" to "1"
    And I set the field "Chat - task L" to "1"
    And I set the field "Chat - task M" to "1"
    And I set the field "Chat - task N" to "1"
    And I set the field "Choice - task O" to "1"
    And I set the field "Choice - task P" to "1"
    And I set the field "Choice - task Q" to "1"
    And I set the field "Choice - task R" to "1"
    And I press "Save changes"
    
    And I follow "task E"
    And I navigate to "Edit settings" node in "Assignment administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"
    And I follow "task F"
    And I navigate to "Edit settings" node in "Assignment administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"

    And I follow "task I"
    And I navigate to "Edit settings" node in "Book administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"
    And I follow "task J"
    And I navigate to "Edit settings" node in "Book administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"
    
    And I follow "task M"
    And I navigate to "Edit settings" node in "Chat administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"
    And I follow "task N"
    And I navigate to "Edit settings" node in "Chat administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"

    And I follow "task Q"
    And I navigate to "Edit settings" node in "Choice administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"
    And I follow "task R"
    And I navigate to "Edit settings" node in "Choice administration"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Activity or resource" to "task B"
    And I press "Save and return to course"

    And I click on "Edit" "link" in the "li#section-2" "css_element"
    And I click on "Hide topic" "link" in the "li#section-2" "css_element"
    And I click on "Edit" "link" in the "li#section-3" "css_element"
    And I click on "Edit topic" "link" in the "li#section-3" "css_element"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I press "Save changes"
    And I click on "Edit" "link" in the "li#section-4" "css_element"
    And I click on "Edit topic" "link" in the "li#section-4" "css_element"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "task A"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I press "Save changes"
    And I log out

    And I log in as "student1"
    And I follow "Course 1"
    And I follow "More details"
    Then I should see "task A"
    And I should see "task B"
    And I should see "task C"
    And I should see "task D" in the "table#criteriastatus .r1 .dimmed_text" "css_element"
    And I should see "task E"
    And I should see "task F"
    And I should see "task G"
    And I should see "task H"
    And I should see "task I"
    And I should see "task J"
    And I should see "task K"
    And I should see "task L"
    And I should see "task M"
    And I should see "task N"
    And I should see "task O"
    And I should see "task P"
    And I should see "task Q"
    And I should see "task R"
    And I log out

    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Course completion status" node in "Site administration > Plugins > Blocks"
    And I click on "Reveal hidden/unavailable titles" "checkbox"
    And I press "Save changes"
    And I log out

    And I log in as "student1"
    And I follow "Course 1"
    And I follow "More details"
    Then I should see "task A"
    And I should see "task B"
    And I should see "task C"
    And I should not see "task D" in the "table#criteriastatus .r1 .dimmed_text" "css_element"
    And I should see "task E"
    And I should not see "task F"
    And I should not see "task G"
    And I should not see "task H"
    And I should not see "task I"
    And I should not see "task J"
    And I should not see "task K"
    And I should not see "task L"
    And I should not see "task M"
    And I should not see "task N"
    And I should not see "task O"
    And I should not see "task P"
    And I should not see "task Q"
    And I should not see "task R"
    And I follow "task A"

    And I am on site homepage
    And I follow "Course 1"
    And I follow "More details"
    Then I should see "task A"
    And I should see "task B"
    And I should see "task C"
    And I should not see "task D"
    And I should see "task E"
    And I should not see "task F"
    And I should not see "task G"
    And I should not see "task H"
    And I should not see "task I"
    And I should not see "task J"
    And I should see "task K"
    And I should not see "task L"
    And I should see "task M"
    And I should not see "task N"
    And I should see "task O"
    And I should not see "task P"
    And I should see "task Q"
    And I should not see "task R"
    And I follow "task B"

    And I am on site homepage
    And I follow "Course 1"
    And I follow "More details"
    Then I should see "task A"
    And I should see "task B"
    And I should see "task C"
    And I should not see "task D"
    And I should see "task E"
    And I should see "task F"
    And I should not see "task G"
    And I should not see "task H"
    And I should not see "task I"
    And I should not see "task J"
    And I should see "task K"
    And I should not see "task L"
    And I should see "task M"
    And I should see "task N"
    And I should see "task O"
    And I should not see "task P"
    And I should see "task Q"
    And I should see "task R"
    And I log out
    
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Course completion status" node in "Site administration > Plugins > Blocks"
    And I click on "Reveal hidden/unavailable titles" "checkbox"
    And I press "Save changes"
    And I log out

    And I log in as "student1"
    And I follow "Course 1"
    And I follow "More details"
    Then I should see "task A"
    And I should see "task D" in the "table#criteriastatus .dimmed_text" "css_element"
    And I should see "task B"
    And I should see "task C"
    And I should see "task D"
    And I should see "task E"
    And I should see "task F"
    And I should see "task G"
    And I should see "task H"
    And I should see "task I"
    And I should see "task J"
    And I should see "task K"
    And I should see "task L"
    And I should see "task M"
    And I should see "task N"
    And I should see "task O"
    And I should see "task P"
    And I should see "task Q"
    And I should see "task R"
    