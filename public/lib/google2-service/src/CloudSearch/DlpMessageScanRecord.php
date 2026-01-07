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

namespace Google\Service\CloudSearch;

class DlpMessageScanRecord extends \Google\Model
{
  protected $attachmentScanSummaryType = DlpScanSummary::class;
  protected $attachmentScanSummaryDataType = '';
  protected $dlpActionType = DlpAction::class;
  protected $dlpActionDataType = '';
  protected $messageScanSummaryType = DlpScanSummary::class;
  protected $messageScanSummaryDataType = '';
  /**
   * @var string
   */
  public $scanOutcome;

  /**
   * @param DlpScanSummary
   */
  public function setAttachmentScanSummary(DlpScanSummary $attachmentScanSummary)
  {
    $this->attachmentScanSummary = $attachmentScanSummary;
  }
  /**
   * @return DlpScanSummary
   */
  public function getAttachmentScanSummary()
  {
    return $this->attachmentScanSummary;
  }
  /**
   * @param DlpAction
   */
  public function setDlpAction(DlpAction $dlpAction)
  {
    $this->dlpAction = $dlpAction;
  }
  /**
   * @return DlpAction
   */
  public function getDlpAction()
  {
    return $this->dlpAction;
  }
  /**
   * @param DlpScanSummary
   */
  public function setMessageScanSummary(DlpScanSummary $messageScanSummary)
  {
    $this->messageScanSummary = $messageScanSummary;
  }
  /**
   * @return DlpScanSummary
   */
  public function getMessageScanSummary()
  {
    return $this->messageScanSummary;
  }
  /**
   * @param string
   */
  public function setScanOutcome($scanOutcome)
  {
    $this->scanOutcome = $scanOutcome;
  }
  /**
   * @return string
   */
  public function getScanOutcome()
  {
    return $this->scanOutcome;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DlpMessageScanRecord::class, 'Google_Service_CloudSearch_DlpMessageScanRecord');
