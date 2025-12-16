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

class GooglePrivacyDlpV2RequestedDeidentifyOptions extends \Google\Model
{
  protected $snapshotDeidentifyTemplateType = GooglePrivacyDlpV2DeidentifyTemplate::class;
  protected $snapshotDeidentifyTemplateDataType = '';
  protected $snapshotImageRedactTemplateType = GooglePrivacyDlpV2DeidentifyTemplate::class;
  protected $snapshotImageRedactTemplateDataType = '';
  protected $snapshotStructuredDeidentifyTemplateType = GooglePrivacyDlpV2DeidentifyTemplate::class;
  protected $snapshotStructuredDeidentifyTemplateDataType = '';

  /**
   * Snapshot of the state of the `DeidentifyTemplate` from the Deidentify
   * action at the time this job was run.
   *
   * @param GooglePrivacyDlpV2DeidentifyTemplate $snapshotDeidentifyTemplate
   */
  public function setSnapshotDeidentifyTemplate(GooglePrivacyDlpV2DeidentifyTemplate $snapshotDeidentifyTemplate)
  {
    $this->snapshotDeidentifyTemplate = $snapshotDeidentifyTemplate;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyTemplate
   */
  public function getSnapshotDeidentifyTemplate()
  {
    return $this->snapshotDeidentifyTemplate;
  }
  /**
   * Snapshot of the state of the image transformation `DeidentifyTemplate` from
   * the `Deidentify` action at the time this job was run.
   *
   * @param GooglePrivacyDlpV2DeidentifyTemplate $snapshotImageRedactTemplate
   */
  public function setSnapshotImageRedactTemplate(GooglePrivacyDlpV2DeidentifyTemplate $snapshotImageRedactTemplate)
  {
    $this->snapshotImageRedactTemplate = $snapshotImageRedactTemplate;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyTemplate
   */
  public function getSnapshotImageRedactTemplate()
  {
    return $this->snapshotImageRedactTemplate;
  }
  /**
   * Snapshot of the state of the structured `DeidentifyTemplate` from the
   * `Deidentify` action at the time this job was run.
   *
   * @param GooglePrivacyDlpV2DeidentifyTemplate $snapshotStructuredDeidentifyTemplate
   */
  public function setSnapshotStructuredDeidentifyTemplate(GooglePrivacyDlpV2DeidentifyTemplate $snapshotStructuredDeidentifyTemplate)
  {
    $this->snapshotStructuredDeidentifyTemplate = $snapshotStructuredDeidentifyTemplate;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyTemplate
   */
  public function getSnapshotStructuredDeidentifyTemplate()
  {
    return $this->snapshotStructuredDeidentifyTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2RequestedDeidentifyOptions::class, 'Google_Service_DLP_GooglePrivacyDlpV2RequestedDeidentifyOptions');
