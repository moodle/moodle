LTI 1.3 Tool Library import instructions

This library is a patched for use in Moodle - it requires the following changes be applied on top of its upstream base:
1. Removal of phpseclib dependency (replaces a single call with openssl equivalent)
2. Removal of Guzzle dependency (replaced with generic http client interfaces which are more compatible with Moodle's curl.)
3. Small fix to http_build_query() usages, to make sure the arg separator is explicitly set to '&', so as not to trip up
on Moodle's definition of PHP's arg_separator.output which is set to '&amp;' in lib/setup.php.
4. Update the firebase/php-jwt requirement from ^5.2 to ^6.0, to match the version shipped with Moodle. This can be removed
when the upstream PR is merged: https://github.com/packbackbooks/lti-1-3-php-library/pull/46
5. Fix launches for platforms choosing to omit 'alg' from their JWKS JSON. This fix is required because of the upgrade to
firebase/php-jwt 6.0, which now requires alg for its key parsing. This can be removed when a similar fix is included upstream,
perhaps when the upstream PR is merged: https://github.com/packbackbooks/lti-1-3-php-library/pull/46

To upgrade to a new version of this library:
1. Clone the latest version of the upstream library from github:
https://github.com/packbackbooks/lti-1-3-php-library/tags
2. Apply the changes mentioned above from the moodle-fixes branch of this repository:
https://github.com/snake/lti-1-3-php-library/tree/moodle-fixes
Apply these commits on top of the upstream clone.
3. Copy the entire src/ directory to lib/lti1p3/
4. Copy LICENSE.md to lib/lti1p3/
5. Copy README.me to lib/lti1p3/
6. Update the dependency note in lib/php-jwt/readme_moodle.txt, listing the correct version of this library there.
7. Check the upstream library's release notes and UPGRADES.md for any backwards incompatible changes to names, etc.
Moodle's calling code may require updates if changes are breaking -  so check this and make any changes if needed.
8. Run all unit tests in enrol/lti and auth/lti.
9. Regression test Moodle-to-Moodle LTI using LTI Advantage (not legacy) using the relevant MDLQA tests as a guide.
