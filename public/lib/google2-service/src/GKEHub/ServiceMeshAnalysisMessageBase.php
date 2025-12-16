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

namespace Google\Service\GKEHub;

class ServiceMeshAnalysisMessageBase extends \Google\Model
{
  /**
   * Illegal. Same istio.analysis.v1alpha1.AnalysisMessageBase.Level.UNKNOWN.
   */
  public const LEVEL_LEVEL_UNSPECIFIED = 'LEVEL_UNSPECIFIED';
  /**
   * ERROR represents a misconfiguration that must be fixed.
   */
  public const LEVEL_ERROR = 'ERROR';
  /**
   * WARNING represents a misconfiguration that should be fixed.
   */
  public const LEVEL_WARNING = 'WARNING';
  /**
   * INFO represents an informational finding.
   */
  public const LEVEL_INFO = 'INFO';
  /**
   * A url pointing to the Service Mesh or Istio documentation for this specific
   * error type.
   *
   * @var string
   */
  public $documentationUrl;
  /**
   * Represents how severe a message is.
   *
   * @var string
   */
  public $level;
  protected $typeType = ServiceMeshType::class;
  protected $typeDataType = '';

  /**
   * A url pointing to the Service Mesh or Istio documentation for this specific
   * error type.
   *
   * @param string $documentationUrl
   */
  public function setDocumentationUrl($documentationUrl)
  {
    $this->documentationUrl = $documentationUrl;
  }
  /**
   * @return string
   */
  public function getDocumentationUrl()
  {
    return $this->documentationUrl;
  }
  /**
   * Represents how severe a message is.
   *
   * Accepted values: LEVEL_UNSPECIFIED, ERROR, WARNING, INFO
   *
   * @param self::LEVEL_* $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return self::LEVEL_*
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * Represents the specific type of a message.
   *
   * @param ServiceMeshType $type
   */
  public function setType(ServiceMeshType $type)
  {
    $this->type = $type;
  }
  /**
   * @return ServiceMeshType
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMeshAnalysisMessageBase::class, 'Google_Service_GKEHub_ServiceMeshAnalysisMessageBase');
