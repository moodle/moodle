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

namespace Google\Service\Dataflow;

class GetWorkerStacktracesResponse extends \Google\Collection
{
  protected $collection_key = 'sdks';
  protected $sdksType = Sdk::class;
  protected $sdksDataType = 'array';

  /**
   * Repeated as unified worker may have multiple SDK processes.
   *
   * @param Sdk[] $sdks
   */
  public function setSdks($sdks)
  {
    $this->sdks = $sdks;
  }
  /**
   * @return Sdk[]
   */
  public function getSdks()
  {
    return $this->sdks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetWorkerStacktracesResponse::class, 'Google_Service_Dataflow_GetWorkerStacktracesResponse');
