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

namespace Google\Service\FirebaseML;

class ModelOperationMetadata extends \Google\Model
{
  /**
   * The status is unspecified
   */
  public const BASIC_OPERATION_STATUS_BASIC_OPERATION_STATUS_UNSPECIFIED = 'BASIC_OPERATION_STATUS_UNSPECIFIED';
  /**
   * The model file is being uploaded
   */
  public const BASIC_OPERATION_STATUS_BASIC_OPERATION_STATUS_UPLOADING = 'BASIC_OPERATION_STATUS_UPLOADING';
  /**
   * The model file is being verified
   */
  public const BASIC_OPERATION_STATUS_BASIC_OPERATION_STATUS_VERIFYING = 'BASIC_OPERATION_STATUS_VERIFYING';
  /**
   * @var string
   */
  public $basicOperationStatus;
  /**
   * The name of the model we are creating/updating The name must have the form
   * `projects/{project_id}/models/{model_id}`
   *
   * @var string
   */
  public $name;

  /**
   * @param self::BASIC_OPERATION_STATUS_* $basicOperationStatus
   */
  public function setBasicOperationStatus($basicOperationStatus)
  {
    $this->basicOperationStatus = $basicOperationStatus;
  }
  /**
   * @return self::BASIC_OPERATION_STATUS_*
   */
  public function getBasicOperationStatus()
  {
    return $this->basicOperationStatus;
  }
  /**
   * The name of the model we are creating/updating The name must have the form
   * `projects/{project_id}/models/{model_id}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModelOperationMetadata::class, 'Google_Service_FirebaseML_ModelOperationMetadata');
