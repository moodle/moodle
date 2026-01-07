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

namespace Google\Service\CloudHealthcare;

class Hl7V2NotificationConfig extends \Google\Model
{
  /**
   * Optional. Restricts notifications sent for messages matching a filter. If
   * this is empty, all messages are matched. The following syntax is available:
   * * A string field value can be written as text inside quotation marks, for
   * example `"query text"`. The only valid relational operation for text fields
   * is equality (`=`), where text is searched within the field, rather than
   * having the field be equal to the text. For example, `"Comment = great"`
   * returns messages with `great` in the comment field. * A number field value
   * can be written as an integer, a decimal, or an exponential. The valid
   * relational operators for number fields are the equality operator (`=`),
   * along with the less than/greater than operators (`<`, `<=`, `>`, `>=`).
   * Note that there is no inequality (`!=`) operator. You can prepend the `NOT`
   * operator to an expression to negate it. * A date field value must be
   * written in `yyyy-mm-dd` form. Fields with date and time use the RFC3339
   * time format. Leading zeros are required for one-digit months and days. The
   * valid relational operators for date fields are the equality operator (`=`)
   * , along with the less than/greater than operators (`<`, `<=`, `>`, `>=`).
   * Note that there is no inequality (`!=`) operator. You can prepend the `NOT`
   * operator to an expression to negate it. * Multiple field query expressions
   * can be combined in one query by adding `AND` or `OR` operators between the
   * expressions. If a boolean operator appears within a quoted string, it is
   * not treated as special, it's just another part of the character string to
   * be matched. You can prepend the `NOT` operator to an expression to negate
   * it. The following fields and functions are available for filtering: *
   * `message_type`, from the MSH-9.1 field. For example, `NOT message_type =
   * "ADT"`. * `send_date` or `sendDate`, the YYYY-MM-DD date the message was
   * sent in the dataset's time_zone, from the MSH-7 segment. For example,
   * `send_date < "2017-01-02"`. * `send_time`, the timestamp when the message
   * was sent, using the RFC3339 time format for comparisons, from the MSH-7
   * segment. For example, `send_time < "2017-01-02T00:00:00-05:00"`. *
   * `create_time`, the timestamp when the message was created in the HL7v2
   * store. Use the RFC3339 time format for comparisons. For example,
   * `create_time < "2017-01-02T00:00:00-05:00"`. * `send_facility`, the care
   * center that the message came from, from the MSH-4 segment. For example,
   * `send_facility = "ABC"`. * `PatientId(value, type)`, which matches if the
   * message lists a patient having an ID of the given value and type in the
   * PID-2, PID-3, or PID-4 segments. For example, `PatientId("123456", "MRN")`.
   * * `labels.x`, a string value of the label with key `x` as set using the
   * Message.labels map. For example, `labels."priority"="high"`. The operator
   * `:*` can be used to assert the existence of a label. For example,
   * `labels."priority":*`.
   *
   * @var string
   */
  public $filter;
  /**
   * The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client. The
   * notification is a `PubsubMessage` with the following fields: *
   * `PubsubMessage.Data` contains the resource name. *
   * `PubsubMessage.MessageId` is the ID of this notification. It's guaranteed
   * to be unique within the topic. * `PubsubMessage.PublishTime` is the time
   * when the message was published. Note that notifications are only sent if
   * the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. The Cloud Healthcare API service account, service-
   * PROJECT_NUMBER@gcp-sa-healthcare.iam.gserviceaccount.com, must have
   * publisher permissions on the given Pub/Sub topic. Not having adequate
   * permissions causes the calls that send notifications to fail. If a
   * notification cannot be published to Pub/Sub, errors are logged to Cloud
   * Logging. For more information, see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * Optional. Restricts notifications sent for messages matching a filter. If
   * this is empty, all messages are matched. The following syntax is available:
   * * A string field value can be written as text inside quotation marks, for
   * example `"query text"`. The only valid relational operation for text fields
   * is equality (`=`), where text is searched within the field, rather than
   * having the field be equal to the text. For example, `"Comment = great"`
   * returns messages with `great` in the comment field. * A number field value
   * can be written as an integer, a decimal, or an exponential. The valid
   * relational operators for number fields are the equality operator (`=`),
   * along with the less than/greater than operators (`<`, `<=`, `>`, `>=`).
   * Note that there is no inequality (`!=`) operator. You can prepend the `NOT`
   * operator to an expression to negate it. * A date field value must be
   * written in `yyyy-mm-dd` form. Fields with date and time use the RFC3339
   * time format. Leading zeros are required for one-digit months and days. The
   * valid relational operators for date fields are the equality operator (`=`)
   * , along with the less than/greater than operators (`<`, `<=`, `>`, `>=`).
   * Note that there is no inequality (`!=`) operator. You can prepend the `NOT`
   * operator to an expression to negate it. * Multiple field query expressions
   * can be combined in one query by adding `AND` or `OR` operators between the
   * expressions. If a boolean operator appears within a quoted string, it is
   * not treated as special, it's just another part of the character string to
   * be matched. You can prepend the `NOT` operator to an expression to negate
   * it. The following fields and functions are available for filtering: *
   * `message_type`, from the MSH-9.1 field. For example, `NOT message_type =
   * "ADT"`. * `send_date` or `sendDate`, the YYYY-MM-DD date the message was
   * sent in the dataset's time_zone, from the MSH-7 segment. For example,
   * `send_date < "2017-01-02"`. * `send_time`, the timestamp when the message
   * was sent, using the RFC3339 time format for comparisons, from the MSH-7
   * segment. For example, `send_time < "2017-01-02T00:00:00-05:00"`. *
   * `create_time`, the timestamp when the message was created in the HL7v2
   * store. Use the RFC3339 time format for comparisons. For example,
   * `create_time < "2017-01-02T00:00:00-05:00"`. * `send_facility`, the care
   * center that the message came from, from the MSH-4 segment. For example,
   * `send_facility = "ABC"`. * `PatientId(value, type)`, which matches if the
   * message lists a patient having an ID of the given value and type in the
   * PID-2, PID-3, or PID-4 segments. For example, `PatientId("123456", "MRN")`.
   * * `labels.x`, a string value of the label with key `x` as set using the
   * Message.labels map. For example, `labels."priority"="high"`. The operator
   * `:*` can be used to assert the existence of a label. For example,
   * `labels."priority":*`.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The [Pub/Sub](https://cloud.google.com/pubsub/docs/) topic that
   * notifications of changes are published on. Supplied by the client. The
   * notification is a `PubsubMessage` with the following fields: *
   * `PubsubMessage.Data` contains the resource name. *
   * `PubsubMessage.MessageId` is the ID of this notification. It's guaranteed
   * to be unique within the topic. * `PubsubMessage.PublishTime` is the time
   * when the message was published. Note that notifications are only sent if
   * the topic is non-empty. [Topic
   * names](https://cloud.google.com/pubsub/docs/overview#names) must be scoped
   * to a project. The Cloud Healthcare API service account, service-
   * PROJECT_NUMBER@gcp-sa-healthcare.iam.gserviceaccount.com, must have
   * publisher permissions on the given Pub/Sub topic. Not having adequate
   * permissions causes the calls that send notifications to fail. If a
   * notification cannot be published to Pub/Sub, errors are logged to Cloud
   * Logging. For more information, see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Hl7V2NotificationConfig::class, 'Google_Service_CloudHealthcare_Hl7V2NotificationConfig');
