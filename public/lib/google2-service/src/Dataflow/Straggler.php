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

class Straggler extends \Google\Model
{
  protected $batchStragglerType = StragglerInfo::class;
  protected $batchStragglerDataType = '';
  protected $streamingStragglerType = StreamingStragglerInfo::class;
  protected $streamingStragglerDataType = '';

  /**
   * Batch straggler identification and debugging information.
   *
   * @param StragglerInfo $batchStraggler
   */
  public function setBatchStraggler(StragglerInfo $batchStraggler)
  {
    $this->batchStraggler = $batchStraggler;
  }
  /**
   * @return StragglerInfo
   */
  public function getBatchStraggler()
  {
    return $this->batchStraggler;
  }
  /**
   * Streaming straggler identification and debugging information.
   *
   * @param StreamingStragglerInfo $streamingStraggler
   */
  public function setStreamingStraggler(StreamingStragglerInfo $streamingStraggler)
  {
    $this->streamingStraggler = $streamingStraggler;
  }
  /**
   * @return StreamingStragglerInfo
   */
  public function getStreamingStraggler()
  {
    return $this->streamingStraggler;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Straggler::class, 'Google_Service_Dataflow_Straggler');
