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

namespace Google\Service\ApigeeRegistry;

class ApiDeployment extends \Google\Model
{
  /**
   * Text briefly describing how to access the endpoint. Changes to this value
   * will not affect the revision.
   *
   * @var string
   */
  public $accessGuidance;
  /**
   * Annotations attach non-identifying metadata to resources. Annotation keys
   * and values are less restricted than those of labels, but should be
   * generally used for small values of broad interest. Larger, topic- specific
   * metadata should be stored in Artifacts.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * The full resource name (including revision ID) of the spec of the API being
   * served by the deployment. Changes to this value will update the revision.
   * Format: `projects/{project}/locations/{location}/apis/{api}/versions/{versi
   * on}/specs/{spec@revision}`
   *
   * @var string
   */
  public $apiSpecRevision;
  /**
   * Output only. Creation timestamp; when the deployment resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A detailed description.
   *
   * @var string
   */
  public $description;
  /**
   * Human-meaningful name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The address where the deployment is serving. Changes to this value will
   * update the revision.
   *
   * @var string
   */
  public $endpointUri;
  /**
   * The address of the external channel of the API (e.g., the Developer
   * Portal). Changes to this value will not affect the revision.
   *
   * @var string
   */
  public $externalChannelUri;
  /**
   * Text briefly identifying the intended audience of the API. Changes to this
   * value will not affect the revision.
   *
   * @var string
   */
  public $intendedAudience;
  /**
   * Labels attach identifying metadata to resources. Identifying metadata can
   * be used to filter list operations. Label keys and values can be no longer
   * than 64 characters (Unicode codepoints), can only contain lowercase
   * letters, numeric characters, underscores and dashes. International
   * characters are allowed. No more than 64 user labels can be associated with
   * one resource (System labels are excluded). See https://goo.gl/xmQnxf for
   * more information and examples of labels. System reserved label keys are
   * prefixed with `apigeeregistry.googleapis.com/` and cannot be changed.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Resource name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Revision creation timestamp; when the represented revision was
   * created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. Immutable. The revision ID of the deployment. A new revision
   * is committed whenever the deployment contents are changed. The format is an
   * 8-character hexadecimal string.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Output only. Last update timestamp: when the represented revision was last
   * modified.
   *
   * @var string
   */
  public $revisionUpdateTime;

  /**
   * Text briefly describing how to access the endpoint. Changes to this value
   * will not affect the revision.
   *
   * @param string $accessGuidance
   */
  public function setAccessGuidance($accessGuidance)
  {
    $this->accessGuidance = $accessGuidance;
  }
  /**
   * @return string
   */
  public function getAccessGuidance()
  {
    return $this->accessGuidance;
  }
  /**
   * Annotations attach non-identifying metadata to resources. Annotation keys
   * and values are less restricted than those of labels, but should be
   * generally used for small values of broad interest. Larger, topic- specific
   * metadata should be stored in Artifacts.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * The full resource name (including revision ID) of the spec of the API being
   * served by the deployment. Changes to this value will update the revision.
   * Format: `projects/{project}/locations/{location}/apis/{api}/versions/{versi
   * on}/specs/{spec@revision}`
   *
   * @param string $apiSpecRevision
   */
  public function setApiSpecRevision($apiSpecRevision)
  {
    $this->apiSpecRevision = $apiSpecRevision;
  }
  /**
   * @return string
   */
  public function getApiSpecRevision()
  {
    return $this->apiSpecRevision;
  }
  /**
   * Output only. Creation timestamp; when the deployment resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * A detailed description.
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
   * Human-meaningful name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The address where the deployment is serving. Changes to this value will
   * update the revision.
   *
   * @param string $endpointUri
   */
  public function setEndpointUri($endpointUri)
  {
    $this->endpointUri = $endpointUri;
  }
  /**
   * @return string
   */
  public function getEndpointUri()
  {
    return $this->endpointUri;
  }
  /**
   * The address of the external channel of the API (e.g., the Developer
   * Portal). Changes to this value will not affect the revision.
   *
   * @param string $externalChannelUri
   */
  public function setExternalChannelUri($externalChannelUri)
  {
    $this->externalChannelUri = $externalChannelUri;
  }
  /**
   * @return string
   */
  public function getExternalChannelUri()
  {
    return $this->externalChannelUri;
  }
  /**
   * Text briefly identifying the intended audience of the API. Changes to this
   * value will not affect the revision.
   *
   * @param string $intendedAudience
   */
  public function setIntendedAudience($intendedAudience)
  {
    $this->intendedAudience = $intendedAudience;
  }
  /**
   * @return string
   */
  public function getIntendedAudience()
  {
    return $this->intendedAudience;
  }
  /**
   * Labels attach identifying metadata to resources. Identifying metadata can
   * be used to filter list operations. Label keys and values can be no longer
   * than 64 characters (Unicode codepoints), can only contain lowercase
   * letters, numeric characters, underscores and dashes. International
   * characters are allowed. No more than 64 user labels can be associated with
   * one resource (System labels are excluded). See https://goo.gl/xmQnxf for
   * more information and examples of labels. System reserved label keys are
   * prefixed with `apigeeregistry.googleapis.com/` and cannot be changed.
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
   * Resource name.
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
  /**
   * Output only. Revision creation timestamp; when the represented revision was
   * created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. Immutable. The revision ID of the deployment. A new revision
   * is committed whenever the deployment contents are changed. The format is an
   * 8-character hexadecimal string.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. Last update timestamp: when the represented revision was last
   * modified.
   *
   * @param string $revisionUpdateTime
   */
  public function setRevisionUpdateTime($revisionUpdateTime)
  {
    $this->revisionUpdateTime = $revisionUpdateTime;
  }
  /**
   * @return string
   */
  public function getRevisionUpdateTime()
  {
    return $this->revisionUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApiDeployment::class, 'Google_Service_ApigeeRegistry_ApiDeployment');
