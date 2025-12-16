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

namespace Google\Service\CloudNaturalLanguage;

class XPSSpeechModelSpecSubModelSpec extends \Google\Model
{
  public const BIASING_MODEL_TYPE_BIASING_MODEL_TYPE_UNSPECIFIED = 'BIASING_MODEL_TYPE_UNSPECIFIED';
  /**
   * Build biasing model on top of COMMAND_AND_SEARCH model
   */
  public const BIASING_MODEL_TYPE_COMMAND_AND_SEARCH = 'COMMAND_AND_SEARCH';
  /**
   * Build biasing model on top of PHONE_CALL model
   */
  public const BIASING_MODEL_TYPE_PHONE_CALL = 'PHONE_CALL';
  /**
   * Build biasing model on top of VIDEO model
   */
  public const BIASING_MODEL_TYPE_VIDEO = 'VIDEO';
  /**
   * Build biasing model on top of DEFAULT model
   */
  public const BIASING_MODEL_TYPE_DEFAULT = 'DEFAULT';
  /**
   * Type of the biasing model.
   *
   * @var string
   */
  public $biasingModelType;
  /**
   * In S3, Recognition ClientContextId.client_id
   *
   * @var string
   */
  public $clientId;
  /**
   * In S3, Recognition ClientContextId.context_id
   *
   * @var string
   */
  public $contextId;
  /**
   * If true then it means we have an enhanced version of the biasing models.
   *
   * @var bool
   */
  public $isEnhancedModel;

  /**
   * Type of the biasing model.
   *
   * Accepted values: BIASING_MODEL_TYPE_UNSPECIFIED, COMMAND_AND_SEARCH,
   * PHONE_CALL, VIDEO, DEFAULT
   *
   * @param self::BIASING_MODEL_TYPE_* $biasingModelType
   */
  public function setBiasingModelType($biasingModelType)
  {
    $this->biasingModelType = $biasingModelType;
  }
  /**
   * @return self::BIASING_MODEL_TYPE_*
   */
  public function getBiasingModelType()
  {
    return $this->biasingModelType;
  }
  /**
   * In S3, Recognition ClientContextId.client_id
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * In S3, Recognition ClientContextId.context_id
   *
   * @param string $contextId
   */
  public function setContextId($contextId)
  {
    $this->contextId = $contextId;
  }
  /**
   * @return string
   */
  public function getContextId()
  {
    return $this->contextId;
  }
  /**
   * If true then it means we have an enhanced version of the biasing models.
   *
   * @param bool $isEnhancedModel
   */
  public function setIsEnhancedModel($isEnhancedModel)
  {
    $this->isEnhancedModel = $isEnhancedModel;
  }
  /**
   * @return bool
   */
  public function getIsEnhancedModel()
  {
    return $this->isEnhancedModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSSpeechModelSpecSubModelSpec::class, 'Google_Service_CloudNaturalLanguage_XPSSpeechModelSpecSubModelSpec');
