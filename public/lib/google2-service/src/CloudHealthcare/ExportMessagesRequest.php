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

class ExportMessagesRequest extends \Google\Model
{
  /**
   * The end of the range in `send_time` (MSH.7, https://www.hl7.org/documentcen
   * ter/public_temp_2E58C1F9-1C23-BA17-0C6126475344DA9D/wg/conf/HL7MSH.htm) to
   * process. If not specified, the time when the export is scheduled is used.
   * This value has to come after the `start_time` defined below. Only messages
   * whose `send_time` lies in the range `start_time` (inclusive) to `end_time`
   * (exclusive) are exported.
   *
   * @var string
   */
  public $endTime;
  /**
   * Restricts messages exported to those matching a filter, only applicable to
   * PubsubDestination and GcsDestination. The following syntax is available: *
   * A string field value can be written as text inside quotation marks, for
   * example `"query text"`. The only valid relational operation for text fields
   * is equality (`=`), where text is searched within the field, rather than
   * having the field be equal to the text. For example, `"Comment = great"`
   * returns messages with `great` in the comment field. * A number field value
   * can be written as an integer, a decimal, or an exponential. The valid
   * relational operators for number fields are the equality operator (`=`),
   * along with the less than/greater than operators (`<`, `<=`, `>`, `>=`).
   * Note that there is no inequality (`!=`) operator. You can prepend the `NOT`
   * operator to an expression to negate it. * A date field value must be
   * written in the `yyyy-mm-dd` format. Fields with date and time use the
   * RFC3339 time format. Leading zeros are required for one-digit months and
   * days. The valid relational operators for date fields are the equality
   * operator (`=`) , along with the less than/greater than operators (`<`,
   * `<=`, `>`, `>=`). Note that there is no inequality (`!=`) operator. You can
   * prepend the `NOT` operator to an expression to negate it. * Multiple field
   * query expressions can be combined in one query by adding `AND` or `OR`
   * operators between the expressions. If a boolean operator appears within a
   * quoted string, it is not treated as special, and is just another part of
   * the character string to be matched. You can prepend the `NOT` operator to
   * an expression to negate it. The following fields and functions are
   * available for filtering: * `message_type`, from the MSH-9.1 field. For
   * example, `NOT message_type = "ADT"`. * `send_date` or `sendDate`, the
   * `yyyy-mm-dd` date the message was sent in the dataset's time_zone, from the
   * MSH-7 segment. For example, `send_date < "2017-01-02"`. * `send_time`, the
   * timestamp when the message was sent, using the RFC3339 time format for
   * comparisons, from the MSH-7 segment. For example, `send_time <
   * "2017-01-02T00:00:00-05:00"`. * `create_time`, the timestamp when the
   * message was created in the HL7v2 store. Use the RFC3339 time format for
   * comparisons. For example, `create_time < "2017-01-02T00:00:00-05:00"`. *
   * `send_facility`, the care center that the message came from, from the MSH-4
   * segment. For example, `send_facility = "ABC"`. Note: The filter will be
   * applied to every message in the HL7v2 store whose `send_time` lies in the
   * range defined by the `start_time` and the `end_time`. Even if the filter
   * only matches a small set of messages, the export operation can still take a
   * long time to finish when a lot of messages are between the specified
   * `start_time` and `end_time` range.
   *
   * @var string
   */
  public $filter;
  protected $gcsDestinationType = GcsDestination::class;
  protected $gcsDestinationDataType = '';
  protected $pubsubDestinationType = PubsubDestination::class;
  protected $pubsubDestinationDataType = '';
  /**
   * The start of the range in `send_time` (MSH.7, https://www.hl7.org/documentc
   * enter/public_temp_2E58C1F9-1C23-BA17-0C6126475344DA9D/wg/conf/HL7MSH.htm)
   * to process. If not specified, the UNIX epoch (1970-01-01T00:00:00Z) is
   * used. This value has to come before the `end_time` defined below. Only
   * messages whose `send_time` lies in the range `start_time` (inclusive) to
   * `end_time` (exclusive) are exported.
   *
   * @var string
   */
  public $startTime;

  /**
   * The end of the range in `send_time` (MSH.7, https://www.hl7.org/documentcen
   * ter/public_temp_2E58C1F9-1C23-BA17-0C6126475344DA9D/wg/conf/HL7MSH.htm) to
   * process. If not specified, the time when the export is scheduled is used.
   * This value has to come after the `start_time` defined below. Only messages
   * whose `send_time` lies in the range `start_time` (inclusive) to `end_time`
   * (exclusive) are exported.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Restricts messages exported to those matching a filter, only applicable to
   * PubsubDestination and GcsDestination. The following syntax is available: *
   * A string field value can be written as text inside quotation marks, for
   * example `"query text"`. The only valid relational operation for text fields
   * is equality (`=`), where text is searched within the field, rather than
   * having the field be equal to the text. For example, `"Comment = great"`
   * returns messages with `great` in the comment field. * A number field value
   * can be written as an integer, a decimal, or an exponential. The valid
   * relational operators for number fields are the equality operator (`=`),
   * along with the less than/greater than operators (`<`, `<=`, `>`, `>=`).
   * Note that there is no inequality (`!=`) operator. You can prepend the `NOT`
   * operator to an expression to negate it. * A date field value must be
   * written in the `yyyy-mm-dd` format. Fields with date and time use the
   * RFC3339 time format. Leading zeros are required for one-digit months and
   * days. The valid relational operators for date fields are the equality
   * operator (`=`) , along with the less than/greater than operators (`<`,
   * `<=`, `>`, `>=`). Note that there is no inequality (`!=`) operator. You can
   * prepend the `NOT` operator to an expression to negate it. * Multiple field
   * query expressions can be combined in one query by adding `AND` or `OR`
   * operators between the expressions. If a boolean operator appears within a
   * quoted string, it is not treated as special, and is just another part of
   * the character string to be matched. You can prepend the `NOT` operator to
   * an expression to negate it. The following fields and functions are
   * available for filtering: * `message_type`, from the MSH-9.1 field. For
   * example, `NOT message_type = "ADT"`. * `send_date` or `sendDate`, the
   * `yyyy-mm-dd` date the message was sent in the dataset's time_zone, from the
   * MSH-7 segment. For example, `send_date < "2017-01-02"`. * `send_time`, the
   * timestamp when the message was sent, using the RFC3339 time format for
   * comparisons, from the MSH-7 segment. For example, `send_time <
   * "2017-01-02T00:00:00-05:00"`. * `create_time`, the timestamp when the
   * message was created in the HL7v2 store. Use the RFC3339 time format for
   * comparisons. For example, `create_time < "2017-01-02T00:00:00-05:00"`. *
   * `send_facility`, the care center that the message came from, from the MSH-4
   * segment. For example, `send_facility = "ABC"`. Note: The filter will be
   * applied to every message in the HL7v2 store whose `send_time` lies in the
   * range defined by the `start_time` and the `end_time`. Even if the filter
   * only matches a small set of messages, the export operation can still take a
   * long time to finish when a lot of messages are between the specified
   * `start_time` and `end_time` range.
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
   * Export to a Cloud Storage destination.
   *
   * @param GcsDestination $gcsDestination
   */
  public function setGcsDestination(GcsDestination $gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return GcsDestination
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
  /**
   * Export messages to a Pub/Sub topic.
   *
   * @param PubsubDestination $pubsubDestination
   */
  public function setPubsubDestination(PubsubDestination $pubsubDestination)
  {
    $this->pubsubDestination = $pubsubDestination;
  }
  /**
   * @return PubsubDestination
   */
  public function getPubsubDestination()
  {
    return $this->pubsubDestination;
  }
  /**
   * The start of the range in `send_time` (MSH.7, https://www.hl7.org/documentc
   * enter/public_temp_2E58C1F9-1C23-BA17-0C6126475344DA9D/wg/conf/HL7MSH.htm)
   * to process. If not specified, the UNIX epoch (1970-01-01T00:00:00Z) is
   * used. This value has to come before the `end_time` defined below. Only
   * messages whose `send_time` lies in the range `start_time` (inclusive) to
   * `end_time` (exclusive) are exported.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportMessagesRequest::class, 'Google_Service_CloudHealthcare_ExportMessagesRequest');
