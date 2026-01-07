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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1SourceMetadata extends \Google\Model
{
  /**
   * Source type not specified.
   */
  public const SOURCE_TYPE_SOURCE_TYPE_UNSPECIFIED = 'SOURCE_TYPE_UNSPECIFIED';
  /**
   * Source type plugin.
   */
  public const SOURCE_TYPE_PLUGIN = 'PLUGIN';
  /**
   * Output only. The time at which the resource was created at the source.
   *
   * @var string
   */
  public $originalResourceCreateTime;
  /**
   * Output only. The unique identifier of the resource at the source.
   *
   * @var string
   */
  public $originalResourceId;
  /**
   * Output only. The time at which the resource was last updated at the source.
   *
   * @var string
   */
  public $originalResourceUpdateTime;
  protected $pluginInstanceActionSourceType = GoogleCloudApihubV1PluginInstanceActionSource::class;
  protected $pluginInstanceActionSourceDataType = '';
  /**
   * Output only. The type of the source.
   *
   * @var string
   */
  public $sourceType;

  /**
   * Output only. The time at which the resource was created at the source.
   *
   * @param string $originalResourceCreateTime
   */
  public function setOriginalResourceCreateTime($originalResourceCreateTime)
  {
    $this->originalResourceCreateTime = $originalResourceCreateTime;
  }
  /**
   * @return string
   */
  public function getOriginalResourceCreateTime()
  {
    return $this->originalResourceCreateTime;
  }
  /**
   * Output only. The unique identifier of the resource at the source.
   *
   * @param string $originalResourceId
   */
  public function setOriginalResourceId($originalResourceId)
  {
    $this->originalResourceId = $originalResourceId;
  }
  /**
   * @return string
   */
  public function getOriginalResourceId()
  {
    return $this->originalResourceId;
  }
  /**
   * Output only. The time at which the resource was last updated at the source.
   *
   * @param string $originalResourceUpdateTime
   */
  public function setOriginalResourceUpdateTime($originalResourceUpdateTime)
  {
    $this->originalResourceUpdateTime = $originalResourceUpdateTime;
  }
  /**
   * @return string
   */
  public function getOriginalResourceUpdateTime()
  {
    return $this->originalResourceUpdateTime;
  }
  /**
   * Output only. The source of the resource is a plugin instance action.
   *
   * @param GoogleCloudApihubV1PluginInstanceActionSource $pluginInstanceActionSource
   */
  public function setPluginInstanceActionSource(GoogleCloudApihubV1PluginInstanceActionSource $pluginInstanceActionSource)
  {
    $this->pluginInstanceActionSource = $pluginInstanceActionSource;
  }
  /**
   * @return GoogleCloudApihubV1PluginInstanceActionSource
   */
  public function getPluginInstanceActionSource()
  {
    return $this->pluginInstanceActionSource;
  }
  /**
   * Output only. The type of the source.
   *
   * Accepted values: SOURCE_TYPE_UNSPECIFIED, PLUGIN
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1SourceMetadata::class, 'Google_Service_APIhub_GoogleCloudApihubV1SourceMetadata');
