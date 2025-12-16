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

class SpriteSheet extends \Google\Model
{
  /**
   * The maximum number of sprites per row in a sprite sheet. The default is 0,
   * which indicates no maximum limit.
   *
   * @var int
   */
  public $columnCount;
  /**
   * End time in seconds, relative to the output file timeline. When
   * `end_time_offset` is not specified, the sprites are generated until the end
   * of the output file.
   *
   * @var string
   */
  public $endTimeOffset;
  /**
   * Required. File name prefix for the generated sprite sheets. Each sprite
   * sheet has an incremental 10-digit zero-padded suffix starting from 0 before
   * the extension, such as `sprite_sheet0000000123.jpeg`.
   *
   * @var string
   */
  public $filePrefix;
  /**
   * Format type. The default is `jpeg`. Supported formats: - `jpeg`
   *
   * @var string
   */
  public $format;
  /**
   * Starting from `0s`, create sprites at regular intervals. Specify the
   * interval value in seconds.
   *
   * @var string
   */
  public $interval;
  /**
   * The quality of the generated sprite sheet. Enter a value between 1 and 100,
   * where 1 is the lowest quality and 100 is the highest quality. The default
   * is 100. A high quality value corresponds to a low image data compression
   * ratio.
   *
   * @var int
   */
  public $quality;
  /**
   * The maximum number of rows per sprite sheet. When the sprite sheet is full,
   * a new sprite sheet is created. The default is 0, which indicates no maximum
   * limit.
   *
   * @var int
   */
  public $rowCount;
  /**
   * Required. The height of sprite in pixels. Must be an even integer. To
   * preserve the source aspect ratio, set the SpriteSheet.sprite_height_pixels
   * field or the SpriteSheet.sprite_width_pixels field, but not both (the API
   * will automatically calculate the missing field). For portrait videos that
   * contain horizontal ASR and rotation metadata, provide the height, in
   * pixels, per the horizontal ASR. The API calculates the width per the
   * horizontal ASR. The API detects any rotation metadata and swaps the
   * requested height and width for the output.
   *
   * @var int
   */
  public $spriteHeightPixels;
  /**
   * Required. The width of sprite in pixels. Must be an even integer. To
   * preserve the source aspect ratio, set the SpriteSheet.sprite_width_pixels
   * field or the SpriteSheet.sprite_height_pixels field, but not both (the API
   * will automatically calculate the missing field). For portrait videos that
   * contain horizontal ASR and rotation metadata, provide the width, in pixels,
   * per the horizontal ASR. The API calculates the height per the horizontal
   * ASR. The API detects any rotation metadata and swaps the requested height
   * and width for the output.
   *
   * @var int
   */
  public $spriteWidthPixels;
  /**
   * Start time in seconds, relative to the output file timeline. Determines the
   * first sprite to pick. The default is `0s`.
   *
   * @var string
   */
  public $startTimeOffset;
  /**
   * Total number of sprites. Create the specified number of sprites distributed
   * evenly across the timeline of the output media. The default is 100.
   *
   * @var int
   */
  public $totalCount;

  /**
   * The maximum number of sprites per row in a sprite sheet. The default is 0,
   * which indicates no maximum limit.
   *
   * @param int $columnCount
   */
  public function setColumnCount($columnCount)
  {
    $this->columnCount = $columnCount;
  }
  /**
   * @return int
   */
  public function getColumnCount()
  {
    return $this->columnCount;
  }
  /**
   * End time in seconds, relative to the output file timeline. When
   * `end_time_offset` is not specified, the sprites are generated until the end
   * of the output file.
   *
   * @param string $endTimeOffset
   */
  public function setEndTimeOffset($endTimeOffset)
  {
    $this->endTimeOffset = $endTimeOffset;
  }
  /**
   * @return string
   */
  public function getEndTimeOffset()
  {
    return $this->endTimeOffset;
  }
  /**
   * Required. File name prefix for the generated sprite sheets. Each sprite
   * sheet has an incremental 10-digit zero-padded suffix starting from 0 before
   * the extension, such as `sprite_sheet0000000123.jpeg`.
   *
   * @param string $filePrefix
   */
  public function setFilePrefix($filePrefix)
  {
    $this->filePrefix = $filePrefix;
  }
  /**
   * @return string
   */
  public function getFilePrefix()
  {
    return $this->filePrefix;
  }
  /**
   * Format type. The default is `jpeg`. Supported formats: - `jpeg`
   *
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Starting from `0s`, create sprites at regular intervals. Specify the
   * interval value in seconds.
   *
   * @param string $interval
   */
  public function setInterval($interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return string
   */
  public function getInterval()
  {
    return $this->interval;
  }
  /**
   * The quality of the generated sprite sheet. Enter a value between 1 and 100,
   * where 1 is the lowest quality and 100 is the highest quality. The default
   * is 100. A high quality value corresponds to a low image data compression
   * ratio.
   *
   * @param int $quality
   */
  public function setQuality($quality)
  {
    $this->quality = $quality;
  }
  /**
   * @return int
   */
  public function getQuality()
  {
    return $this->quality;
  }
  /**
   * The maximum number of rows per sprite sheet. When the sprite sheet is full,
   * a new sprite sheet is created. The default is 0, which indicates no maximum
   * limit.
   *
   * @param int $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return int
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * Required. The height of sprite in pixels. Must be an even integer. To
   * preserve the source aspect ratio, set the SpriteSheet.sprite_height_pixels
   * field or the SpriteSheet.sprite_width_pixels field, but not both (the API
   * will automatically calculate the missing field). For portrait videos that
   * contain horizontal ASR and rotation metadata, provide the height, in
   * pixels, per the horizontal ASR. The API calculates the width per the
   * horizontal ASR. The API detects any rotation metadata and swaps the
   * requested height and width for the output.
   *
   * @param int $spriteHeightPixels
   */
  public function setSpriteHeightPixels($spriteHeightPixels)
  {
    $this->spriteHeightPixels = $spriteHeightPixels;
  }
  /**
   * @return int
   */
  public function getSpriteHeightPixels()
  {
    return $this->spriteHeightPixels;
  }
  /**
   * Required. The width of sprite in pixels. Must be an even integer. To
   * preserve the source aspect ratio, set the SpriteSheet.sprite_width_pixels
   * field or the SpriteSheet.sprite_height_pixels field, but not both (the API
   * will automatically calculate the missing field). For portrait videos that
   * contain horizontal ASR and rotation metadata, provide the width, in pixels,
   * per the horizontal ASR. The API calculates the height per the horizontal
   * ASR. The API detects any rotation metadata and swaps the requested height
   * and width for the output.
   *
   * @param int $spriteWidthPixels
   */
  public function setSpriteWidthPixels($spriteWidthPixels)
  {
    $this->spriteWidthPixels = $spriteWidthPixels;
  }
  /**
   * @return int
   */
  public function getSpriteWidthPixels()
  {
    return $this->spriteWidthPixels;
  }
  /**
   * Start time in seconds, relative to the output file timeline. Determines the
   * first sprite to pick. The default is `0s`.
   *
   * @param string $startTimeOffset
   */
  public function setStartTimeOffset($startTimeOffset)
  {
    $this->startTimeOffset = $startTimeOffset;
  }
  /**
   * @return string
   */
  public function getStartTimeOffset()
  {
    return $this->startTimeOffset;
  }
  /**
   * Total number of sprites. Create the specified number of sprites distributed
   * evenly across the timeline of the output media. The default is 100.
   *
   * @param int $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return int
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpriteSheet::class, 'Google_Service_Transcoder_SpriteSheet');
