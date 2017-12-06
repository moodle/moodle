@block @block_dataformaccessview @mod_dataform @dataformrule
Feature: Block dataform access view

    @javascript
    Scenario: Manage access rule
        Given I run dataform scenario "access rule management" with:
            | ruletype | view |
