[![ci](https://github.com/catalyst/moodle-tool_crawler/actions/workflows/ci.yml/badge.svg?branch=MOODLE_310_STABLE)](https://github.com/catalyst/moodle-tool_crawler/actions/workflows/ci.yml?branch=MOODLE_310_STABLE)

# moodle-tool_crawler

* [What is this?](#what-is-this)
* [How does it work?](#how-does-it-work)
* [Branches](#branches)
* [Installation](#installation)
* [Configuration](#configuration)
* [Testing](#testing)
* [Debugging](#debugging)
* [Reports](#reports)
* [Support](#support)
* [Warm thanks](#warm-thanks)

# What is this?

This is a link checking robot, that crawls your Moodle site following links
and reporting on links that are either broken or that link to very large
files.

https://moodle.org/plugins/tool_crawler

# How does it work?

It is an admin tool plugin with a Moodle cron task. It logs into your Moodle
via curl effectively from outside Moodle. The cronjob scrapes each page,
parses it and follows links. By using this architecture it will only find
broken links that actually matter to students.

Since the plugin cronjob comes in from outside it needs to authenticate in Moodle.

# Branches

| Moodle version    | Branch                |
| ----------------- | --------------------- |
| Moodle 3.10+      | MOODLE_310_STABLE     |
| Moodle 3.4 to 3.9 | master                |
| Totara 12+        | master                |

# Installation

The plugin has a dependency on the [moodle-auth_basic](https://moodle.org/plugins/auth_basic).
To install the dependency plugin as a git submodule:
```
git submodule add git@github.com:catalyst/moodle-auth_basic.git auth/basic
```


Install plugin moodle-tool_crawler as a git submodule:
```
git submodule add git@github.com:catalyst/moodle-tool_crawler.git admin/tool/crawler
```
# Configuration

When installing the plugins please keep in mind the official Moodle recommendations: [installing Moodle plugins](https://docs.moodle.org/32/en/Installing_add-ons)

## Step 1

Login to Moodle after you have downloaded the plugin code with git. You will be
forwarded to URL http://your_moodle_website.com/admin/index.php with Plugins check.
There you should see plugins "Basic authentication" and "Link checker robot".

Click button "Upgrade Moodle database now" which should initiate plugins installation.

Now you should see page "Upgrading to new version" with plugins installation
statuses and button "Continue".

**Note! Plugin auth_basic is disabled by default after installation.
You will need to enable it manually from 


Home ► Site administration ► Plugins ► Authentication ► Manage authentication**

After clicking "Continue" you will get to the page "New settings - Link checker robot".
While you may leave other settings default, you might want to setup a custom bot username
and make sure to change bot password.

**It is recommended that bot user should be kept with readonly access to all
the site pages you wish to crawl. You can give the robot similar read
capabilities that real students have. Never give your bot user write capabilities.**

It can also be a good idea to give your robot some extra permissions, like visibility of hidden courses
or activites so it can crawl content which is being developed and will be later delivered to students.
If you are worried about load and total crawl time then you can filter out whole courses, eg last years
archives courses, see below for more details.

After verifying all settings click "Save changes".

## Step 2

Enable auth_basic plugin (if you haven't done that earlier) from

Home ► Site administration ► Plugins ► Authentication ► Manage authentication

Now navigate to URL http://your_moodle_website.com/admin/tool/crawler/index.php".
It will show some stats about the Link checker Robot.

Click "Auto create" button against "Bot user". This actually creates the user
with the username and password you have configured previously on page
"New settings - Link checker robot".

Once bot user is created "Bot user" line in status report should be showing "Good".

## Disabling crawling of specific course categories

This is achieved by configuring proper security roles in Moodle and assigning
these roles to the robot user on desired categories.

Import role "Robot" from admin/tool/crawler/roles/robot.xml on

Site administration ► Users ► Permissions ► Define roles ► Add a new role

Add this role to the "Link checker robot" user on


Site administration ► Users ► Permissions ► Assign system roles.

Import role "Robot nofollow" from file 
admin/tool/crawler/roles/robotnofollow.xml on 


Site administration ► Users ► Permissions ► Define roles ► Add a new role.

To disable crawling of, say "Category ABC", go to


Site administration ► Courses ► Manage courses and categories ► Category ABC

then click on "Assign roles" in the left navigation menu.
Click on role "Robot nofollow", click on user "Link checker Robot"
under "Potential users" and add him to "Existing users".

The above configuration applies role "Robot" on the whole Moodle site
and lets crawler to access general content. And "Role nofollow" prohibits
crawler from accessing the specific category.

In the same way it is possible to restrict crawler from accessing other
Moodle contexts such as courses, activities and blocks.

The same effect could be achieved even without role "Robot nofollow" by
assigning role "Robot" on the contexts you want to be crawled. But
using the combination of two roles gives more flexibility.

# Testing

## Test basic authentication with curl

Example in bash:

```
curl -c /tmp/cookies -v -L --user moodlebot:moodlebot http://your_moodle_website.com/course/view.php?id=3
```

This command should log you in with specified credentials via Basic HTTP Auth.
It will dump headers, requests and responses and among the output you should
be able to see the line "You are logged in as ".

Once Basic HTTP auth works test running the robot task from the CLI:

```
php admin/cli/scheduled_task.php --execute='\tool_crawler\task\crawl_task'
Execute scheduled task: Parallel crawling task (tool_crawler\task\crawl_task)
... used 22 dbqueries
... used 0.039698123931885 seconds
Scheduled task complete: Parallel crawling task (tool_crawler\task\crawl_task)
```

This will create a batch of new adhoc crawl tasks in the mdl_task_adhoc table that
will run in parallel, depending on the crawl_task setting. 

You can manually run the adhoc tasks from the CLI with:
```
php admin/cli/adhoc_task.php --execute
Execute adhoc task: tool_crawler\task\adhoc_crawl_task
... used 5733 dbqueries
... used 58.239180088043 seconds
Adhoc task complete: tool_crawler\task\adhoc_crawl_task
```

If this worked then it's a matter of sitting back and waiting for the
robot to do it's thing. It works incrementally spreading the load over many
cron cycles, you can watch it's progress in

/admin/tool/crawler/report.php?report=queued

and

/admin/tool/crawler/report.php?report=recent

# Debugging

You can also run link crawler on given page by passing url. You might need to Reset Progress if its still running from Administration > Reports > Link crawler -> Robot status

```
php admin/tool/crawler/cli/crawl-as.php --url=http://localhost/
```

# Reports

4 new admin reports are available for showing the current crawl status, broken
links and URLs and slow links. They are available under:

Administration > Reports > Link checker

# Support

Please raise any issues in GitHub:

https://github.com/catalyst/moodle-tool_crawler/issues

If you need anything urgently and would like to sponsor it's implementation please
email me: [Brendan Heywood](mailto:brendan@catalyst-au.net).



Warm thanks
-----------

Thanks to Central Queensland University for sponsoring the initial creation of this plugin:

https://www.cqu.edu.au/

This plugin was developed by Catalyst IT Australia:

https://www.catalyst-au.net/

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400">
