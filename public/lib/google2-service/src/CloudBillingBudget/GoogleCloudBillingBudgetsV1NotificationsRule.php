<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudBillingBudget;

class GoogleCloudBillingBudgetsV1NotificationsRule extends \Google\Collection
{
  protected $collection_key = 'monitoringNotificationChannels';
  /**
   * Optional. When set to true, disables default notifications sent when a
   * threshold is exceeded. Default notifications are sent to those with Billing
   * Account Administrator and Billing Account User IAM roles for the target
   * account.
   *
   * @var bool
   */
  public $disableDefaultIamRecipients;
  /**
   * Optional. When set to true, and when the budget has a single project
   * configured, notifications will be sent to project level recipients of that
   * project. This field will be ignored if the budget has multiple or no
   * project configured. Currently, project level recipients are the users with
   * `Owner` role on a cloud project.
   *
   * @var bool
   */
  public $enableProjectLevelRecipients;
  /**
   * Optional. Email targets to send notifications to when a threshold is
   * exceeded. This is in addition to the `DefaultIamRecipients` who receive
   * alert emails based on their billing account IAM role. The value is the full
   * REST resource name of a Cloud Monitoring email notification channel with
   * the form `projects/{project_id}/notificationChannels/{channel_id}`. A
   * maximum of 5 email notifications are allowed. To customize budget alert
   * email recipients with monitoring notification channels, you _must create
   * the monitoring notification channels before you link them to a budget_. For
   * guidance on setting up notification channels to use with budgets, see
   * [Customize budget alert email
   * recipients](https://cloud.google.com/billing/docs/how-to/budgets-
   * notification-recipients). For Cloud Billing budget alerts, you _must use
   * email notification channels_. The other types of notification channels are
   * _not_ supported, such as Slack, SMS, or PagerDuty. If you want to [send
   * budget notifications to Slack](https://cloud.google.com/billing/docs/how-
   * to/notify#send_notifications_to_slack), use a pubsubTopic and configure
   * [programmatic notifications](https://cloud.google.com/billing/docs/how-
   * to/budgets-programmatic-notifications).
   *
   * @var string[]
   */
  public $monitoringNotificationChannels;
  /**
   * Optional. The name of the Pub/Sub topic where budget-related messages are
   * published, in the form `projects/{project_id}/topics/{topic_id}`. Updates
   * are sent to the topic at regular intervals; the timing of the updates is
   * not dependent on the [threshold rules](#thresholdrule) you've set. Note
   * that if you want your [Pub/Sub JSON
   * object](https://cloud.google.com/billing/docs/how-to/budgets-programmatic-
   * notifications#notification_format) to contain data for
   * `alertThresholdExceeded`, you need at least one [alert threshold
   * rule](#thresholdrule). When you set threshold rules, you must also enable
   * at least one of the email notification options, either using the default
   * IAM recipients or Cloud Monitoring email notification channels. To use
   * Pub/Sub topics with budgets, you must do the following: 1. Create the
   * Pub/Sub topic before connecting it to your budget. For guidance, see
   * [Manage programmatic budget alert
   * notifications](https://cloud.google.com/billing/docs/how-to/budgets-
   * programmatic-notifications). 2. Grant the API caller the
   * `pubsub.topics.setIamPolicy` permission on the Pub/Sub topic. If not set,
   * the API call fails with PERMISSION_DENIED. For additional details on
   * Pub/Sub roles and permissions, see [Permissions required for this
   * task](https://cloud.google.com/billing/docs/how-to/budgets-programmatic-
   * notifications#permissions_required_for_this_task).
   *
   * @var string
   */
  public $pubsubTopic;
  /**
   * Optional. Required when NotificationsRule.pubsub_topic is set. The schema
   * version of the notification sent to NotificationsRule.pubsub_topic. Only
   * "1.0" is accepted. It represents the JSON schema as defined in
   * https://cloud.google.com/billing/docs/how-to/budgets-programmatic-
   * notifications#notification_format.
   *
   * @var string
   */
  public $schemaVersion;

  /**
   * Optional. When set to true, disables default notifications sent when a
   * threshold is exceeded. Default notifications are sent to those with Billing
   * Account Administrator and Billing Account User IAM roles for the target
   * account.
   *
   * @param bool $disableDefaultIamRecipients
   */
  public function setDisableDefaultIamRecipients($disableDefaultIamRecipients)
  {
    $this->disableDefaultIamRecipients = $disableDefaultIamRecipients;
  }
  /**
   * @return bool
   */
  public function getDisableDefaultIamRecipients()
  {
    return $this->disableDefaultIamRecipients;
  }
  /**
   * Optional. When set to true, and when the budget has a single project
   * configured, notifications will be sent to project level recipients of that
   * project. This field will be ignored if the budget has multiple or no
   * project configured. Currently, project level recipients are the users with
   * `Owner` role on a cloud project.
   *
   * @param bool $enableProjectLevelRecipients
   */
  public function setEnableProjectLevelRecipients($enableProjectLevelRecipients)
  {
    $this->enableProjectLevelRecipients = $enableProjectLevelRecipients;
  }
  /**
   * @return bool
   */
  public function getEnableProjectLevelRecipients()
  {
    return $this->enableProjectLevelRecipients;
  }
  /**
   * Optional. Email targets to send notifications to when a threshold is
   * exceeded. This is in addition to the `DefaultIamRecipients` who receive
   * alert emails based on their billing account IAM role. The value is the full
   * REST resource name of a Cloud Monitoring email notification channel with
   * the form `projects/{project_id}/notificationChannels/{channel_id}`. A
   * maximum of 5 email notifications are allowed. To customize budget alert
   * email recipients with monitoring notification channels, you _must create
   * the monitoring notification channels before you link them to a budget_. For
   * guidance on setting up notification channels to use with budgets, see
   * [Customize budget alert email
   * recipients](https://cloud.google.com/billing/docs/how-to/budgets-
   * notification-recipients). For Cloud Billing budget alerts, you _must use
   * email notification channels_. The other types of notification channels are
   * _not_ supported, such as Slack, SMS, or PagerDuty. If you want to [send
   * budget notifications to Slack](https://cloud.google.com/billing/docs/how-
   * to/notify#send_notifications_to_slack), use a pubsubTopic and configure
   * [programmatic notifications](https://cloud.google.com/billing/docs/how-
   * to/budgets-programmatic-notifications).
   *
   * @param string[] $monitoringNotificationChannels
   */
  public function setMonitoringNotificationChannels($monitoringNotificationChannels)
  {
    $this->monitoringNotificationChannels = $monitoringNotificationChannels;
  }
  /**
   * @return string[]
   */
  public function getMonitoringNotificationChannels()
  {
    return $this->monitoringNotificationChannels;
  }
  /**
   * Optional. The name of the Pub/Sub topic where budget-related messages are
   * published, in the form `projects/{project_id}/topics/{topic_id}`. Updates
   * are sent to the topic at regular intervals; the timing of the updates is
   * not dependent on the [threshold rules](#thresholdrule) you've set. Note
   * that if you want your [Pub/Sub JSON
   * object](https://cloud.google.com/billing/docs/how-to/budgets-programmatic-
   * notifications#notification_format) to contain data for
   * `alertThresholdExceeded`, you need at least one [alert threshold
   * rule](#thresholdrule). When you set threshold rules, you must also enable
   * at least one of the email notification options, either using the default
   * IAM recipients or Cloud Monitoring email notification channels. To use
   * Pub/Sub topics with budgets, you must do the following: 1. Create the
   * Pub/Sub topic before connecting it to your budget. For guidance, see
   * [Manage programmatic budget alert
   * notifications](https://cloud.google.com/billing/docs/how-to/budgets-
   * programmatic-notifications). 2. Grant the API caller the
   * `pubsub.topics.setIamPolicy` permission on the Pub/Sub topic. If not set,
   * the API call fails with PERMISSION_DENIED. For additional details on
   * Pub/Sub roles and permissions, see [Permissions required for this
   * task](https://cloud.google.com/billing/docs/how-to/budgets-programmatic-
   * notifications#permissions_required_for_this_task).
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
  /**
   * Optional. Required when NotificationsRule.pubsub_topic is set. The schema
   * version of the notification sent to NotificationsRule.pubsub_topic. Only
   * "1.0" is accepted. It represents the JSON schema as defined in
   * https://cloud.google.com/billing/docs/how-to/budgets-programmatic-
   * notifications#notification_format.
   *
   * @param string $schemaVersion
   */
  public function setSchemaVersion($schemaVersion)
  {
    $this->schemaVersion = $schemaVersion;
  }
  /**
   * @return string
   */
  public function getSchemaVersion()
  {
    return $this->schemaVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBillingBudgetsV1NotificationsRule::class, 'Google_Service_CloudBillingBudget_GoogleCloudBillingBudgetsV1NotificationsRule');
