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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequest extends \Google\Model
{
  /**
   * Filter matching identity mappings to purge. The eligible field for
   * filtering is: * `update_time`: in ISO 8601 "zulu" format. * `external_id`
   * Examples: * Deleting all identity mappings updated in a time range:
   * `update_time > "2012-04-23T18:25:43.511Z" AND update_time <
   * "2012-04-23T18:30:43.511Z"` * Deleting all identity mappings for a given
   * external_id: `external_id = "id1"` * Deleting all identity mappings inside
   * an identity mapping store: `*` The filtering fields are assumed to have an
   * implicit AND. Should not be used with source. An error will be thrown, if
   * both are provided.
   *
   * @var string
   */
  public $filter;
  /**
   * Actually performs the purge. If `force` is set to false, return the
   * expected purge count without deleting any identity mappings. This field is
   * only supported for purge with filter. For input source this field is
   * ignored and data will be purged regardless of the value of this field.
   *
   * @var bool
   */
  public $force;
  protected $inlineSourceType = GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource::class;
  protected $inlineSourceDataType = '';

  /**
   * Filter matching identity mappings to purge. The eligible field for
   * filtering is: * `update_time`: in ISO 8601 "zulu" format. * `external_id`
   * Examples: * Deleting all identity mappings updated in a time range:
   * `update_time > "2012-04-23T18:25:43.511Z" AND update_time <
   * "2012-04-23T18:30:43.511Z"` * Deleting all identity mappings for a given
   * external_id: `external_id = "id1"` * Deleting all identity mappings inside
   * an identity mapping store: `*` The filtering fields are assumed to have an
   * implicit AND. Should not be used with source. An error will be thrown, if
   * both are provided.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Actually performs the purge. If `force` is set to false, return the
   * expected purge count without deleting any identity mappings. This field is
   * only supported for purge with filter. For input source this field is
   * ignored and data will be purged regardless of the value of this field.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * The inline source to purge identity mapping entries from.
   *
   * @param GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequestInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1PurgeIdentityMappingsRequest');
