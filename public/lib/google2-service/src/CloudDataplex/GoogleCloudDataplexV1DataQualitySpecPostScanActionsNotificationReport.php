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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataQualitySpecPostScanActionsNotificationReport extends \Google\Model
{
  protected $jobEndTriggerType = GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobEndTrigger::class;
  protected $jobEndTriggerDataType = '';
  protected $jobFailureTriggerType = GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobFailureTrigger::class;
  protected $jobFailureTriggerDataType = '';
  protected $recipientsType = GoogleCloudDataplexV1DataQualitySpecPostScanActionsRecipients::class;
  protected $recipientsDataType = '';
  protected $scoreThresholdTriggerType = GoogleCloudDataplexV1DataQualitySpecPostScanActionsScoreThresholdTrigger::class;
  protected $scoreThresholdTriggerDataType = '';

  /**
   * Optional. If set, report will be sent when a scan job ends.
   *
   * @param GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobEndTrigger $jobEndTrigger
   */
  public function setJobEndTrigger(GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobEndTrigger $jobEndTrigger)
  {
    $this->jobEndTrigger = $jobEndTrigger;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobEndTrigger
   */
  public function getJobEndTrigger()
  {
    return $this->jobEndTrigger;
  }
  /**
   * Optional. If set, report will be sent when a scan job fails.
   *
   * @param GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobFailureTrigger $jobFailureTrigger
   */
  public function setJobFailureTrigger(GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobFailureTrigger $jobFailureTrigger)
  {
    $this->jobFailureTrigger = $jobFailureTrigger;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualitySpecPostScanActionsJobFailureTrigger
   */
  public function getJobFailureTrigger()
  {
    return $this->jobFailureTrigger;
  }
  /**
   * Required. The recipients who will receive the notification report.
   *
   * @param GoogleCloudDataplexV1DataQualitySpecPostScanActionsRecipients $recipients
   */
  public function setRecipients(GoogleCloudDataplexV1DataQualitySpecPostScanActionsRecipients $recipients)
  {
    $this->recipients = $recipients;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualitySpecPostScanActionsRecipients
   */
  public function getRecipients()
  {
    return $this->recipients;
  }
  /**
   * Optional. If set, report will be sent when score threshold is met.
   *
   * @param GoogleCloudDataplexV1DataQualitySpecPostScanActionsScoreThresholdTrigger $scoreThresholdTrigger
   */
  public function setScoreThresholdTrigger(GoogleCloudDataplexV1DataQualitySpecPostScanActionsScoreThresholdTrigger $scoreThresholdTrigger)
  {
    $this->scoreThresholdTrigger = $scoreThresholdTrigger;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualitySpecPostScanActionsScoreThresholdTrigger
   */
  public function getScoreThresholdTrigger()
  {
    return $this->scoreThresholdTrigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualitySpecPostScanActionsNotificationReport::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualitySpecPostScanActionsNotificationReport');
