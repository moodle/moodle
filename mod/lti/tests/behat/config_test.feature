@mod @mod_lti @core_backup @javascript
Feature: Restoring Moodle 2 backup restores LTI configuration

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | idnumber |
      | Course 1 | C1 | 0 | C1 |
    And I log in as "admin"

  Scenario: Backup and restore course 1
    Given I navigate to "Manage external tool types" node in "Site administration > Plugins > Activity modules > LTI"
    And I follow "Add external tool configuration"
    And I set the following fields to these values:
      | Tool name | Site Netspot Tool |
      | Tool base URL | http://www.netspot.com.au |
      | lti_coursevisible | 1 |
    And I press "Save changes"
    And I wait to be redirected
    Given I am on site homepage
    Then I follow "Course 1"
    Then I turn editing mode on
    And I add a "External tool" to section "1" and I fill the form with:
        | Activity name | Site Netspot Tool |
        | External tool type | Site Netspot Tool |
        | Launch container | Embed |
    And I follow "Course 1"
    Then I should see "Site Netspot Tool"
    Then I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
    Then I am on site homepage
    And I follow "Course 1 copy 1"
    And I open "Site Netspot Tool" actions menu
    And I click on "Edit settings" "link" in the "Site Netspot Tool" activity
    Then the field "External tool type" matches value "Site Netspot Tool"