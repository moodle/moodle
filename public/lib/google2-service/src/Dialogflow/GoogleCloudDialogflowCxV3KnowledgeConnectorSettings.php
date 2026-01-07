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

class GoogleCloudDialogflowCxV3KnowledgeConnectorSettings extends \Google\Collection
{
  protected $collection_key = 'dataStoreConnections';
  protected $dataStoreConnectionsType = GoogleCloudDialogflowCxV3DataStoreConnection::class;
  protected $dataStoreConnectionsDataType = 'array';
  /**
   * Whether Knowledge Connector is enabled or not.
   *
   * @var bool
   */
  public $enabled;
  /**
   * The target flow to transition to. Format:
   * `projects//locations//agents//flows/`.
   *
   * @var string
   */
  public $targetFlow;
  /**
   * The target page to transition to. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @var string
   */
  public $targetPage;
  protected $triggerFulfillmentType = GoogleCloudDialogflowCxV3Fulfillment::class;
  protected $triggerFulfillmentDataType = '';

  /**
   * Optional. List of related data store connections.
   *
   * @param GoogleCloudDialogflowCxV3DataStoreConnection[] $dataStoreConnections
   */
  public function setDataStoreConnections($dataStoreConnections)
  {
    $this->dataStoreConnections = $dataStoreConnections;
  }
  /**
   * @return GoogleCloudDialogflowCxV3DataStoreConnection[]
   */
  public function getDataStoreConnections()
  {
    return $this->dataStoreConnections;
  }
  /**
   * Whether Knowledge Connector is enabled or not.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * The target flow to transition to. Format:
   * `projects//locations//agents//flows/`.
   *
   * @param string $targetFlow
   */
  public function setTargetFlow($targetFlow)
  {
    $this->targetFlow = $targetFlow;
  }
  /**
   * @return string
   */
  public function getTargetFlow()
  {
    return $this->targetFlow;
  }
  /**
   * The target page to transition to. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @param string $targetPage
   */
  public function setTargetPage($targetPage)
  {
    $this->targetPage = $targetPage;
  }
  /**
   * @return string
   */
  public function getTargetPage()
  {
    return $this->targetPage;
  }
  /**
   * The fulfillment to be triggered. When the answers from the Knowledge
   * Connector are selected by Dialogflow, you can utitlize the request scoped
   * parameter `$request.knowledge.answers` (contains up to the 5 highest
   * confidence answers) and `$request.knowledge.questions` (contains the
   * corresponding questions) to construct the fulfillment.
   *
   * @param GoogleCloudDialogflowCxV3Fulfillment $triggerFulfillment
   */
  public function setTriggerFulfillment(GoogleCloudDialogflowCxV3Fulfillment $triggerFulfillment)
  {
    $this->triggerFulfillment = $triggerFulfillment;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Fulfillment
   */
  public function getTriggerFulfillment()
  {
    return $this->triggerFulfillment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3KnowledgeConnectorSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3KnowledgeConnectorSettings');
