# Contributing to MathType for TinyMCE

We would love for you to contribute to this project and help make it better.

This file explains the guidelines we would like you to follow as a contributor.

## Requirements

To contribute to MathType Moodle plugin for TinyMCE, set-up the Development Environment as is described in the [Environment](../environment/README.md) documentation.

- Fork the repository and create a new branch from the `release` one.
- Follow the [Conventional Commits Format guideline](#conventional-commits).
- Lint the code.
- Validate all tests pass.

## Conventional Commits

We encourage you to follow the rules over how our Git commit messages must be formatted.
This format leads to **easier to read commit history**.

- Wrap message lines to a few characters, not long sentences.

- Each commit message can consist of a **header**, a **body**, and a **footer**.

    ```
    <header>
    <BLANK LINE>
    <body>
    <BLANK LINE>
    <footer>
    ```

- The `header` is mandatory and follows the [Commit Message Header](#commit-message-header) format.

- The `body` is optional but suggested for those that need long explanations.
  When the body is present it has to follow the [Commit Message Body](#commit-message-body) format.

- The `footer` is optional. The [Commit Message Footer](#commit-message-footer) format describes what the footer is used for, and the structure it must have.

### Commit Message Header

The commit message `header` should be structured as follows:

```
<type>(<scope>): <summary>
  │       │             │
  │       │             └─> Summary in present tense. Not capitalized. No period at the end.
  │       │
  │       └─> Commit Scope (optional): plugin|packaging|changelog|dev-infra|none
  │
  └─> [Commit Type](#type): build|ci|docs|feat|fix|perf|refactor|test
```

#### Type

This [article](https://medium.com/@noriller/docs-conventional-commits-feat-fix-refactor-which-is-which-531614fcb65a) specifies the recommended commit types that this project uses.

## Commit Message Guidelines


### Commit Message Body

Explain the motivation for the change in the commit message `body`.

Use the imperative, present tense: "fix" not "fixed" nor "fixes".

This commit message should explain _why_ you are making the change.

### Commit Message Footer

The `footer` can contain information about breaking changes and deprecations, and it is also the place to reference GitHub issues, Kanbanize cards, Jira tickets, and other PRs that this commit closes or is related to.

For example:

```
BREAKING CHANGE: <breaking change summary>

<breaking change description + migration instructions>

Fixes #{issue_number}
#taskid {card_number}
```

### Revert commits

A revert commit should begin with `revert:`, followed by the header of the reverted commit.

The content of the commit message body should contain:

- Information about the SHA of the commit being reverted in the following format: `This reverts commit <SHA>`,
- A description of the reason for reverting the commit.

## Git flow

The development team in charge of maintaining this project will follow the internal guides to create branches, tags, handling issues and tasks.

This project is strict with testing to ensure a good quality product. Each time a pull request opens or a commit is made to an opened pull request, a GitHub action running all the test for the repository will execute. If that fails, the pull request can not be merged.

## Software Dependencies Update Plan

This project contains [dependencies](../environment/README.md#dependencies-of-mathType-moodle-plugin-for-tinyMCE) from other company projects. Those are updated by the development team in charge of maintaining the MathType for TinyMCE on their own repositories.

The development team is also in charge of updating the versions used for the other projects. As soon as one of the dependencies releases a new version with changes, the development team will update it in this project.

> **Note:** More details on the `thirdpartylibs.xml` file.

