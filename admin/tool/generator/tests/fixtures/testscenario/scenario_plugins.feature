Feature: Enable and disable plugins
  Scenario: Course with some disabled plugins
    Given the following config values are set as admin:
      | sendcoursewelcomemessage | 0 | enrol_manual |
    And I enable "page" "mod" plugin
    And I disable "book" "mod" plugin
    And the following "course" exists:
      | fullname         | Course test |
      | shortname        | C1          |
      | category         | 0           |
      | numsections      | 3           |
      | initsections     | 1           |
