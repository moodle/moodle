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

namespace Google\Service\Transcoder;

class Manifest extends \Google\Collection
{
  /**
   * The manifest type is not specified.
   */
  public const TYPE_MANIFEST_TYPE_UNSPECIFIED = 'MANIFEST_TYPE_UNSPECIFIED';
  /**
   * Create an HLS manifest. The corresponding file extension is `.m3u8`.
   */
  public const TYPE_HLS = 'HLS';
  /**
   * Create an MPEG-DASH manifest. The corresponding file extension is `.mpd`.
   */
  public const TYPE_DASH = 'DASH';
  protected $collection_key = 'muxStreams';
  protected $dashType = DashConfig::class;
  protected $dashDataType = '';
  /**
   * The name of the generated file. The default is `manifest` with the
   * extension suffix corresponding to the Manifest.type.
   *
   * @var string
   */
  public $fileName;
  /**
   * Required. List of user supplied MuxStream.key values that should appear in
   * this manifest. When Manifest.type is `HLS`, a media manifest with name
   * MuxStream.key and `.m3u8` extension is generated for each element in this
   * list.
   *
   * @var string[]
   */
  public $muxStreams;
  /**
   * Required. Type of the manifest.
   *
   * @var string
   */
  public $type;

  /**
   * `DASH` manifest configuration.
   *
   * @param DashConfig $dash
   */
  public function setDash(DashConfig $dash)
  {
    $this->dash = $dash;
  }
  /**
   * @return DashConfig
   */
  public function getDash()
  {
    return $this->dash;
  }
  /**
   * The name of the generated file. The default is `manifest` with the
   * extension suffix corresponding to the Manifest.type.
   *
   * @param string $fileName
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  /**
   * @return string
   */
  public function getFileName()
  {
    return $this->fileName;
  }
  /**
   * Required. List of user supplied MuxStream.key values that should appear in
   * this manifest. When Manifest.type is `HLS`, a media manifest with name
   * MuxStream.key and `.m3u8` extension is generated for each element in this
   * list.
   *
   * @param string[] $muxStreams
   */
  public function setMuxStreams($muxStreams)
  {
    $this->muxStreams = $muxStreams;
  }
  /**
   * @return string[]
   */
  public function getMuxStreams()
  {
    return $this->muxStreams;
  }
  /**
   * Required. Type of the manifest.
   *
   * Accepted values: MANIFEST_TYPE_UNSPECIFIED, HLS, DASH
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Manifest::class, 'Google_Service_Transcoder_Manifest');
