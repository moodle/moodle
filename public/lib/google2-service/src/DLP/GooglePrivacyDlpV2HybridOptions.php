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

class GooglePrivacyDlpV2HybridOptions extends \Google\Collection
{
  protected $collection_key = 'requiredFindingLabelKeys';
  /**
   * A short description of where the data is coming from. Will be stored once
   * in the job. 256 max length.
   *
   * @var string
   */
  public $description;
  /**
   * To organize findings, these labels will be added to each finding. Label
   * keys must be between 1 and 63 characters long and must conform to the
   * following regular expression: `[a-z]([-a-z0-9]*[a-z0-9])?`. Label values
   * must be between 0 and 63 characters long and must conform to the regular
   * expression `([a-z]([-a-z0-9]*[a-z0-9])?)?`. No more than 10 labels can be
   * associated with a given finding. Examples: * `"environment" : "production"`
   * * `"pipeline" : "etl"`
   *
   * @var string[]
   */
  public $labels;
  /**
   * These are labels that each inspection request must include within their
   * 'finding_labels' map. Request may contain others, but any missing one of
   * these will be rejected. Label keys must be between 1 and 63 characters long
   * and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. No more than 10 keys can be required.
   *
   * @var string[]
   */
  public $requiredFindingLabelKeys;
  protected $tableOptionsType = GooglePrivacyDlpV2TableOptions::class;
  protected $tableOptionsDataType = '';

  /**
   * A short description of where the data is coming from. Will be stored once
   * in the job. 256 max length.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * To organize findings, these labels will be added to each finding. Label
   * keys must be between 1 and 63 characters long and must conform to the
   * following regular expression: `[a-z]([-a-z0-9]*[a-z0-9])?`. Label values
   * must be between 0 and 63 characters long and must conform to the regular
   * expression `([a-z]([-a-z0-9]*[a-z0-9])?)?`. No more than 10 labels can be
   * associated with a given finding. Examples: * `"environment" : "production"`
   * * `"pipeline" : "etl"`
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * These are labels that each inspection request must include within their
   * 'finding_labels' map. Request may contain others, but any missing one of
   * these will be rejected. Label keys must be between 1 and 63 characters long
   * and must conform to the following regular expression:
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. No more than 10 keys can be required.
   *
   * @param string[] $requiredFindingLabelKeys
   */
  public function setRequiredFindingLabelKeys($requiredFindingLabelKeys)
  {
    $this->requiredFindingLabelKeys = $requiredFindingLabelKeys;
  }
  /**
   * @return string[]
   */
  public function getRequiredFindingLabelKeys()
  {
    return $this->requiredFindingLabelKeys;
  }
  /**
   * If the container is a table, additional information to make findings
   * meaningful such as the columns that are primary keys.
   *
   * @param GooglePrivacyDlpV2TableOptions $tableOptions
   */
  public function setTableOptions(GooglePrivacyDlpV2TableOptions $tableOptions)
  {
    $this->tableOptions = $tableOptions;
  }
  /**
   * @return GooglePrivacyDlpV2TableOptions
   */
  public function getTableOptions()
  {
    return $this->tableOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2HybridOptions::class, 'Google_Service_DLP_GooglePrivacyDlpV2HybridOptions');
