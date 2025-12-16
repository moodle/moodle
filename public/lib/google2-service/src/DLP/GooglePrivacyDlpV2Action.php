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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Action extends \Google\Model
{
  protected $deidentifyType = GooglePrivacyDlpV2Deidentify::class;
  protected $deidentifyDataType = '';
  protected $jobNotificationEmailsType = GooglePrivacyDlpV2JobNotificationEmails::class;
  protected $jobNotificationEmailsDataType = '';
  protected $pubSubType = GooglePrivacyDlpV2PublishToPubSub::class;
  protected $pubSubDataType = '';
  protected $publishFindingsToCloudDataCatalogType = GooglePrivacyDlpV2PublishFindingsToCloudDataCatalog::class;
  protected $publishFindingsToCloudDataCatalogDataType = '';
  protected $publishFindingsToDataplexCatalogType = GooglePrivacyDlpV2PublishFindingsToDataplexCatalog::class;
  protected $publishFindingsToDataplexCatalogDataType = '';
  protected $publishSummaryToCsccType = GooglePrivacyDlpV2PublishSummaryToCscc::class;
  protected $publishSummaryToCsccDataType = '';
  protected $publishToStackdriverType = GooglePrivacyDlpV2PublishToStackdriver::class;
  protected $publishToStackdriverDataType = '';
  protected $saveFindingsType = GooglePrivacyDlpV2SaveFindings::class;
  protected $saveFindingsDataType = '';

  /**
   * Create a de-identified copy of the input data.
   *
   * @param GooglePrivacyDlpV2Deidentify $deidentify
   */
  public function setDeidentify(GooglePrivacyDlpV2Deidentify $deidentify)
  {
    $this->deidentify = $deidentify;
  }
  /**
   * @return GooglePrivacyDlpV2Deidentify
   */
  public function getDeidentify()
  {
    return $this->deidentify;
  }
  /**
   * Sends an email when the job completes. The email goes to IAM project owners
   * and technical [Essential Contacts](https://cloud.google.com/resource-
   * manager/docs/managing-notification-contacts).
   *
   * @param GooglePrivacyDlpV2JobNotificationEmails $jobNotificationEmails
   */
  public function setJobNotificationEmails(GooglePrivacyDlpV2JobNotificationEmails $jobNotificationEmails)
  {
    $this->jobNotificationEmails = $jobNotificationEmails;
  }
  /**
   * @return GooglePrivacyDlpV2JobNotificationEmails
   */
  public function getJobNotificationEmails()
  {
    return $this->jobNotificationEmails;
  }
  /**
   * Publish a notification to a Pub/Sub topic.
   *
   * @param GooglePrivacyDlpV2PublishToPubSub $pubSub
   */
  public function setPubSub(GooglePrivacyDlpV2PublishToPubSub $pubSub)
  {
    $this->pubSub = $pubSub;
  }
  /**
   * @return GooglePrivacyDlpV2PublishToPubSub
   */
  public function getPubSub()
  {
    return $this->pubSub;
  }
  /**
   * Deprecated because Data Catalog is being turned down. Use
   * publish_findings_to_dataplex_catalog to publish findings to Dataplex
   * Universal Catalog.
   *
   * @deprecated
   * @param GooglePrivacyDlpV2PublishFindingsToCloudDataCatalog $publishFindingsToCloudDataCatalog
   */
  public function setPublishFindingsToCloudDataCatalog(GooglePrivacyDlpV2PublishFindingsToCloudDataCatalog $publishFindingsToCloudDataCatalog)
  {
    $this->publishFindingsToCloudDataCatalog = $publishFindingsToCloudDataCatalog;
  }
  /**
   * @deprecated
   * @return GooglePrivacyDlpV2PublishFindingsToCloudDataCatalog
   */
  public function getPublishFindingsToCloudDataCatalog()
  {
    return $this->publishFindingsToCloudDataCatalog;
  }
  /**
   * Publish findings as an aspect to Dataplex Universal Catalog.
   *
   * @param GooglePrivacyDlpV2PublishFindingsToDataplexCatalog $publishFindingsToDataplexCatalog
   */
  public function setPublishFindingsToDataplexCatalog(GooglePrivacyDlpV2PublishFindingsToDataplexCatalog $publishFindingsToDataplexCatalog)
  {
    $this->publishFindingsToDataplexCatalog = $publishFindingsToDataplexCatalog;
  }
  /**
   * @return GooglePrivacyDlpV2PublishFindingsToDataplexCatalog
   */
  public function getPublishFindingsToDataplexCatalog()
  {
    return $this->publishFindingsToDataplexCatalog;
  }
  /**
   * Publish summary to Cloud Security Command Center (Alpha).
   *
   * @param GooglePrivacyDlpV2PublishSummaryToCscc $publishSummaryToCscc
   */
  public function setPublishSummaryToCscc(GooglePrivacyDlpV2PublishSummaryToCscc $publishSummaryToCscc)
  {
    $this->publishSummaryToCscc = $publishSummaryToCscc;
  }
  /**
   * @return GooglePrivacyDlpV2PublishSummaryToCscc
   */
  public function getPublishSummaryToCscc()
  {
    return $this->publishSummaryToCscc;
  }
  /**
   * Enable Stackdriver metric dlp.googleapis.com/finding_count.
   *
   * @param GooglePrivacyDlpV2PublishToStackdriver $publishToStackdriver
   */
  public function setPublishToStackdriver(GooglePrivacyDlpV2PublishToStackdriver $publishToStackdriver)
  {
    $this->publishToStackdriver = $publishToStackdriver;
  }
  /**
   * @return GooglePrivacyDlpV2PublishToStackdriver
   */
  public function getPublishToStackdriver()
  {
    return $this->publishToStackdriver;
  }
  /**
   * Save resulting findings in a provided location.
   *
   * @param GooglePrivacyDlpV2SaveFindings $saveFindings
   */
  public function setSaveFindings(GooglePrivacyDlpV2SaveFindings $saveFindings)
  {
    $this->saveFindings = $saveFindings;
  }
  /**
   * @return GooglePrivacyDlpV2SaveFindings
   */
  public function getSaveFindings()
  {
    return $this->saveFindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Action::class, 'Google_Service_DLP_GooglePrivacyDlpV2Action');
