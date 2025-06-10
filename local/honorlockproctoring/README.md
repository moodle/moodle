# Honorlock Proctoring

Welcome to the Moodle plugin for integrating with the Honorlock Proctoring! This plugin provides seamless integration between Moodle and Honorlock Proctoring, enabling online assessments to be proctored and monitored in real time. With this plugin, Moodle administrators can ensure the authenticity and validity of online exams, enhancing the reliability and trustworthiness of online education. 

The Honorlock Proctoring Moodle plugin is easy to install and use, and requires minimal configuration. It provides a comprehensive solution for proctoring online exams, and is designed to be flexible and adaptable to the specific needs of each institution.

A commercial license with Honorlock is required for integration. Please reach out to Honorlock for more information [here](https://www.honorlock.com).

In this readme file, we will go through the key features of the Honorlock Proctoring Moodle plugin, and provide detailed instructions on how to install, configure and use it. This readme file will provide all the information you need to get started with the Honorlock Proctoring Moodle plugin.

- [Requirements](#requirements)
- [Configuration](#configuration)
    - [LTI Configuration](#lti-configuration)
        - [Create the LTI External Tool](#create-the-lti-external-tool)
        - [Add LTI to a Course](#add-lti-to-a-course)
    - [Plugin Configuration](#plugin-configuration)
        - [Install the Plugin](#install-the-plugin)
        - [External Service Configuration and User Access](#external-service-configuration-and-user-access)
- [Issue Tracker](#issue-tracker-for-honorlocks-moodle-plugin)
    - [How to Submit an Issue](#how-to-submit-an-issue)
    - [What to Expect](#what-to-expect)


# Requirements

1. Honorlock Proctoring was tested with Moodle 3 and Moodle 4.
2. Honorlock organization client id and client secret are required for setup
3. Honorlock Proctoring authenticates for instructors via LTI. Please, check the LTI configuration below.

# Configuration
There are two parts to the Honorlock Proctoring integration process
- LTI configuration
- Plugin Installation and Configuration

## LTI Configuration
The LTI (Learning Tools Interoperability) will serve as the interface for instructors to access and configure the Honorlock proctoring system for exams. To accomplish this, the following steps are required: 
- Setting up an LTI external tool
- Adding the tool to each course where proctoring is desired

### Create the LTI External Tool
- Login as an admin in Moodle
- Go to the **Site Administration > Plugins > Activity modules > External Tool > Manage Tools** section
- Click the **configure a tool manually** link and enter the following configuration (Ignoring the ones not present in this list)
    - Tool name: Honorlock LTI
    - Tool URL: https://app.honorlock.com/org/[organization_uuid]/launch
    - Tool Description: Honorlock LTI Tool 1.3
    - LTI version: LTI 1.3
    - Public key type: Keyset URL
    - Initial login URL: https://app.honorlock.com/org/[organization_uuid]/oidc/login
    - Redirection URI(s): https://app.honorlock.com/org/[organization_uuid]/launch
    - Tool configuration usage: Show as preconfigured tool when adding an external tool
    - Default launch container: Embed, without blocks
    - In Moodle 4.3, you will also need to expand the Privacy Settings and Change the dropdown options to Always Share launchers name with tool and Always Share launchers email with tool
    - ***[optional]*** Icon URL (*You might need to click "**Show more...**"*): https://app.honorlock.com/favicons/favicon.ico
- Scroll down and click the **Save changes** button.
- On the generated tool box click the configuration details button (<img src="https://raw.githubusercontent.com/FortAwesome/Font-Awesome/6.x/svgs/solid/list.svg" width="12" height="12">) and take note of the following values as they will need to be provided to Honorlock to complete setup. 
    - Platform ID
    - Client ID
    - Deployment ID
    - Public keyset URL
    - Access token URL
    - Authentication request URL

### Add LTI to a course
- Select a course in Moodle
- Toggle the **Edit mode** option on the top right corner of the page
- Under any topic
    - Click **Add an activity or resource**
    - Click **External tool**
    - Fill the following fields
        - Activity name: Honorlock
        - Preconfigured tool: Honorlock LTI
        - Click **Save and return to course**
        - Untoggle **Edit mode**
    - Click the Honorlock LTI external Tool
- Note for Moodle 4.3, you will need toggle the Show in activity chooser for the LTI in the Course Menu (Select the LTI External Tools option from the “More” dropdown)

## Plugin Configuration
The Honorlock Plugin will enable students to take Honorlock proctored exams. To accomplish this, the following steps are required:
- Plugin Installation
- External Service Configuration and User Access
    
### Install the plugin
- If this is the first time installing the plugin, you will be prompted for some setting values.
    - Honorlock URL: The complete base URL for Honorlock (https://app.honorlock.com)
    - Honorlock Client ID: The Organization Client ID generated for your organization.
    - Honorlock Client Secret: The Organization Client Secret generated for your organization.

### External Service Configuration and User Access
- Setting up the external service is most likely the most involved part of the whole integration. For this portion we need the following:
    - Create a special user in moodle for the API.
    - Enable Web services in Moodle.
    - Create the new web service in Moodle.
    - Authorize the created user on the web service.
    - Define a new role in Moodle.
    - Assign the new role to the user.
    - Create a token for the user.
    - Contact Honorlock to complete the setup.

- For all the steps that follow you should log in to Moodle as an admin.

#### Create a special user in Moodle for the API
- Go to Site administration > Users > Accounts > Add a new user.
- Fill in the required fields (the following values are suggested).
    - Username: honorlock_api
    - Choose an authentication method: Web services authentication
    - First name: Honorlock
    - Surname: API
    - Email: honorlockapi@example.com
- Click Create user.

#### Enable Web services in Moodle
- Go to Site administration > General Advanced features (In Moodle 3 it’s just “Advanced features”).
- Find the Enable web services option and make sure it is enabled (Notice the default is disabled).
- Click the Save Changes Button.

#### Enable Web Service REST Protocol
- Go to Site administration > Server > Web Services > Manage Protocols (In Moodle 3.9 and Above this can be found in Site Adminstration > Plugins > Web Services > Manage Protocols)
- Click the icon to Enable the REST protocol.
- Click the Save Changes Button.

#### Create the new web service in Moodle
- Go to Site administration > Server > Web services > External services (In Moodle 3.9 and Above this can be found in Site Adminstration > Plugins > Web Services > External services)
- Click Add.
- Fill in the required fields.
    - Name: Moodle API
    - Short name: moodle_api
    - Enabled: check true
    - Authorized users only: check true
- Click on Add service.
- Go to the new service you created and click Functions.
- Add the following functions.
    - core_course_get_courses
    - local_honorlockproctoring_update_quiz_values
    - local_honorlockproctoring_get_quiz_questions
    - mod_quiz_get_quizzes_by_courses
- Click Add functions.

#### Authorize the created user on the newly created web service
- Go to Site administration > Server > Web services > External services. (In Moodle 3.9 and Above this can be found in Site Adminstration > Plugins > Web Services > External services)
- Look for the Moodle API you just created and click the Authorised users link.
- Add the Honorlock API user to the list of authorized users.
- Click the user from the Not authorized users list.
- Click the ← Add button.
- Make sure the user appears in the Authorized users list.


#### Define a new role in moodle
- Go to Site administration > Users > Permissions > define roles.
- Click Add a new role.
- For “Use role or archetype” select “No role”.
- Click Continue.
- Fill in the fields.
    - Short name: honorlock_api_access
    - Custom full name: Honorlock API Access
    - Custom description: Access for API user to the external service
    - Role archetype: None
    - Context types where this role may be assigned: 
        - Check system and leave others unchecked
    - In the capability section at the bottom allow the following permissions (tip: do a find for each).
        - moodle/course:update
        - moodle/course:view
        - moodle/course:viewhiddencourses
        - moodle/question:viewall
        - webservice/rest:use
        - mod/quiz:view
        - mod/quiz:viewreports
- Scroll down and click Create This Role and then you’ll be presented with the definition of the role after

#### Assign the new role to the user
- Go to Site administration > Users > Permissions > Assign system roles.
- Select the Honorlock API Access role.
- Add the Honorlock API user to the Existing users list.
- Click the user from the Potential users list.
- Click the ← Add button.
- Make sure the user appears in the Existing users list.

#### Create a token for the user
- Go to Site administration > Server > Web services > Manage tokens.
- Click Create token.
- Search the Honorlock API  user in the user field and click on it to add it. 
- Make sure the user is added to the field.
- Select the Moodle API service in the Service field.
- Make sure the Valid until field does not expire bay keeping the enable check box unchecked.
- Click the Save changes button.
- Make note of the token to provide to Honorlock.

#### Contact Honorlock to complete setup
- Provide Honorlock with the following information from the previous steps: 
    - Platform ID
    - Client ID
    - Deployment ID
    - Public keyset URL
    - Access token URL
    - Authentication request URL
    - Token

# Issue Tracker for Honorlock's Moodle Plugin

This repository serves as the dedicated issue tracker for Honorlock's Moodle Plugin. We have created this separate repository to keep things organized and efficient as we work towards improving and maintaining our Moodle plugin.

**Please note**, at this time, we are only accepting reports of bugs or glitches. We are not taking feature requests through this issue tracker.

## How to Submit an Issue

If you have found a bug or glitch related to our Moodle plugin, please follow these steps to create a new issue:

1. **Check existing issues** - Take a look at the existing issues to see if someone else has already reported the same problem.

2. **Create a new issue** - If your issue is unique, click on the "Issues" tab near the top of the page, and then click the "New issue" button.

3. **Describe the issue** - Provide as much detail as possible in the issue form:

    - Choose a clear and concise title that summarises the problem.
    - Describe the issue in detail, providing the necessary steps to reproduce the problem if it's a bug.
    - If possible, include screenshots or screen recordings to help illustrate the issue.
    - Include details about your system configuration such as operating system, browser version, and Moodle version.
    
4. **Submit the issue** - After you have filled out the form, click "Submit new issue" to create the issue. We will review it and respond as soon as possible.

## What to Expect

Once an issue is submitted, our team will review it and potentially ask for further information to better understand the problem. If we're able to reproduce a reported bug, we'll classify the issue accordingly and add it to our development roadmap. 

Please understand that we prioritize issues based on a variety of factors including but not limited to the impact of the issue, the number of users it affects, and our development resources. As such, we can't guarantee a specific timeline for when an issue will be resolved. We appreciate your patience and understanding.

Thank you for your contribution and support.