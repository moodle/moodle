# Releasing a new version

This guide is to release a new version of the Code-checker plugin. Remember that when considering the version number to use, that
 this project follows [Semantic Versioning](http://semver.org/), so bump the version number accordingly.

Check the existing PRs and approve those that are applicable to this release.

Create a standard PR to get the version bump reviewed and incorporated upstream. Please **avoid merge commit** for the new
 release merge (use "Rebase and merge" option). Ensure the following have been updated:

* The `CHANGES.md`, adding the PRs that have been approved and other existing changes.
* The `version.php` file, at least the *version* and *release* values.

Create a commit with message, and, optionally, anything worth commenting in the commit description:

```
Bump to YYYYMMDD (vX.Y.Z) - <<release name, if any>>
```

Once the version bump PR has been reviewed and incorporated upstream, then you need to tag the release, that will trigger a new CI build (right now [@ GHA](https://github.com/moodlehq/moodle-local_codechecker/actions)) to run the integration testing.

Tag `master` branch `HEAD` and push using commands:

```bash
$ git tag -a vX.Y.Z -m "Code checker vX.Y.Z - <<release name, if any>>"
$ git push origin vX.Y.Z
```

Then just go to [Github releases](https://github.com/moodlehq/moodle-local_codechecker/releases) and "Draft a new release" for the just created tag. Then "Publish release" without filling anything and that's all!

# Moodle plugins directory
Once the new release is ready, you should add the latest release to the `Moodle plugins directory`.

1. Login to the plugins' directory site and go to [plugin page](https://moodle.org/plugins/local_codechecker).
2. To add a new version, you have to be a plugin maintainer. **Only lead maintainers can add other maintainers**.
3. In the main action bar, follow the *</>Developer zone* option.
4. Click `Add a new version`.
5. Select the appropriate GitHub release from the drop-down and press the `Release` button, in the next page (showing the zip and the VCS tag) just press `Continue`.
6. In the next page (it can take a while to be presented), fill in the required information:
  1. In the `Version release name`, copy the hyphen and the release name from the `Release notes` header.
  2. In the `Release notes` field we put the contents of the `README.md` file, with the contents of the `CHANGES.md` current release inserted after the GHA badge.
  3. In the `Supported software` fill both the supported Moodle and PHP versions.
  4. In the `Version control information` set the `VCS Branch` to `master`.
  5.  Click `Add a new version` (it can take a while too).

That's pretty much it. The new version is publicly available.
