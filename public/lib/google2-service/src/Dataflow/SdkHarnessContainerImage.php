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

class SdkHarnessContainerImage extends \Google\Collection
{
  protected $collection_key = 'capabilities';
  /**
   * The set of capabilities enumerated in the above Environment proto. See also
   * [beam_runner_api.proto](https://github.com/apache/beam/blob/master/model/pi
   * peline/src/main/proto/org/apache/beam/model/pipeline/v1/beam_runner_api.pro
   * to)
   *
   * @var string[]
   */
  public $capabilities;
  /**
   * A docker container image that resides in Google Container Registry.
   *
   * @var string
   */
  public $containerImage;
  /**
   * Environment ID for the Beam runner API proto Environment that corresponds
   * to the current SDK Harness.
   *
   * @var string
   */
  public $environmentId;
  /**
   * If true, recommends the Dataflow service to use only one core per SDK
   * container instance with this image. If false (or unset) recommends using
   * more than one core per SDK container instance with this image for
   * efficiency. Note that Dataflow service may choose to override this property
   * if needed.
   *
   * @var bool
   */
  public $useSingleCorePerContainer;

  /**
   * The set of capabilities enumerated in the above Environment proto. See also
   * [beam_runner_api.proto](https://github.com/apache/beam/blob/master/model/pi
   * peline/src/main/proto/org/apache/beam/model/pipeline/v1/beam_runner_api.pro
   * to)
   *
   * @param string[] $capabilities
   */
  public function setCapabilities($capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return string[]
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * A docker container image that resides in Google Container Registry.
   *
   * @param string $containerImage
   */
  public function setContainerImage($containerImage)
  {
    $this->containerImage = $containerImage;
  }
  /**
   * @return string
   */
  public function getContainerImage()
  {
    return $this->containerImage;
  }
  /**
   * Environment ID for the Beam runner API proto Environment that corresponds
   * to the current SDK Harness.
   *
   * @param string $environmentId
   */
  public function setEnvironmentId($environmentId)
  {
    $this->environmentId = $environmentId;
  }
  /**
   * @return string
   */
  public function getEnvironmentId()
  {
    return $this->environmentId;
  }
  /**
   * If true, recommends the Dataflow service to use only one core per SDK
   * container instance with this image. If false (or unset) recommends using
   * more than one core per SDK container instance with this image for
   * efficiency. Note that Dataflow service may choose to override this property
   * if needed.
   *
   * @param bool $useSingleCorePerContainer
   */
  public function setUseSingleCorePerContainer($useSingleCorePerContainer)
  {
    $this->useSingleCorePerContainer = $useSingleCorePerContainer;
  }
  /**
   * @return bool
   */
  public function getUseSingleCorePerContainer()
  {
    return $this->useSingleCorePerContainer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SdkHarnessContainerImage::class, 'Google_Service_Dataflow_SdkHarnessContainerImage');
