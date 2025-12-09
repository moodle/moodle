
**Key Tool**: We use `moodle-plugin-ci`, a specialized tool maintained by the Moodle community. It spins up a fresh Moodle environment (using Docker/Actions) to run a comprehensive suite of tests.

### Tested Plugins
- `local/coursematrix`
- `local/masterbuilder`
- `local/quiz_password_verify`

## Expected Results

When you push code or open a PR, navigate to the **Actions** tab in your GitHub repository. You should see a workflow run named **Moodle Plugin CI**.

Inside, you will see separate jobs for each plugin (e.g., `test-plugin (local/coursematrix, ...)`).
A **Green Checkmark** means all tests passed.
A **Red X** means something failed and requires attention.

## What a Green Build Proves
A successful run guarantees the following about your code:

| Component | Guarantee | Significance |
| :--- | :--- | :--- |
| **Linting (PHP/JS/CSS)** | Syntax is valid; No syntax errors. | Prevents "White Screen of Death" crashes due to typos. |
| **CodeChecker** | Code style matches Moodle standards (PSR-12). | Ensures long-term maintainability and readability by other Moodle devs. |
| **Grunt** | Frontend assets (JS/SCSS) are compiled and up-to-date. | Validates that current JS edits match the deployed `.min.js` files, preventing UI bugs. |
| **Smoke Test** | Core System Integrity. | Proves the **Database**, **Data Directory**, **Cache**, and **Configuration** are accessible and writable. |
| **SMTP Test** | Email Configuration Validity. | Proves the system can generate emails and that SMTP credentials (if set) are readable. |
| **Savepoints** | Upgrade paths are valid. | Ensures users can upgrade the plugin without data loss or DB errors. |

## Troubleshooting Failures

If a job fails, click on it to see the logs. Here is how to interpret common errors:

### 1. PHPLint / Syntax Errors
*   **Error**: `Parse error: syntax error, unexpected...`
*   **Cause**: You have a typo in your PHP code (missing semicolon, mismatched braces).
*   **Fix**: Check the line number reported in the log and fix the syntax.

### 2. CodeChecker / Standard Violations
*   **Error**: `[Style] ... Line 45: Variable "foo" must be all lower-case.`
*   **Cause**: The code does not follow Moodle's strict coding standards.
*   **Fix**: run `moodle-plugin-ci codechecker` locally if possible, or adjust the code to match Moodle style (e.g., camelCase vs snake_case).
*   *Note: Some legacy code warnings can be ignored if the job is configured with `continue-on-error: true`.*

### 3. PHPUnit Failures
*   **Error**: `Failed asserting that...`
*   **Cause**: A unit test logic failed. This means your code changes broke expected functionality.
*   **Fix**: Read the failure message. It will tell you exactly which test failed and why.

## Verifying Specific Tests in GitHub
Your new **Smoke Test** and **SMTP Test** do not appear as separate "Jobs" in the main actions list. They run **inside** the `local/masterbuilder` job.

**To verify they are running:**
1.  Click on the **test-plugin (local/masterbuilder...)** job in the Actions summary.
2.  Scroll down and expand the **Run PHPUnit** step.
3.  Look for output similar to:
    ```text
    Local_masterbuilder_smoke_test tests (local_masterbuilder_smoke_test)
    ...
    Local_masterbuilder_smtp_test tests (local_masterbuilder_smtp_test)
    ```
4.  If you see dots `.` or `OK`, the tests passed.

## Running Tests Locally (Advanced)
*Note: The `vendor/bin/phpunit` command requires a fully initialized Moodle development environment with Composer dependencies installed.*

If you see `command not found`, we recommend relying on the **GitHub Actions Pipeline** which sets this up automatically. To run locally, you must:
1.  Run `composer install` in your project root.
2.  Initialize the test database: `php admin/tool/phpunit/cli/init.php`.
3.  Then run: `vendor/bin/phpunit local_masterbuilder_smoke_test`.

### 4. Behat Failures
*   **Error**: `Scenario: User logs in... Failed step: And I press "Login"`
*   **Cause**: The automated browser test failed. This often happens if you change the UI (buttons, IDs, labels) without updating the test.
*   **Fix**: Verify if your UI changes were intentional. If so, update the `.feature` file to match the new UI.
