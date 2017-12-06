@block @block_dataformaccessentry @mod_dataform @dataformrule
Feature: Block dataform access entry

    @javascript
    Scenario: Manage access rule
        Given I run dataform scenario "access rule management" with:
            | ruletype | entry |
