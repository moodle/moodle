# Conventional commits policy for the WirisQuizzes squad

In the WirisQuizzes public repositories, and in accordance with the general Wiris engineering policies, we use **conventional commits**. This is a specification for commit messages that dovetails with SemVer and makes the usage of automated tools on top of git. Visit this [webpage](https://www.conventionalcommits.org/) in order to access the full specification.

The commit types we use are the following ones:

* `fix:` for bug fixing.
* `feat:` for new features.
* `refactor:` for improvements of the code that do not change any functionality.
* `docs:` for documentation.
* `test:` for changes in automated tests.
* `ops:` for changes in infrastructure, ci system, etc.
* `chore:` for any other change.

The description must begin with a lower case letter and be in imperative tense.

*(Only for Wiris employees)* Kanbanize-style footers are allowed (see the [documentation](https://knowledgebase.kanbanize.com/hc/en-us/articles/360022042331-GitHub-Integration-Scenarios-with-Kanbanize)), and the use of `#taskid card_id_number` is recommended in order to preserve traceability between cards in Kanbanize and commits. They must follow the [git trailer](https://git-scm.com/docs/git-interpret-trailers) format using the token `Kanbanize`.

Example:

```text/plain
fix: avoid NullPointerException when setting the id field.

Kanbanize: #taskid 12345
```
