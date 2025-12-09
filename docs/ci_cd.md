# CI/CD Pipeline Documentation

This project uses **GitHub Actions** to automatically test custom Moodle plugins whenever code is pushed to the `main` or `master` branches, or when a Pull Request is opened.

## How It Works

The pipeline is defined in `.github/workflows/ci.yml`. It uses a **Matrix Strategy** to run tests in parallel for each custom plugin. This ensures that a failure in one plugin does not block the testing of others.

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
*   **Fix**: Read the failure message. It will tell you exactly which test failed and why (e.g., "Expected 'A' but got 'B'").

### 4. Behat Failures
*   **Error**: `Scenario: User logs in... Failed step: And I press "Login"`
*   **Cause**: The automated browser test failed. This often happens if you change the UI (buttons, IDs, labels) without updating the test.
*   **Fix**: Verify if your UI changes were intentional. If so, update the `.feature` file to match the new UI.
