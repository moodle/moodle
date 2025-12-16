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

namespace Google\Service\MigrationCenterAPI;

class BatchDeleteAssetsRequest extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * Optional. When this value is set to `true` the request is a no-op for non-
   * existing assets. See https://google.aip.dev/135#delete-if-existing for
   * additional details. Default value is `false`.
   *
   * @var bool
   */
  public $allowMissing;
  protected $cascadingRulesType = CascadingRule::class;
  protected $cascadingRulesDataType = 'array';
  /**
   * Required. The IDs of the assets to delete. A maximum of 1000 assets can be
   * deleted in a batch. Format:
   * projects/{project}/locations/{location}/assets/{name}.
   *
   * @var string[]
   */
  public $names;

  /**
   * Optional. When this value is set to `true` the request is a no-op for non-
   * existing assets. See https://google.aip.dev/135#delete-if-existing for
   * additional details. Default value is `false`.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Optional. Optional cascading rules for deleting related assets.
   *
   * @param CascadingRule[] $cascadingRules
   */
  public function setCascadingRules($cascadingRules)
  {
    $this->cascadingRules = $cascadingRules;
  }
  /**
   * @return CascadingRule[]
   */
  public function getCascadingRules()
  {
    return $this->cascadingRules;
  }
  /**
   * Required. The IDs of the assets to delete. A maximum of 1000 assets can be
   * deleted in a batch. Format:
   * projects/{project}/locations/{location}/assets/{name}.
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchDeleteAssetsRequest::class, 'Google_Service_MigrationCenterAPI_BatchDeleteAssetsRequest');
