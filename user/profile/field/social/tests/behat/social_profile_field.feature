@core @core_user
Feature: Social profile fields can not have a duplicate shortname.
  In order edit social profile fields properly
  As an admin
  I should not be able to create duplicate shortnames for social profile fields.

  @javascript
  Scenario: Verify you can edit social profile fields.
    Given I log in as "admin"
    When I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Social" "link"
    And I set the following fields to these values:
      | Network type                  | Yahoo ID         |
      | Short name                    | yahoo            |
    And I click on "Save changes" "button"
    And I click on "Create a new profile field" "link"
    And I click on "Social" "link"
    And I set the following fields to these values:
      | Network type                  | Yahoo ID         |
      | Short name                    | yahoo            |
    And I click on "Save changes" "button"
    Then I should see "This short name is already in use"
