@core
Feature: Read-only forms should work
  In order to use certain forms on large Moodle installations
  As a user
  Relevant featuers of non-editable forms should still work

  @javascript
  Scenario: Shortforms expand collapsing should work for read-only forms - one-section form
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name | intro                                                                        | course | idnumber |
      | label      | L1   | <a href="../lib/tests/fixtures/readonlyform.php?sections=1">Fixture link</a> | C1     | label1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Fixture link"
    When I expand all fieldsets
    Then the field "Name" matches value "Important information"

  @javascript
  Scenario: Shortforms expand collapsing should work for read-only forms - two-section form
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name | intro                                                                        | course | idnumber |
      | label      | L1   | <a href="../lib/tests/fixtures/readonlyform.php?sections=2">Fixture link</a> | C1     | label1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Fixture link"
    When I expand all fieldsets
    Then the field "Name" matches value "Important information"
    Then the field "Other" matches value "Other information"
