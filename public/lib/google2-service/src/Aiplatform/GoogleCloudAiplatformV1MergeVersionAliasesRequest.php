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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1MergeVersionAliasesRequest extends \Google\Collection
{
  protected $collection_key = 'versionAliases';
  /**
   * Required. The set of version aliases to merge. The alias should be at most
   * 128 characters, and match `a-z{0,126}[a-z-0-9]`. Add the `-` prefix to an
   * alias means removing that alias from the version. `-` is NOT counted in the
   * 128 characters. Example: `-golden` means removing the `golden` alias from
   * the version. There is NO ordering in aliases, which means 1) The aliases
   * returned from GetModel API might not have the exactly same order from this
   * MergeVersionAliases API. 2) Adding and deleting the same alias in the
   * request is not recommended, and the 2 operations will be cancelled out.
   *
   * @var string[]
   */
  public $versionAliases;

  /**
   * Required. The set of version aliases to merge. The alias should be at most
   * 128 characters, and match `a-z{0,126}[a-z-0-9]`. Add the `-` prefix to an
   * alias means removing that alias from the version. `-` is NOT counted in the
   * 128 characters. Example: `-golden` means removing the `golden` alias from
   * the version. There is NO ordering in aliases, which means 1) The aliases
   * returned from GetModel API might not have the exactly same order from this
   * MergeVersionAliases API. 2) Adding and deleting the same alias in the
   * request is not recommended, and the 2 operations will be cancelled out.
   *
   * @param string[] $versionAliases
   */
  public function setVersionAliases($versionAliases)
  {
    $this->versionAliases = $versionAliases;
  }
  /**
   * @return string[]
   */
  public function getVersionAliases()
  {
    return $this->versionAliases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MergeVersionAliasesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MergeVersionAliasesRequest');
