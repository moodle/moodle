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

namespace Google\Service\Chromewebstore;

class PublishItemRequest extends \Google\Collection
{
  /**
   * Default value. This is the same as DEFAULT_PUBLISH.
   */
  public const PUBLISH_TYPE_PUBLISH_TYPE_UNSPECIFIED = 'PUBLISH_TYPE_UNSPECIFIED';
  /**
   * The submission will be published immediately after being approved.
   */
  public const PUBLISH_TYPE_DEFAULT_PUBLISH = 'DEFAULT_PUBLISH';
  /**
   * After approval the submission will be staged and can then be published by
   * the developer.
   */
  public const PUBLISH_TYPE_STAGED_PUBLISH = 'STAGED_PUBLISH';
  protected $collection_key = 'deployInfos';
  protected $deployInfosType = DeployInfo::class;
  protected $deployInfosDataType = 'array';
  /**
   * Optional. Use this to control if the item is published immediately on
   * approval or staged for publishing in the future. Defaults to
   * `DEFAULT_PUBLISH` if unset.
   *
   * @var string
   */
  public $publishType;
  /**
   * Optional. Whether to attempt to skip item review. The API will validate if
   * the item qualifies and return a validation error if the item requires
   * review. Defaults to `false` if unset.
   *
   * @var bool
   */
  public $skipReview;

  /**
   * Optional. Additional deploy information including the desired initial
   * percentage rollout. Defaults to the current value saved in the developer
   * dashboard if unset.
   *
   * @param DeployInfo[] $deployInfos
   */
  public function setDeployInfos($deployInfos)
  {
    $this->deployInfos = $deployInfos;
  }
  /**
   * @return DeployInfo[]
   */
  public function getDeployInfos()
  {
    return $this->deployInfos;
  }
  /**
   * Optional. Use this to control if the item is published immediately on
   * approval or staged for publishing in the future. Defaults to
   * `DEFAULT_PUBLISH` if unset.
   *
   * Accepted values: PUBLISH_TYPE_UNSPECIFIED, DEFAULT_PUBLISH, STAGED_PUBLISH
   *
   * @param self::PUBLISH_TYPE_* $publishType
   */
  public function setPublishType($publishType)
  {
    $this->publishType = $publishType;
  }
  /**
   * @return self::PUBLISH_TYPE_*
   */
  public function getPublishType()
  {
    return $this->publishType;
  }
  /**
   * Optional. Whether to attempt to skip item review. The API will validate if
   * the item qualifies and return a validation error if the item requires
   * review. Defaults to `false` if unset.
   *
   * @param bool $skipReview
   */
  public function setSkipReview($skipReview)
  {
    $this->skipReview = $skipReview;
  }
  /**
   * @return bool
   */
  public function getSkipReview()
  {
    return $this->skipReview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishItemRequest::class, 'Google_Service_Chromewebstore_PublishItemRequest');
