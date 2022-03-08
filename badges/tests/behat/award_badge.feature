@core @core_badges @_file_upload
Feature: Award badges
  In order to award badges to users for their achievements
  As an admin
  I need to add criteria to badges in the system

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | section                             | 1                       |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | completion                          | 1                       |
      | assignsubmission_onlinetext_enabled | 1                       |

  @javascript
  Scenario: Award badge on other badges as criteria
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Create course badge 1.
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge 1 |
      | Description | Course badge 1 description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    # Set to ANY of the roles awards badge.
    And I set the field "Teacher" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    # Badge #2
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge 2 |
      | Description | Course badge 2 description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    # Set "course badge 1" as criteria
    And I set the field "type" to "Awarded badges"
    And I set the field "id_badge_badges" to "Course Badge 1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge 1"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    # Award course badge 1 to student 1.
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge 1"
    And I select "Recipients (1)" from the "jump" singleselect
    Then I should see "Recipients (1)"
    And I log out
    # Student 1 should have both badges.
    And I log in as "student1"
    And I follow "Profile" in the user menu
    When I click on "Course 1" "link" in the "region-main" "region"
    Then I should see "Course Badge 1"
    And I should see "Course Badge 2"
    # Student 1 should have both badges also in the Badges navigation section.
    When I follow "Badges"
    Then I should see "Course Badge 1"
    And I should see "Course Badge 2"
    And I should not see "Manage badges"
    And I should not see "Add a new badge"
    And I log out
    # Teacher 1 should have access to manage/create badges in the Badges navigation section.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    Then I should see "Course Badge 1"
    And I should see "Course Badge 2"
    And I should see "Manage badges"
    And I should see "Add a new badge"
    # Teacher 1 should NOT have access to manage/create site badges in the Site badges section.
    When I am on homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
   # TODO MDL-57120 site "Badges" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Site badges" "link" in the "Navigation" "block"
    Then I should see "There are currently no badges available for users to earn."
    And I should not see "Manage badges"
    And I should not see "Add a new badge"

  @javascript
  Scenario: Award profile badge
    Given I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Profile Badge |
      | Description | Test badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Profile completion"
    And I expand all fieldsets
    And I set the field "First name" to "1"
    And I set the field "Email address" to "1"
    And I set the field "Phone" to "1"
    And I set the field "id_description" to "Criterion description"
    When I press "Save"
    Then I should see "Profile completion"
    And I should see "First name"
    And I should see "Email address"
    And I should see "Phone"
    And I should see "Criterion description"
    And I should not see "Criteria for this badge have not been set up yet."
    And I press "Enable access"
    And I press "Continue"
    And I open my profile in edit mode
    And I expand all fieldsets
    And I set the field "Phone" to "123456789"
    And I press "Update profile"
    And I follow "Profile" in the user menu
    Then I should see "Profile Badge"
    And I should not see "There are currently no badges available for users to earn."

  @javascript
  Scenario: Award site badge
    Given I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    And I navigate to "Badges > Manage badges" in site administration
    When I follow "Site Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    Then I should see "Site Badge"

  @javascript
  Scenario: Award course badge
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I click on "Course Badge" "link"
    And I should see "Course: Course 1"

  @javascript
  Scenario: Award badge on activity completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I change window size to "large"
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Activity completion"
    And I set the field "Test assignment name" to "1"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    Then I should not see "badges"
    And I am on "Course 1" course homepage
    And I toggle the manual completion state of "Test assignment name"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    Then I should see "Course Badge"

  @javascript
  Scenario: Award badge on course completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Course completion" in current page administration
    And I set the field "id_overall_aggregation" to "2"
    And I click on "Condition: Activity completion" "link"
    And I set the field "Assignment - Test assignment name" to "1"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Course completion"
    And I set the field with xpath ".//*[contains(., 'Minimum grade required')]/ancestor::*[contains(concat(' ', @class, ' '), ' fitem ')]//input[1]" to "0"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    And I log out
    And I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    Then I should not see "badges"
    And I am on "Course 1" course homepage
    And I toggle the manual completion state of "Test assignment name"
    And I log out
    # Completion cron won't mark the whole course completed unless the
    # individual criteria was marked completed more than a second ago. So
    # run it twice, first to mark the criteria and second for the course.
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    # The student should now see their badge.
    And I log in as "student1"
    And I follow "Profile" in the user menu
    Then I should see "Course Badge"

  @javascript
  Scenario: All of the selected roles can award badges
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Create course badge 1.
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge 1 |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    # Set to ANY of the roles awards badge.
    And I set the field "Teacher" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    # Award course badge 1 to student 1.
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge 1"
    And I select "Recipients (1)" from the "jump" singleselect
    Then I should see "Recipients (1)"
    # Add course badge 2.
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge 2 |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    # Set to ALL of the selected roles award badge.
    And I set the field "Teacher" to "1"
    And I set the field "All of the selected roles award the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    # Award course badge 2 to student 2.
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    When I press "Award badge"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge 2"
    And I select "Recipients (1)" from the "jump" singleselect
    Then I should see "Recipients (1)"
    And I log out
    And I trigger cron
    # Student 1 should have just course badge 1.
    And I log in as "student1"
    And I follow "Profile" in the user menu
    When I click on "Course 1" "link" in the "region-main" "region"
    Then I should see "Course Badge 1"
    And I should not see "Course Badge 2"
    And I log out
    # Student 2 should have just course badge 2.
    And I log in as "student2"
    And I follow "Profile" in the user menu
    When I click on "Course 1" "link" in the "region-main" "region"
    Then I should see "Course Badge 2"
    Then I should not see "Course Badge 1"

  @javascript
  Scenario: Revoke badge
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Award badge"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge"
    Then I should see "Recipients (2)"
    And I select "Recipients (2)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "existingrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Revoke badge"
    And I set the field "existingrecipients[]" to "Student 1 (student1@example.com)"
    When I press "Revoke badge"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Course Badge"
    Then I should see "Recipients (0)"
