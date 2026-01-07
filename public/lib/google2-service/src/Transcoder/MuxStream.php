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

class MuxStream extends \Google\Collection
{
  protected $collection_key = 'elementaryStreams';
  /**
   * The container format. The default is `mp4` Supported streaming formats: -
   * `ts` - `fmp4`- the corresponding file extension is `.m4s` Supported
   * standalone file formats: - `mp4` - `mp3` - `ogg` - `vtt` See also:
   * [Supported input and output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats)
   *
   * @var string
   */
  public $container;
  /**
   * List of ElementaryStream.key values multiplexed in this stream.
   *
   * @var string[]
   */
  public $elementaryStreams;
  /**
   * Identifier of the encryption configuration to use. If omitted, output will
   * be unencrypted.
   *
   * @var string
   */
  public $encryptionId;
  /**
   * The name of the generated file. The default is MuxStream.key with the
   * extension suffix corresponding to the MuxStream.container. Individual
   * segments also have an incremental 10-digit zero-padded suffix starting from
   * 0 before the extension, such as `mux_stream0000000123.ts`.
   *
   * @var string
   */
  public $fileName;
  protected $fmp4Type = Fmp4Config::class;
  protected $fmp4DataType = '';
  /**
   * A unique key for this multiplexed stream.
   *
   * @var string
   */
  public $key;
  protected $segmentSettingsType = SegmentSettings::class;
  protected $segmentSettingsDataType = '';

  /**
   * The container format. The default is `mp4` Supported streaming formats: -
   * `ts` - `fmp4`- the corresponding file extension is `.m4s` Supported
   * standalone file formats: - `mp4` - `mp3` - `ogg` - `vtt` See also:
   * [Supported input and output
   * formats](https://cloud.google.com/transcoder/docs/concepts/supported-input-
   * and-output-formats)
   *
   * @param string $container
   */
  public function setContainer($container)
  {
    $this->container = $container;
  }
  /**
   * @return string
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * List of ElementaryStream.key values multiplexed in this stream.
   *
   * @param string[] $elementaryStreams
   */
  public function setElementaryStreams($elementaryStreams)
  {
    $this->elementaryStreams = $elementaryStreams;
  }
  /**
   * @return string[]
   */
  public function getElementaryStreams()
  {
    return $this->elementaryStreams;
  }
  /**
   * Identifier of the encryption configuration to use. If omitted, output will
   * be unencrypted.
   *
   * @param string $encryptionId
   */
  public function setEncryptionId($encryptionId)
  {
    $this->encryptionId = $encryptionId;
  }
  /**
   * @return string
   */
  public function getEncryptionId()
  {
    return $this->encryptionId;
  }
  /**
   * The name of the generated file. The default is MuxStream.key with the
   * extension suffix corresponding to the MuxStream.container. Individual
   * segments also have an incremental 10-digit zero-padded suffix starting from
   * 0 before the extension, such as `mux_stream0000000123.ts`.
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
   * Optional. `fmp4` container configuration.
   *
   * @param Fmp4Config $fmp4
   */
  public function setFmp4(Fmp4Config $fmp4)
  {
    $this->fmp4 = $fmp4;
  }
  /**
   * @return Fmp4Config
   */
  public function getFmp4()
  {
    return $this->fmp4;
  }
  /**
   * A unique key for this multiplexed stream.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Segment settings for `ts`, `fmp4` and `vtt`.
   *
   * @param SegmentSettings $segmentSettings
   */
  public function setSegmentSettings(SegmentSettings $segmentSettings)
  {
    $this->segmentSettings = $segmentSettings;
  }
  /**
   * @return SegmentSettings
   */
  public function getSegmentSettings()
  {
    return $this->segmentSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MuxStream::class, 'Google_Service_Transcoder_MuxStream');
