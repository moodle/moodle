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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1Action extends \Google\Model
{
  protected $accessControlType = GoogleCloudContentwarehouseV1AccessControlAction::class;
  protected $accessControlDataType = '';
  /**
   * ID of the action. Managed internally.
   *
   * @var string
   */
  public $actionId;
  protected $addToFolderType = GoogleCloudContentwarehouseV1AddToFolderAction::class;
  protected $addToFolderDataType = '';
  protected $dataUpdateType = GoogleCloudContentwarehouseV1DataUpdateAction::class;
  protected $dataUpdateDataType = '';
  protected $dataValidationType = GoogleCloudContentwarehouseV1DataValidationAction::class;
  protected $dataValidationDataType = '';
  protected $deleteDocumentActionType = GoogleCloudContentwarehouseV1DeleteDocumentAction::class;
  protected $deleteDocumentActionDataType = '';
  protected $publishToPubSubType = GoogleCloudContentwarehouseV1PublishAction::class;
  protected $publishToPubSubDataType = '';
  protected $removeFromFolderActionType = GoogleCloudContentwarehouseV1RemoveFromFolderAction::class;
  protected $removeFromFolderActionDataType = '';

  /**
   * Action triggering access control operations.
   *
   * @param GoogleCloudContentwarehouseV1AccessControlAction $accessControl
   */
  public function setAccessControl(GoogleCloudContentwarehouseV1AccessControlAction $accessControl)
  {
    $this->accessControl = $accessControl;
  }
  /**
   * @return GoogleCloudContentwarehouseV1AccessControlAction
   */
  public function getAccessControl()
  {
    return $this->accessControl;
  }
  /**
   * ID of the action. Managed internally.
   *
   * @param string $actionId
   */
  public function setActionId($actionId)
  {
    $this->actionId = $actionId;
  }
  /**
   * @return string
   */
  public function getActionId()
  {
    return $this->actionId;
  }
  /**
   * Action triggering create document link operation.
   *
   * @param GoogleCloudContentwarehouseV1AddToFolderAction $addToFolder
   */
  public function setAddToFolder(GoogleCloudContentwarehouseV1AddToFolderAction $addToFolder)
  {
    $this->addToFolder = $addToFolder;
  }
  /**
   * @return GoogleCloudContentwarehouseV1AddToFolderAction
   */
  public function getAddToFolder()
  {
    return $this->addToFolder;
  }
  /**
   * Action triggering data update operations.
   *
   * @param GoogleCloudContentwarehouseV1DataUpdateAction $dataUpdate
   */
  public function setDataUpdate(GoogleCloudContentwarehouseV1DataUpdateAction $dataUpdate)
  {
    $this->dataUpdate = $dataUpdate;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DataUpdateAction
   */
  public function getDataUpdate()
  {
    return $this->dataUpdate;
  }
  /**
   * Action triggering data validation operations.
   *
   * @param GoogleCloudContentwarehouseV1DataValidationAction $dataValidation
   */
  public function setDataValidation(GoogleCloudContentwarehouseV1DataValidationAction $dataValidation)
  {
    $this->dataValidation = $dataValidation;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DataValidationAction
   */
  public function getDataValidation()
  {
    return $this->dataValidation;
  }
  /**
   * Action deleting the document.
   *
   * @param GoogleCloudContentwarehouseV1DeleteDocumentAction $deleteDocumentAction
   */
  public function setDeleteDocumentAction(GoogleCloudContentwarehouseV1DeleteDocumentAction $deleteDocumentAction)
  {
    $this->deleteDocumentAction = $deleteDocumentAction;
  }
  /**
   * @return GoogleCloudContentwarehouseV1DeleteDocumentAction
   */
  public function getDeleteDocumentAction()
  {
    return $this->deleteDocumentAction;
  }
  /**
   * Action publish to Pub/Sub operation.
   *
   * @param GoogleCloudContentwarehouseV1PublishAction $publishToPubSub
   */
  public function setPublishToPubSub(GoogleCloudContentwarehouseV1PublishAction $publishToPubSub)
  {
    $this->publishToPubSub = $publishToPubSub;
  }
  /**
   * @return GoogleCloudContentwarehouseV1PublishAction
   */
  public function getPublishToPubSub()
  {
    return $this->publishToPubSub;
  }
  /**
   * Action removing a document from a folder.
   *
   * @param GoogleCloudContentwarehouseV1RemoveFromFolderAction $removeFromFolderAction
   */
  public function setRemoveFromFolderAction(GoogleCloudContentwarehouseV1RemoveFromFolderAction $removeFromFolderAction)
  {
    $this->removeFromFolderAction = $removeFromFolderAction;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RemoveFromFolderAction
   */
  public function getRemoveFromFolderAction()
  {
    return $this->removeFromFolderAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1Action::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1Action');
