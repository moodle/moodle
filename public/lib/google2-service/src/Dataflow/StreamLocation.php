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

class StreamLocation extends \Google\Model
{
  protected $customSourceLocationType = CustomSourceLocation::class;
  protected $customSourceLocationDataType = '';
  protected $pubsubLocationType = PubsubLocation::class;
  protected $pubsubLocationDataType = '';
  protected $sideInputLocationType = StreamingSideInputLocation::class;
  protected $sideInputLocationDataType = '';
  protected $streamingStageLocationType = StreamingStageLocation::class;
  protected $streamingStageLocationDataType = '';

  /**
   * The stream is a custom source.
   *
   * @param CustomSourceLocation $customSourceLocation
   */
  public function setCustomSourceLocation(CustomSourceLocation $customSourceLocation)
  {
    $this->customSourceLocation = $customSourceLocation;
  }
  /**
   * @return CustomSourceLocation
   */
  public function getCustomSourceLocation()
  {
    return $this->customSourceLocation;
  }
  /**
   * The stream is a pubsub stream.
   *
   * @param PubsubLocation $pubsubLocation
   */
  public function setPubsubLocation(PubsubLocation $pubsubLocation)
  {
    $this->pubsubLocation = $pubsubLocation;
  }
  /**
   * @return PubsubLocation
   */
  public function getPubsubLocation()
  {
    return $this->pubsubLocation;
  }
  /**
   * The stream is a streaming side input.
   *
   * @param StreamingSideInputLocation $sideInputLocation
   */
  public function setSideInputLocation(StreamingSideInputLocation $sideInputLocation)
  {
    $this->sideInputLocation = $sideInputLocation;
  }
  /**
   * @return StreamingSideInputLocation
   */
  public function getSideInputLocation()
  {
    return $this->sideInputLocation;
  }
  /**
   * The stream is part of another computation within the current streaming
   * Dataflow job.
   *
   * @param StreamingStageLocation $streamingStageLocation
   */
  public function setStreamingStageLocation(StreamingStageLocation $streamingStageLocation)
  {
    $this->streamingStageLocation = $streamingStageLocation;
  }
  /**
   * @return StreamingStageLocation
   */
  public function getStreamingStageLocation()
  {
    return $this->streamingStageLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamLocation::class, 'Google_Service_Dataflow_StreamLocation');
