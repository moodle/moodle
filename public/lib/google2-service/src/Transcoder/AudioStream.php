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

class AudioStream extends \Google\Collection
{
  protected $collection_key = 'mapping';
  /**
   * Required. Audio bitrate in bits per second. Must be between 1 and
   * 10,000,000.
   *
   * @var int
   */
  public $bitrateBps;
  /**
   * Number of audio channels. Must be between 1 and 6. The default is 2.
   *
   * @var int
   */
  public $channelCount;
  /**
   * A list of channel names specifying layout of the audio channels. This only
   * affects the metadata embedded in the container headers, if supported by the
   * specified format. The default is `["fl", "fr"]`. Supported channel names: -
   * `fl` - Front left channel - `fr` - Front right channel - `sl` - Side left
   * channel - `sr` - Side right channel - `fc` - Front center channel - `lfe` -
   * Low frequency
   *
   * @var string[]
   */
  public $channelLayout;
  /**
   * The codec for this audio stream. The default is `aac`. Supported audio
   * codecs: - `aac` - `aac-he` - `aac-he-v2` - `mp3` - `ac3` - `eac3` -
   * `vorbis`
   *
   * @var string
   */
  public $codec;
  /**
   * The name for this particular audio stream that will be added to the
   * HLS/DASH manifest. Not supported in MP4 files.
   *
   * @var string
   */
  public $displayName;
  /**
   * The BCP-47 language code, such as `en-US` or `sr-Latn`. For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier. Not
   * supported in MP4 files.
   *
   * @var string
   */
  public $languageCode;
  protected $mappingType = AudioMapping::class;
  protected $mappingDataType = 'array';
  /**
   * The audio sample rate in Hertz. The default is 48000 Hertz.
   *
   * @var int
   */
  public $sampleRateHertz;

  /**
   * Required. Audio bitrate in bits per second. Must be between 1 and
   * 10,000,000.
   *
   * @param int $bitrateBps
   */
  public function setBitrateBps($bitrateBps)
  {
    $this->bitrateBps = $bitrateBps;
  }
  /**
   * @return int
   */
  public function getBitrateBps()
  {
    return $this->bitrateBps;
  }
  /**
   * Number of audio channels. Must be between 1 and 6. The default is 2.
   *
   * @param int $channelCount
   */
  public function setChannelCount($channelCount)
  {
    $this->channelCount = $channelCount;
  }
  /**
   * @return int
   */
  public function getChannelCount()
  {
    return $this->channelCount;
  }
  /**
   * A list of channel names specifying layout of the audio channels. This only
   * affects the metadata embedded in the container headers, if supported by the
   * specified format. The default is `["fl", "fr"]`. Supported channel names: -
   * `fl` - Front left channel - `fr` - Front right channel - `sl` - Side left
   * channel - `sr` - Side right channel - `fc` - Front center channel - `lfe` -
   * Low frequency
   *
   * @param string[] $channelLayout
   */
  public function setChannelLayout($channelLayout)
  {
    $this->channelLayout = $channelLayout;
  }
  /**
   * @return string[]
   */
  public function getChannelLayout()
  {
    return $this->channelLayout;
  }
  /**
   * The codec for this audio stream. The default is `aac`. Supported audio
   * codecs: - `aac` - `aac-he` - `aac-he-v2` - `mp3` - `ac3` - `eac3` -
   * `vorbis`
   *
   * @param string $codec
   */
  public function setCodec($codec)
  {
    $this->codec = $codec;
  }
  /**
   * @return string
   */
  public function getCodec()
  {
    return $this->codec;
  }
  /**
   * The name for this particular audio stream that will be added to the
   * HLS/DASH manifest. Not supported in MP4 files.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The BCP-47 language code, such as `en-US` or `sr-Latn`. For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier. Not
   * supported in MP4 files.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The mapping for the JobConfig.edit_list atoms with audio EditAtom.inputs.
   *
   * @param AudioMapping[] $mapping
   */
  public function setMapping($mapping)
  {
    $this->mapping = $mapping;
  }
  /**
   * @return AudioMapping[]
   */
  public function getMapping()
  {
    return $this->mapping;
  }
  /**
   * The audio sample rate in Hertz. The default is 48000 Hertz.
   *
   * @param int $sampleRateHertz
   */
  public function setSampleRateHertz($sampleRateHertz)
  {
    $this->sampleRateHertz = $sampleRateHertz;
  }
  /**
   * @return int
   */
  public function getSampleRateHertz()
  {
    return $this->sampleRateHertz;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AudioStream::class, 'Google_Service_Transcoder_AudioStream');
