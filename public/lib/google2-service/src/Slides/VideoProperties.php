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

namespace Google\Service\Slides;

class VideoProperties extends \Google\Model
{
  /**
   * Whether to enable video autoplay when the page is displayed in present
   * mode. Defaults to false.
   *
   * @var bool
   */
  public $autoPlay;
  /**
   * The time at which to end playback, measured in seconds from the beginning
   * of the video. If set, the end time should be after the start time. If not
   * set or if you set this to a value that exceeds the video's length, the
   * video will be played until its end.
   *
   * @var string
   */
  public $end;
  /**
   * Whether to mute the audio during video playback. Defaults to false.
   *
   * @var bool
   */
  public $mute;
  protected $outlineType = Outline::class;
  protected $outlineDataType = '';
  /**
   * The time at which to start playback, measured in seconds from the beginning
   * of the video. If set, the start time should be before the end time. If you
   * set this to a value that exceeds the video's length in seconds, the video
   * will be played from the last second. If not set, the video will be played
   * from the beginning.
   *
   * @var string
   */
  public $start;

  /**
   * Whether to enable video autoplay when the page is displayed in present
   * mode. Defaults to false.
   *
   * @param bool $autoPlay
   */
  public function setAutoPlay($autoPlay)
  {
    $this->autoPlay = $autoPlay;
  }
  /**
   * @return bool
   */
  public function getAutoPlay()
  {
    return $this->autoPlay;
  }
  /**
   * The time at which to end playback, measured in seconds from the beginning
   * of the video. If set, the end time should be after the start time. If not
   * set or if you set this to a value that exceeds the video's length, the
   * video will be played until its end.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Whether to mute the audio during video playback. Defaults to false.
   *
   * @param bool $mute
   */
  public function setMute($mute)
  {
    $this->mute = $mute;
  }
  /**
   * @return bool
   */
  public function getMute()
  {
    return $this->mute;
  }
  /**
   * The outline of the video. The default outline matches the defaults for new
   * videos created in the Slides editor.
   *
   * @param Outline $outline
   */
  public function setOutline(Outline $outline)
  {
    $this->outline = $outline;
  }
  /**
   * @return Outline
   */
  public function getOutline()
  {
    return $this->outline;
  }
  /**
   * The time at which to start playback, measured in seconds from the beginning
   * of the video. If set, the start time should be before the end time. If you
   * set this to a value that exceeds the video's length in seconds, the video
   * will be played from the last second. If not set, the video will be played
   * from the beginning.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoProperties::class, 'Google_Service_Slides_VideoProperties');
