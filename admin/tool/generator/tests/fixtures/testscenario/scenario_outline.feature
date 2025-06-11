Feature: Fixture to prepare scenario for testing from an outline

  Scenario Outline: creating test scenarios using an outline
    Given the following "course" exists:
      | fullname    | <name>      |
      | shortname   | <shortname> |
      | category    | 0           |
      | numsections | 3           |

    Examples:
      | name     | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
      | Course 3 | C3        |

  @cleanup
  Scenario: clean up fixture to prepare scenario for testing from an outline
    Given the course "Course 1" is deleted
    And the course "Course 2" is deleted
    And the course "Course 3" is deleted
