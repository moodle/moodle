# local_launchpad

Repository-backed tools to create the **LaunchPad** Moodle category/course skeleton for **ToBa Learn MVP 1** (`ToBa_Learn_MVP.pdf`).

## What it creates (additive)

- **Hidden** category: `ToBa Tech LaunchPad Fellowship`
- **Five** hidden track sub-categories (AI & Automation, Full Stack, Product Management, Digital Marketing, Startup Building)
- **Six** hidden cohort courses:
  - Orientation under the fellowship **root** (not a sixth track category)
  - One course per track
- Four phase sections on each **track** course: Foundations → Guided project → Industry simulation → Demo day and certification
- Hidden placeholder activities (Page / Quiz / Assign only — **no video stubs**)
- Ensures a **manual** enrolment instance (create/enable if needed). Other enabled enrolment methods are **reported and left unchanged**
- On **My / My courses**, fellows with no `lp-*` enrolment see an honest empty-state notice (no invented contacts or self-enrol instructions)
- Read-only **account persona** classifier (`classes/account_persona.php`): Fellowship / Mentor / Admin / Employee from enrolments, admin checks, and cohort `lp-employee`. Does **not** authorize actions by itself.
- Staff **account provisioning** (`classes/account_provision.php` + `cli/provision_account.php`): create manual users, enrol fellows (student on `lp-*`), assign mentors (teacher/editingteacher on `lp-*` only), grant employees (cohort `lp-employee`). Refuses Admin grants. Does **not** change signup/auth settings by itself.
- Public **landing page** (Dipesh): `classes/page.php` + templates/assets; frontpage for guests via `public/index.php`.
- Local-only **email signup** helper: `cli/enable_local_signup.php` (fail-closed to local targets).

Does **not** delete legacy categories (e.g. an older Orientation category). Does **not** invent curriculum body text, mentors, grades, or certificates. Out of scope vs MVP §8: commerce, hiring, mentor marketplace. **Employee** access is cohort `lp-employee` + `/local/launchpad/employee.php` stub (no curriculum invented).

## Commands

From the Moodle root (directory that contains `config.php` / `public/`):

```bash
# Preview only (default)
php public/local/launchpad/cli/setup_structure.php --cohort=Pilot

# Write to the database bound to this config.php
php public/local/launchpad/cli/setup_structure.php --cohort=Pilot --apply

# Account provisioning (dry-run default; --apply local-only)
php public/local/launchpad/cli/provision_account.php --help
php public/local/launchpad/cli/provision_account.php --action=create-user \
  --username=fellow1 --email=fellow1@example.local --firstname=A --lastname=B
php public/local/launchpad/cli/provision_account.php --action=enrol-fellow \
  --username=fellow1 --course=lp-ai-pilot --apply
php public/local/launchpad/cli/provision_account.php --action=assign-mentor \
  --username=mentor1 --course=lp-ai-pilot --apply
```

After code deploy, visit Site administration so Moodle installs/upgrades the `local_launchpad` plugin (or run CLI upgrade). If new PHP classes are not autoloaded, run `php admin/cli/purge_caches.php` once on that site.

## Safety

- Default is **dry-run** (always allowed; no writes)
- `--apply` is **fail-closed**: requires loopback `wwwroot` (`localhost` / `127.0.0.1` / `::1`) **and** a positive local signal (`moodle_phpunit` / `*_local` dbname, or `MOODLE_DOCKER_PHPUNIT`, or `MOODLE_LAUNCHPAD_ALLOW_APPLY=1`). Denied hosts (e.g. `*.toba-tech*`, `learn.*`) never apply.
- Idempotent via `idnumber` keys (`launchpad-*`, `lp-*`)
- **New** categories/courses/activities are created **hidden**. **Existing** objects keep admin visibility / name / placement (no force-hide, rename, or move on re-apply — category hide would cascade in Moodle core)
- `--cohort` is validated (non-empty slug, Moodle shortname/idnumber length limits). Invalid values are rejected; there is **no** silent fallback to `pilot`
- Partial failure prints the **operation + course/activity** and exits non-zero. **Safe retry:** re-run the same command after fixing the cause — no DB reset required

## Spec

Authoritative: `personal-resources/ToBa_Learn_MVP.pdf` (MVP 1 structure). Local notes: `personal-resources/specs/`.

### Tests

| Suite | Needs DB? | Covers |
|-------|-----------|--------|
| `tests/blueprint_test.php` | No | Structure shape |
| `tests/cohort_test.php` | No | Cohort / slug / idnumber limits |
| `tests/enrol_plan_test.php` | No | Manual enrol ensure + report others |
| `tests/operation_failure_test.php` | No | Failure message shape |
| `tests/apply_target_test.php` | No | CLI `--apply` local guard |
| `tests/reconcile_policy_test.php` | No | Visibility/rename/move policy + cascade documentation |
| `tests/account_persona_test.php` | Yes (phpunit DB / `phpu_` prefix) | Persona classifier: fellow, mentor, admin, multi, request ignored, employee unresolved, read-only |
| `tests/account_provision_policy_test.php` | No | Grant policy: admin/employee rejected; lp-* + mentor roles |
| `tests/account_provision_test.php` | Yes (phpunit DB) | Staff create/enrol/mentor; forbidden actor; invalid course/role; auth snapshot unchanged |
| Full setup apply / enrol / access | Yes | Manual / local scripts under `personal-resources/` (not PHPUnit) |
