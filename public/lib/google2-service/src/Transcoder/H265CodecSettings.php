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

class H265CodecSettings extends \Google\Model
{
  /**
   * Unspecified frame rate conversion strategy.
   */
  public const FRAME_RATE_CONVERSION_STRATEGY_FRAME_RATE_CONVERSION_STRATEGY_UNSPECIFIED = 'FRAME_RATE_CONVERSION_STRATEGY_UNSPECIFIED';
  /**
   * Selectively retain frames to reduce the output frame rate. Every _n_ th
   * frame is kept, where `n = ceil(input frame rate / target frame rate)`. When
   * _n_ = 1 (that is, the target frame rate is greater than the input frame
   * rate), the output frame rate matches the input frame rate. When _n_ > 1,
   * frames are dropped and the output frame rate is equal to `(input frame rate
   * / n)`. For more information, see [Calculate frame
   * rate](https://cloud.google.com/transcoder/docs/concepts/frame-rate).
   */
  public const FRAME_RATE_CONVERSION_STRATEGY_DOWNSAMPLE = 'DOWNSAMPLE';
  /**
   * Drop or duplicate frames to match the specified frame rate.
   */
  public const FRAME_RATE_CONVERSION_STRATEGY_DROP_DUPLICATE = 'DROP_DUPLICATE';
  /**
   * Specifies whether an open Group of Pictures (GOP) structure should be
   * allowed or not. The default is `false`.
   *
   * @var bool
   */
  public $allowOpenGop;
  /**
   * Specify the intensity of the adaptive quantizer (AQ). Must be between 0 and
   * 1, where 0 disables the quantizer and 1 maximizes the quantizer. A higher
   * value equals a lower bitrate but smoother image. The default is 0.
   *
   * @var 
   */
  public $aqStrength;
  /**
   * The number of consecutive B-frames. Must be greater than or equal to zero.
   * Must be less than H265CodecSettings.gop_frame_count if set. The default is
   * 0.
   *
   * @var int
   */
  public $bFrameCount;
  /**
   * Allow B-pyramid for reference frame selection. This may not be supported on
   * all decoders. The default is `false`.
   *
   * @var bool
   */
  public $bPyramid;
  /**
   * Required. The video bitrate in bits per second. The minimum value is 1,000.
   * The maximum value is 800,000,000.
   *
   * @var int
   */
  public $bitrateBps;
  /**
   * Target CRF level. Must be between 10 and 36, where 10 is the highest
   * quality and 36 is the most efficient compression. The default is 21.
   *
   * @var int
   */
  public $crfLevel;
  /**
   * Use two-pass encoding strategy to achieve better video quality.
   * H265CodecSettings.rate_control_mode must be `vbr`. The default is `false`.
   *
   * @var bool
   */
  public $enableTwoPass;
  /**
   * Required. The target video frame rate in frames per second (FPS). Must be
   * less than or equal to 120.
   *
   * @var 
   */
  public $frameRate;
  /**
   * Optional. Frame rate conversion strategy for desired frame rate. The
   * default is `DOWNSAMPLE`.
   *
   * @var string
   */
  public $frameRateConversionStrategy;
  /**
   * Select the GOP size based on the specified duration. The default is `3s`.
   * Note that `gopDuration` must be less than or equal to
   * [`segmentDuration`](#SegmentSettings), and
   * [`segmentDuration`](#SegmentSettings) must be divisible by `gopDuration`.
   *
   * @var string
   */
  public $gopDuration;
  /**
   * Select the GOP size based on the specified frame count. Must be greater
   * than zero.
   *
   * @var int
   */
  public $gopFrameCount;
  protected $hdr10Type = H265ColorFormatHDR10::class;
  protected $hdr10DataType = '';
  /**
   * The height of the video in pixels. Must be an even integer. When not
   * specified, the height is adjusted to match the specified width and input
   * aspect ratio. If both are omitted, the input height is used. For portrait
   * videos that contain horizontal ASR and rotation metadata, provide the
   * height, in pixels, per the horizontal ASR. The API calculates the width per
   * the horizontal ASR. The API detects any rotation metadata and swaps the
   * requested height and width for the output.
   *
   * @var int
   */
  public $heightPixels;
  protected $hlgType = H265ColorFormatHLG::class;
  protected $hlgDataType = '';
  /**
   * Pixel format to use. The default is `yuv420p`. Supported pixel formats: -
   * `yuv420p` pixel format - `yuv422p` pixel format - `yuv444p` pixel format -
   * `yuv420p10` 10-bit HDR pixel format - `yuv422p10` 10-bit HDR pixel format -
   * `yuv444p10` 10-bit HDR pixel format - `yuv420p12` 12-bit HDR pixel format -
   * `yuv422p12` 12-bit HDR pixel format - `yuv444p12` 12-bit HDR pixel format
   *
   * @var string
   */
  public $pixelFormat;
  /**
   * Enforces the specified codec preset. The default is `veryfast`. The
   * available options are [FFmpeg-
   * compatible](https://trac.ffmpeg.org/wiki/Encode/H.265). Note that certain
   * values for this field may cause the transcoder to override other fields you
   * set in the `H265CodecSettings` message.
   *
   * @var string
   */
  public $preset;
  /**
   * Enforces the specified codec profile. The following profiles are supported:
   * * 8-bit profiles * `main` (default) * `main-intra` * `mainstillpicture` *
   * 10-bit profiles * `main10` (default) * `main10-intra` * `main422-10` *
   * `main422-10-intra` * `main444-10` * `main444-10-intra` * 12-bit profiles *
   * `main12` (default) * `main12-intra` * `main422-12` * `main422-12-intra` *
   * `main444-12` * `main444-12-intra` The available options are [FFmpeg-
   * compatible](https://x265.readthedocs.io/). Note that certain values for
   * this field may cause the transcoder to override other fields you set in the
   * `H265CodecSettings` message.
   *
   * @var string
   */
  public $profile;
  /**
   * Specify the mode. The default is `vbr`. Supported rate control modes: -
   * `vbr` - variable bitrate - `crf` - constant rate factor
   *
   * @var string
   */
  public $rateControlMode;
  protected $sdrType = H265ColorFormatSDR::class;
  protected $sdrDataType = '';
  /**
   * Enforces the specified codec tune. The available options are [FFmpeg-
   * compatible](https://trac.ffmpeg.org/wiki/Encode/H.265). Note that certain
   * values for this field may cause the transcoder to override other fields you
   * set in the `H265CodecSettings` message.
   *
   * @var string
   */
  public $tune;
  /**
   * Initial fullness of the Video Buffering Verifier (VBV) buffer in bits. Must
   * be greater than zero. The default is equal to 90% of
   * H265CodecSettings.vbv_size_bits.
   *
   * @var int
   */
  public $vbvFullnessBits;
  /**
   * Size of the Video Buffering Verifier (VBV) buffer in bits. Must be greater
   * than zero. The default is equal to `VideoStream.bitrate_bps`.
   *
   * @var int
   */
  public $vbvSizeBits;
  /**
   * The width of the video in pixels. Must be an even integer. When not
   * specified, the width is adjusted to match the specified height and input
   * aspect ratio. If both are omitted, the input width is used. For portrait
   * videos that contain horizontal ASR and rotation metadata, provide the
   * width, in pixels, per the horizontal ASR. The API calculates the height per
   * the horizontal ASR. The API detects any rotation metadata and swaps the
   * requested height and width for the output.
   *
   * @var int
   */
  public $widthPixels;

  /**
   * Specifies whether an open Group of Pictures (GOP) structure should be
   * allowed or not. The default is `false`.
   *
   * @param bool $allowOpenGop
   */
  public function setAllowOpenGop($allowOpenGop)
  {
    $this->allowOpenGop = $allowOpenGop;
  }
  /**
   * @return bool
   */
  public function getAllowOpenGop()
  {
    return $this->allowOpenGop;
  }
  public function setAqStrength($aqStrength)
  {
    $this->aqStrength = $aqStrength;
  }
  public function getAqStrength()
  {
    return $this->aqStrength;
  }
  /**
   * The number of consecutive B-frames. Must be greater than or equal to zero.
   * Must be less than H265CodecSettings.gop_frame_count if set. The default is
   * 0.
   *
   * @param int $bFrameCount
   */
  public function setBFrameCount($bFrameCount)
  {
    $this->bFrameCount = $bFrameCount;
  }
  /**
   * @return int
   */
  public function getBFrameCount()
  {
    return $this->bFrameCount;
  }
  /**
   * Allow B-pyramid for reference frame selection. This may not be supported on
   * all decoders. The default is `false`.
   *
   * @param bool $bPyramid
   */
  public function setBPyramid($bPyramid)
  {
    $this->bPyramid = $bPyramid;
  }
  /**
   * @return bool
   */
  public function getBPyramid()
  {
    return $this->bPyramid;
  }
  /**
   * Required. The video bitrate in bits per second. The minimum value is 1,000.
   * The maximum value is 800,000,000.
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
   * Target CRF level. Must be between 10 and 36, where 10 is the highest
   * quality and 36 is the most efficient compression. The default is 21.
   *
   * @param int $crfLevel
   */
  public function setCrfLevel($crfLevel)
  {
    $this->crfLevel = $crfLevel;
  }
  /**
   * @return int
   */
  public function getCrfLevel()
  {
    return $this->crfLevel;
  }
  /**
   * Use two-pass encoding strategy to achieve better video quality.
   * H265CodecSettings.rate_control_mode must be `vbr`. The default is `false`.
   *
   * @param bool $enableTwoPass
   */
  public function setEnableTwoPass($enableTwoPass)
  {
    $this->enableTwoPass = $enableTwoPass;
  }
  /**
   * @return bool
   */
  public function getEnableTwoPass()
  {
    return $this->enableTwoPass;
  }
  public function setFrameRate($frameRate)
  {
    $this->frameRate = $frameRate;
  }
  public function getFrameRate()
  {
    return $this->frameRate;
  }
  /**
   * Optional. Frame rate conversion strategy for desired frame rate. The
   * default is `DOWNSAMPLE`.
   *
   * Accepted values: FRAME_RATE_CONVERSION_STRATEGY_UNSPECIFIED, DOWNSAMPLE,
   * DROP_DUPLICATE
   *
   * @param self::FRAME_RATE_CONVERSION_STRATEGY_* $frameRateConversionStrategy
   */
  public function setFrameRateConversionStrategy($frameRateConversionStrategy)
  {
    $this->frameRateConversionStrategy = $frameRateConversionStrategy;
  }
  /**
   * @return self::FRAME_RATE_CONVERSION_STRATEGY_*
   */
  public function getFrameRateConversionStrategy()
  {
    return $this->frameRateConversionStrategy;
  }
  /**
   * Select the GOP size based on the specified duration. The default is `3s`.
   * Note that `gopDuration` must be less than or equal to
   * [`segmentDuration`](#SegmentSettings), and
   * [`segmentDuration`](#SegmentSettings) must be divisible by `gopDuration`.
   *
   * @param string $gopDuration
   */
  public function setGopDuration($gopDuration)
  {
    $this->gopDuration = $gopDuration;
  }
  /**
   * @return string
   */
  public function getGopDuration()
  {
    return $this->gopDuration;
  }
  /**
   * Select the GOP size based on the specified frame count. Must be greater
   * than zero.
   *
   * @param int $gopFrameCount
   */
  public function setGopFrameCount($gopFrameCount)
  {
    $this->gopFrameCount = $gopFrameCount;
  }
  /**
   * @return int
   */
  public function getGopFrameCount()
  {
    return $this->gopFrameCount;
  }
  /**
   * Optional. HDR10 color format setting for H265.
   *
   * @param H265ColorFormatHDR10 $hdr10
   */
  public function setHdr10(H265ColorFormatHDR10 $hdr10)
  {
    $this->hdr10 = $hdr10;
  }
  /**
   * @return H265ColorFormatHDR10
   */
  public function getHdr10()
  {
    return $this->hdr10;
  }
  /**
   * The height of the video in pixels. Must be an even integer. When not
   * specified, the height is adjusted to match the specified width and input
   * aspect ratio. If both are omitted, the input height is used. For portrait
   * videos that contain horizontal ASR and rotation metadata, provide the
   * height, in pixels, per the horizontal ASR. The API calculates the width per
   * the horizontal ASR. The API detects any rotation metadata and swaps the
   * requested height and width for the output.
   *
   * @param int $heightPixels
   */
  public function setHeightPixels($heightPixels)
  {
    $this->heightPixels = $heightPixels;
  }
  /**
   * @return int
   */
  public function getHeightPixels()
  {
    return $this->heightPixels;
  }
  /**
   * Optional. HLG color format setting for H265.
   *
   * @param H265ColorFormatHLG $hlg
   */
  public function setHlg(H265ColorFormatHLG $hlg)
  {
    $this->hlg = $hlg;
  }
  /**
   * @return H265ColorFormatHLG
   */
  public function getHlg()
  {
    return $this->hlg;
  }
  /**
   * Pixel format to use. The default is `yuv420p`. Supported pixel formats: -
   * `yuv420p` pixel format - `yuv422p` pixel format - `yuv444p` pixel format -
   * `yuv420p10` 10-bit HDR pixel format - `yuv422p10` 10-bit HDR pixel format -
   * `yuv444p10` 10-bit HDR pixel format - `yuv420p12` 12-bit HDR pixel format -
   * `yuv422p12` 12-bit HDR pixel format - `yuv444p12` 12-bit HDR pixel format
   *
   * @param string $pixelFormat
   */
  public function setPixelFormat($pixelFormat)
  {
    $this->pixelFormat = $pixelFormat;
  }
  /**
   * @return string
   */
  public function getPixelFormat()
  {
    return $this->pixelFormat;
  }
  /**
   * Enforces the specified codec preset. The default is `veryfast`. The
   * available options are [FFmpeg-
   * compatible](https://trac.ffmpeg.org/wiki/Encode/H.265). Note that certain
   * values for this field may cause the transcoder to override other fields you
   * set in the `H265CodecSettings` message.
   *
   * @param string $preset
   */
  public function setPreset($preset)
  {
    $this->preset = $preset;
  }
  /**
   * @return string
   */
  public function getPreset()
  {
    return $this->preset;
  }
  /**
   * Enforces the specified codec profile. The following profiles are supported:
   * * 8-bit profiles * `main` (default) * `main-intra` * `mainstillpicture` *
   * 10-bit profiles * `main10` (default) * `main10-intra` * `main422-10` *
   * `main422-10-intra` * `main444-10` * `main444-10-intra` * 12-bit profiles *
   * `main12` (default) * `main12-intra` * `main422-12` * `main422-12-intra` *
   * `main444-12` * `main444-12-intra` The available options are [FFmpeg-
   * compatible](https://x265.readthedocs.io/). Note that certain values for
   * this field may cause the transcoder to override other fields you set in the
   * `H265CodecSettings` message.
   *
   * @param string $profile
   */
  public function setProfile($profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return string
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Specify the mode. The default is `vbr`. Supported rate control modes: -
   * `vbr` - variable bitrate - `crf` - constant rate factor
   *
   * @param string $rateControlMode
   */
  public function setRateControlMode($rateControlMode)
  {
    $this->rateControlMode = $rateControlMode;
  }
  /**
   * @return string
   */
  public function getRateControlMode()
  {
    return $this->rateControlMode;
  }
  /**
   * Optional. SDR color format setting for H265.
   *
   * @param H265ColorFormatSDR $sdr
   */
  public function setSdr(H265ColorFormatSDR $sdr)
  {
    $this->sdr = $sdr;
  }
  /**
   * @return H265ColorFormatSDR
   */
  public function getSdr()
  {
    return $this->sdr;
  }
  /**
   * Enforces the specified codec tune. The available options are [FFmpeg-
   * compatible](https://trac.ffmpeg.org/wiki/Encode/H.265). Note that certain
   * values for this field may cause the transcoder to override other fields you
   * set in the `H265CodecSettings` message.
   *
   * @param string $tune
   */
  public function setTune($tune)
  {
    $this->tune = $tune;
  }
  /**
   * @return string
   */
  public function getTune()
  {
    return $this->tune;
  }
  /**
   * Initial fullness of the Video Buffering Verifier (VBV) buffer in bits. Must
   * be greater than zero. The default is equal to 90% of
   * H265CodecSettings.vbv_size_bits.
   *
   * @param int $vbvFullnessBits
   */
  public function setVbvFullnessBits($vbvFullnessBits)
  {
    $this->vbvFullnessBits = $vbvFullnessBits;
  }
  /**
   * @return int
   */
  public function getVbvFullnessBits()
  {
    return $this->vbvFullnessBits;
  }
  /**
   * Size of the Video Buffering Verifier (VBV) buffer in bits. Must be greater
   * than zero. The default is equal to `VideoStream.bitrate_bps`.
   *
   * @param int $vbvSizeBits
   */
  public function setVbvSizeBits($vbvSizeBits)
  {
    $this->vbvSizeBits = $vbvSizeBits;
  }
  /**
   * @return int
   */
  public function getVbvSizeBits()
  {
    return $this->vbvSizeBits;
  }
  /**
   * The width of the video in pixels. Must be an even integer. When not
   * specified, the width is adjusted to match the specified height and input
   * aspect ratio. If both are omitted, the input width is used. For portrait
   * videos that contain horizontal ASR and rotation metadata, provide the
   * width, in pixels, per the horizontal ASR. The API calculates the height per
   * the horizontal ASR. The API detects any rotation metadata and swaps the
   * requested height and width for the output.
   *
   * @param int $widthPixels
   */
  public function setWidthPixels($widthPixels)
  {
    $this->widthPixels = $widthPixels;
  }
  /**
   * @return int
   */
  public function getWidthPixels()
  {
    return $this->widthPixels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(H265CodecSettings::class, 'Google_Service_Transcoder_H265CodecSettings');
