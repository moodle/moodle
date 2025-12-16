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

namespace Google\Service\CloudSearch;

class UpdateDataSourceRequest extends \Google\Model
{
  protected $debugOptionsType = DebugOptions::class;
  protected $debugOptionsDataType = '';
  protected $sourceType = DataSource::class;
  protected $sourceDataType = '';
  /**
   * Only applies to
   * [`settings.datasources.patch`](https://developers.google.com/cloud-
   * search/docs/reference/rest/v1/settings.datasources/patch). Update mask to
   * control which fields to update. Example field paths: `name`, `displayName`.
   * * If `update_mask` is non-empty, then only the fields specified in the
   * `update_mask` are updated. * If you specify a field in the `update_mask`,
   * but don't specify its value in the source, that field is cleared. * If the
   * `update_mask` is not present or empty or has the value `*`, then all fields
   * are updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Common debug options.
   *
   * @param DebugOptions $debugOptions
   */
  public function setDebugOptions(DebugOptions $debugOptions)
  {
    $this->debugOptions = $debugOptions;
  }
  /**
   * @return DebugOptions
   */
  public function getDebugOptions()
  {
    return $this->debugOptions;
  }
  /**
   * @param DataSource $source
   */
  public function setSource(DataSource $source)
  {
    $this->source = $source;
  }
  /**
   * @return DataSource
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Only applies to
   * [`settings.datasources.patch`](https://developers.google.com/cloud-
   * search/docs/reference/rest/v1/settings.datasources/patch). Update mask to
   * control which fields to update. Example field paths: `name`, `displayName`.
   * * If `update_mask` is non-empty, then only the fields specified in the
   * `update_mask` are updated. * If you specify a field in the `update_mask`,
   * but don't specify its value in the source, that field is cleared. * If the
   * `update_mask` is not present or empty or has the value `*`, then all fields
   * are updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDataSourceRequest::class, 'Google_Service_CloudSearch_UpdateDataSourceRequest');
