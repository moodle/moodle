@tool @tool_dataprivacy
Feature: Protected data should not be deleted
  In order to delete data for users and meet legal requirements
  As an privacy office
  I need to be ensure that only expired or unprotected data is removed

  Background:
    Given the following "users" exist:
      | username  | firstname       | lastname  |
      | u1        | u1              | u1        |
    And the following "courses" exist:
      | fullname  | shortname  | startdate       | enddate         |
      | C1        | C1         | ##1 year ago##  | ##1 month ago##  |
      | C2        | C2         | ##1 year ago##  | ##last day of next month##     |
    And the following "course enrolments" exist:
      | user  | course  | role    |
      | u1    | C1      | student |
      | u1    | C2      | student |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | forum      | forump1                | Test forum description        | C1     | forump1      |
      | forum      | forumu1                | Test forum description        | C1     | forumu1      |
      | forum      | forump2                | Test forum description        | C2     | forump2      |
      | forum      | forumu2                | Test forum description        | C2     | forumu2      |
    And the following data privacy "categories" exist:
      | name          |
      | CAT           |
    And the following data privacy "purposes" exist:
      | name         | retentionperiod | protected  |
      | Site purpose | PT1H            | 0          |
      | prot         | P1D             | 1          |
      | unprot       | P1D             | 0          |
    And the following "mod_forum > discussions" exist:
      | user     | forum   | name                | message              |
      | u1       | forump1 | Discussion subject  | Test post in forump1 |
      | u1       | forumu1 | Discussion subject  | Test post in forumu1 |
      | u1       | forump2 | Discussion subject  | Test post in forump2 |
      | u1       | forumu2 | Discussion subject  | Test post in forumu2 |
    And I set the category and purpose for the "forump1" "forum" in course "C1" to "CAT" and "prot"
    And I set the category and purpose for the "forump2" "forum" in course "C2" to "CAT" and "prot"
    And I set the category and purpose for the "forumu1" "forum" in course "C1" to "CAT" and "unprot"
    And I set the category and purpose for the "forumu2" "forum" in course "C2" to "CAT" and "unprot"
    And I set the site category and purpose to "CAT" and "Site purpose"

  @javascripta
  Scenario: Unexpired and protected data is not removed
    Given  I log in as "admin"
    And I create a dataprivacy "delete" request for "u1"
    And I approve a dataprivacy "delete" request for "u1"
    And I run all adhoc tasks
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I should see "Deleted" in the "u1" "table_row"

    And I am on the "forump1" "forum activity" page
    And I follow "Discussion subject"
    Then I should not see "Test post in forump1"

    When I am on the "forumu1" "forum activity" page
    And I follow "Discussion subject"
    Then I should not see "Test post in forumu1"

    And I am on the "forump2" "forum activity" page
    And I follow "Discussion subject"
    Then I should see "Test post in forump2"

    When I am on the "forumu2" "forum activity" page
    And I follow "Discussion subject"
    Then I should not see "Test post in forumu2"
