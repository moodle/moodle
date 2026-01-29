LTI 1.3 Tool Library import instructions

This library is a patched for use in Moodle - it requires the following changes be applied on top of the packback upstream base:
1. Removal of phpseclib dependency (replaces a single call with openssl equivalent)
2. The fix included in MDL-87789, ensuring optional lineitem properties are omitted if not set.

To upgrade to a new version of this library:
1. Clone the latest version of the upstream library from github:
https://github.com/packbackbooks/lti-1-3-php-library/tags
2. Apply the changes mentioned above from the moodle-fixes branch of this repository:
https://github.com/snake/lti-1-3-php-library/tree/moodle-fixes
3. Apply the local fix to the library from MDL-87789, unless already fixed
upstream (https://github.com/packbackbooks/lti-1-3-php-library/issues/169)
Apply these commits on top of the upstream clone.
4. Replace lib/lti1p3/src/ with the library's /src directory
5. Copy LICENSE.md to lib/lti1p3/
6. Copy README.md to lib/lti1p3/
7. Copy composer.json to lib/lti1p3/
8. Update the library entry in lib/thirdpartylibs.xml
9. Update the dependency note in lib/php-jwt/readme_moodle.txt, recording the version of php-jwt lib/lti1p3 depends on, if needed.
10. Check the upstream library's release notes and UPGRADES.md for any backwards incompatible changes to names, etc.
Moodle's calling code may require updates if changes are breaking -  so check this and make any changes if needed.
11. Run all unit tests in enrol/lti and auth/lti.
12. Regression test Moodle-to-Moodle LTI using LTI Advantage (not legacy) using the relevant MDLQA tests as a guide.
