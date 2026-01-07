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

namespace Google\Service\YouTube;

class VideoFileDetails extends \Google\Collection
{
  /**
   * Known video file (e.g., an MP4 file).
   */
  public const FILE_TYPE_video = 'video';
  /**
   * Audio only file (e.g., an MP3 file).
   */
  public const FILE_TYPE_audio = 'audio';
  /**
   * Image file (e.g., a JPEG image).
   */
  public const FILE_TYPE_image = 'image';
  /**
   * Archive file (e.g., a ZIP archive).
   */
  public const FILE_TYPE_archive = 'archive';
  /**
   * Document or text file (e.g., MS Word document).
   */
  public const FILE_TYPE_document = 'document';
  /**
   * Movie project file (e.g., Microsoft Windows Movie Maker project).
   */
  public const FILE_TYPE_project = 'project';
  /**
   * Other non-video file type.
   */
  public const FILE_TYPE_other = 'other';
  protected $collection_key = 'videoStreams';
  protected $audioStreamsType = VideoFileDetailsAudioStream::class;
  protected $audioStreamsDataType = 'array';
  /**
   * The uploaded video file's combined (video and audio) bitrate in bits per
   * second.
   *
   * @var string
   */
  public $bitrateBps;
  /**
   * The uploaded video file's container format.
   *
   * @var string
   */
  public $container;
  /**
   * The date and time when the uploaded video file was created. The value is
   * specified in ISO 8601 format. Currently, the following ISO 8601 formats are
   * supported: - Date only: YYYY-MM-DD - Naive time: YYYY-MM-DDTHH:MM:SS - Time
   * with timezone: YYYY-MM-DDTHH:MM:SS+HH:MM
   *
   * @var string
   */
  public $creationTime;
  /**
   * The length of the uploaded video in milliseconds.
   *
   * @var string
   */
  public $durationMs;
  /**
   * The uploaded file's name. This field is present whether a video file or
   * another type of file was uploaded.
   *
   * @var string
   */
  public $fileName;
  /**
   * The uploaded file's size in bytes. This field is present whether a video
   * file or another type of file was uploaded.
   *
   * @var string
   */
  public $fileSize;
  /**
   * The uploaded file's type as detected by YouTube's video processing engine.
   * Currently, YouTube only processes video files, but this field is present
   * whether a video file or another type of file was uploaded.
   *
   * @var string
   */
  public $fileType;
  protected $videoStreamsType = VideoFileDetailsVideoStream::class;
  protected $videoStreamsDataType = 'array';

  /**
   * A list of audio streams contained in the uploaded video file. Each item in
   * the list contains detailed metadata about an audio stream.
   *
   * @param VideoFileDetailsAudioStream[] $audioStreams
   */
  public function setAudioStreams($audioStreams)
  {
    $this->audioStreams = $audioStreams;
  }
  /**
   * @return VideoFileDetailsAudioStream[]
   */
  public function getAudioStreams()
  {
    return $this->audioStreams;
  }
  /**
   * The uploaded video file's combined (video and audio) bitrate in bits per
   * second.
   *
   * @param string $bitrateBps
   */
  public function setBitrateBps($bitrateBps)
  {
    $this->bitrateBps = $bitrateBps;
  }
  /**
   * @return string
   */
  public function getBitrateBps()
  {
    return $this->bitrateBps;
  }
  /**
   * The uploaded video file's container format.
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
   * The date and time when the uploaded video file was created. The value is
   * specified in ISO 8601 format. Currently, the following ISO 8601 formats are
   * supported: - Date only: YYYY-MM-DD - Naive time: YYYY-MM-DDTHH:MM:SS - Time
   * with timezone: YYYY-MM-DDTHH:MM:SS+HH:MM
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * The length of the uploaded video in milliseconds.
   *
   * @param string $durationMs
   */
  public function setDurationMs($durationMs)
  {
    $this->durationMs = $durationMs;
  }
  /**
   * @return string
   */
  public function getDurationMs()
  {
    return $this->durationMs;
  }
  /**
   * The uploaded file's name. This field is present whether a video file or
   * another type of file was uploaded.
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
   * The uploaded file's size in bytes. This field is present whether a video
   * file or another type of file was uploaded.
   *
   * @param string $fileSize
   */
  public function setFileSize($fileSize)
  {
    $this->fileSize = $fileSize;
  }
  /**
   * @return string
   */
  public function getFileSize()
  {
    return $this->fileSize;
  }
  /**
   * The uploaded file's type as detected by YouTube's video processing engine.
   * Currently, YouTube only processes video files, but this field is present
   * whether a video file or another type of file was uploaded.
   *
   * Accepted values: video, audio, image, archive, document, project, other
   *
   * @param self::FILE_TYPE_* $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return self::FILE_TYPE_*
   */
  public function getFileType()
  {
    return $this->fileType;
  }
  /**
   * A list of video streams contained in the uploaded video file. Each item in
   * the list contains detailed metadata about a video stream.
   *
   * @param VideoFileDetailsVideoStream[] $videoStreams
   */
  public function setVideoStreams($videoStreams)
  {
    $this->videoStreams = $videoStreams;
  }
  /**
   * @return VideoFileDetailsVideoStream[]
   */
  public function getVideoStreams()
  {
    return $this->videoStreams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoFileDetails::class, 'Google_Service_YouTube_VideoFileDetails');
