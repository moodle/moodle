@core @core_admin
Feature: Administrator is warned and when trying to set invalid allcountrycodes value.
  In order to avoid misconfiguration of the country selector fields
  As an admin
  I want to be warned when I try to set an invalid country code in the allcountrycodes field

  Scenario: Attempting to set allcountrycodes field with valid country codes
    Given I log in as "admin"
    And I navigate to "Location > Location settings" in site administration
    When I set the following administration settings values:
      | All country codes | CZ,BE,GB,ES |
    Then I should not see "Invalid country code"

  Scenario: Attempting to set allcountrycodes field with invalid country code
    Given I log in as "admin"
    And I navigate to "Location > Location settings" in site administration
    When I set the following administration settings values:
      | All country codes | CZ,BE,FOOBAR,GB,ES |
    Then I should see "Invalid country code: FOOBAR"

  Scenario: Attempting to unset allcountrycodes field
    Given I log in as "admin"
    And I navigate to "Location > Location settings" in site administration
    And I set the following administration settings values:
      | All country codes | CZ,BE,GB,ES |
    And I navigate to "Location > Location settings" in site administration
    When I set the following administration settings values:
      | All country codes | |
    Then I should not see "Invalid country code"
