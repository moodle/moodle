@core @core_user
Feature: Social profile fields can not have a duplicate shortname.
  In order edit social profile fields properly
  As an admin
  I should not be able to create duplicate shortnames for social profile fields.

  @javascript
  Scenario: Verify you can edit social profile fields.
    Given I log in as "admin"
    When I navigate to "Users > Accounts > User profile fields" in site administration
    And I set the field "datatype" to "Social"
    And I set the following fields to these values:
      | Short name                    | yahoo            |
      | Networktype                   | Yahoo ID         |
    And I click on "Save changes" "button"
    And I set the field "datatype" to "Social"
    And I set the following fields to these values:
      | Short name                    | yahoo            |
      | Networktype                   | Yahoo ID         |
    And I click on "Save changes" "button"
    Then I should see "This short name is already in use"
