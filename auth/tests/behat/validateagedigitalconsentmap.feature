@core @verify_age_location
Feature: Test validation of 'Age of digital consent' setting.
  In order to set the 'Age of digital consent' setting
  As an admin
  I need to provide valid data and valid format

  Background:
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Privacy settings" in site administration

  Scenario: Admin provides valid value for 'Age of digital consent'.
    Given I set the field "s__agedigitalconsentmap" to multiline:
    """
    *, 16
    AT, 14
    BE, 14
    """
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should not see "Some settings were not changed due to an error."
    And I should not see "The digital age of consent is not valid:"

  Scenario: Admin provides invalid format for 'Age of digital consent'.
    # Try to set a value with missing space separator
    Given I set the field "s__agedigitalconsentmap" to multiline:
    """
    *16
    AT, 14
    BE, 14
    """
    When I press "Save changes"
    Then I should not see "Changes saved"
    And I should see "Some settings were not changed due to an error."
    And I should see "The digital age of consent is not valid: \"*16\" has more or less than one comma separator."
    # Try to set a value with missing default age of consent
    When I set the field "s__agedigitalconsentmap" to multiline:
    """
    AT, 14
    BE, 14
    """
    And I press "Save changes"
    Then I should not see "Changes saved"
    And I should see "Some settings were not changed due to an error."
    And I should see "The digital age of consent is not valid: Default (*) value is missing."

  Scenario: Admin provides invalid age of consent or country for 'Age of digital consent'.
    # Try to set a value containing invalid age of consent
    Given I set the field "s__agedigitalconsentmap" to multiline:
    """
    *, 16
    AT, age
    BE, 14
    """
    When I press "Save changes"
    Then I should not see "Changes saved"
    And I should see "Some settings were not changed due to an error."
    And I should see "The digital age of consent is not valid: \"age\" is not a valid value for age."
    # Try to set a value containing invalid country
    When I set the field "s__agedigitalconsentmap" to multiline:
    """
    *, 16
    COUNTRY, 14
    BE, 14
    """
    And I press "Save changes"
    Then I should not see "Changes saved"
    And I should see "Some settings were not changed due to an error."
    And I should see "The digital age of consent is not valid: \"COUNTRY\" is not a valid value for country."
