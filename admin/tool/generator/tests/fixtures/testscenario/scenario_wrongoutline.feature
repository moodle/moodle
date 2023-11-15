Feature: Prepare scenario for testing

  Scenario Outline: test outline scenarios are not supported yet
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
