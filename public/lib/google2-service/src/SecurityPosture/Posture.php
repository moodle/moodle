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

namespace Google\Service\SecurityPosture;

class Posture extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The posture is deprecated and can no longer be deployed.
   */
  public const STATE_DEPRECATED = 'DEPRECATED';
  /**
   * The posture is a draft and is not ready to deploy.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The posture is complete and ready to deploy.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  protected $collection_key = 'policySets';
  /**
   * Optional. The user-specified annotations for the posture. For details about
   * the values you can use in an annotation, see [AIP-148: Standard
   * fields](https://google.aip.dev/148#annotations).
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The categories that the posture belongs to, as determined by
   * the Security Posture API.
   *
   * @var string[]
   */
  public $categories;
  /**
   * Output only. The time at which the posture was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of the posture.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. An opaque identifier for the current version of the posture at
   * the specified `revision_id`. To prevent concurrent updates from overwriting
   * each other, always provide the `etag` when you update a posture. You can
   * also provide the `etag` when you delete a posture, to help ensure that
   * you're deleting the intended version of the posture.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. Identifier. The name of the posture, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
   *
   * @var string
   */
  public $name;
  protected $policySetsType = PolicySet::class;
  protected $policySetsDataType = 'array';
  /**
   * Output only. Whether the posture is in the process of being updated.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. Immutable. An opaque eight-character string that identifies
   * the revision of the posture. A posture can have multiple revisions; when
   * you deploy a posture, you deploy a specific revision of the posture.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Required. The state of the posture at the specified `revision_id`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which the posture was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The user-specified annotations for the posture. For details about
   * the values you can use in an annotation, see [AIP-148: Standard
   * fields](https://google.aip.dev/148#annotations).
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
   * Output only. The categories that the posture belongs to, as determined by
   * the Security Posture API.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Output only. The time at which the posture was created.
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
   * Optional. A description of the posture.
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
   * Optional. An opaque identifier for the current version of the posture at
   * the specified `revision_id`. To prevent concurrent updates from overwriting
   * each other, always provide the `etag` when you update a posture. You can
   * also provide the `etag` when you delete a posture, to help ensure that
   * you're deleting the intended version of the posture.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. Identifier. The name of the posture, in the format
   * `organizations/{organization}/locations/global/postures/{posture_id}`.
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
   * Required. The PolicySet resources that the posture includes.
   *
   * @param PolicySet[] $policySets
   */
  public function setPolicySets($policySets)
  {
    $this->policySets = $policySets;
  }
  /**
   * @return PolicySet[]
   */
  public function getPolicySets()
  {
    return $this->policySets;
  }
  /**
   * Output only. Whether the posture is in the process of being updated.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. Immutable. An opaque eight-character string that identifies
   * the revision of the posture. A posture can have multiple revisions; when
   * you deploy a posture, you deploy a specific revision of the posture.
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
   * Required. The state of the posture at the specified `revision_id`.
   *
   * Accepted values: STATE_UNSPECIFIED, DEPRECATED, DRAFT, ACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The time at which the posture was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Posture::class, 'Google_Service_SecurityPosture_Posture');
