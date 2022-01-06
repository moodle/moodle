@core @core_badges @_file_upload
Feature: Award badges based on user profile field
  In order to award badges to users based on completion of their user profile
  As an admin
  I need to add profile completion criteria to badges in the system

  @javascript
  Scenario: Award badge for a uploading a profile picture.
    Given the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Profile completion"
    And I set the field "id_field_picture" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I log out
    When I log in as "user1"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I upload "badges/tests/behat/badge.png" file to "New picture" filemanager
    And I press "Update profile"
    Then I should see "Site Badge"
