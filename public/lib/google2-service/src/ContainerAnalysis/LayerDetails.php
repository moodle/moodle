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

namespace Google\Service\ContainerAnalysis;

class LayerDetails extends \Google\Collection
{
  protected $collection_key = 'baseImages';
  protected $baseImagesType = BaseImage::class;
  protected $baseImagesDataType = 'array';
  /**
   * The layer chain ID (sha256 hash) of the layer in the container image.
   * https://github.com/opencontainers/image-spec/blob/main/config.md#layer-
   * chainid
   *
   * @var string
   */
  public $chainId;
  /**
   * The layer build command that was used to build the layer. This may not be
   * found in all layers depending on how the container image is built.
   *
   * @var string
   */
  public $command;
  /**
   * The diff ID (typically a sha256 hash) of the layer in the container image.
   *
   * @var string
   */
  public $diffId;
  /**
   * The index of the layer in the container image.
   *
   * @var int
   */
  public $index;

  /**
   * The base images the layer is found within.
   *
   * @param BaseImage[] $baseImages
   */
  public function setBaseImages($baseImages)
  {
    $this->baseImages = $baseImages;
  }
  /**
   * @return BaseImage[]
   */
  public function getBaseImages()
  {
    return $this->baseImages;
  }
  /**
   * The layer chain ID (sha256 hash) of the layer in the container image.
   * https://github.com/opencontainers/image-spec/blob/main/config.md#layer-
   * chainid
   *
   * @param string $chainId
   */
  public function setChainId($chainId)
  {
    $this->chainId = $chainId;
  }
  /**
   * @return string
   */
  public function getChainId()
  {
    return $this->chainId;
  }
  /**
   * The layer build command that was used to build the layer. This may not be
   * found in all layers depending on how the container image is built.
   *
   * @param string $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * The diff ID (typically a sha256 hash) of the layer in the container image.
   *
   * @param string $diffId
   */
  public function setDiffId($diffId)
  {
    $this->diffId = $diffId;
  }
  /**
   * @return string
   */
  public function getDiffId()
  {
    return $this->diffId;
  }
  /**
   * The index of the layer in the container image.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LayerDetails::class, 'Google_Service_ContainerAnalysis_LayerDetails');
