@core @core_badges
Feature: Badge overview
  In order to view a badge's information
  As an user with the capability to view badges
  I need to access the badge overview page

  Background:
    Given the following config values are set as admin:
      | badges_defaultissuername    | Test Badge Site      |

  @javascript @accessibility @_file_upload
  Scenario: Test accessibility of badge overview page
    Given I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name        | Cool badge            |
      | Description | Badge for cool people |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    When I select "Overview" from the "Badges navigation" singleselect
    Then the "region-main" "region" should meet accessibility standards with "best-practice" extra tests
