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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ImportPlaybookRequest extends \Google\Model
{
  protected $importStrategyType = GoogleCloudDialogflowCxV3PlaybookImportStrategy::class;
  protected $importStrategyDataType = '';
  /**
   * Uncompressed raw byte content for playbook.
   *
   * @var string
   */
  public $playbookContent;
  /**
   * [Dialogflow access control]
   * (https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $playbookUri;

  /**
   * Optional. Specifies the import strategy used when resolving resource
   * conflicts.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookImportStrategy $importStrategy
   */
  public function setImportStrategy(GoogleCloudDialogflowCxV3PlaybookImportStrategy $importStrategy)
  {
    $this->importStrategy = $importStrategy;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookImportStrategy
   */
  public function getImportStrategy()
  {
    return $this->importStrategy;
  }
  /**
   * Uncompressed raw byte content for playbook.
   *
   * @param string $playbookContent
   */
  public function setPlaybookContent($playbookContent)
  {
    $this->playbookContent = $playbookContent;
  }
  /**
   * @return string
   */
  public function getPlaybookContent()
  {
    return $this->playbookContent;
  }
  /**
   * [Dialogflow access control]
   * (https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @param string $playbookUri
   */
  public function setPlaybookUri($playbookUri)
  {
    $this->playbookUri = $playbookUri;
  }
  /**
   * @return string
   */
  public function getPlaybookUri()
  {
    return $this->playbookUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ImportPlaybookRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ImportPlaybookRequest');
