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

class IngestionInfo extends \Google\Model
{
  /**
   * The backup ingestion URL that you should use to stream video to YouTube.
   * You have the option of simultaneously streaming the content that you are
   * sending to the ingestionAddress to this URL.
   *
   * @var string
   */
  public $backupIngestionAddress;
  /**
   * The primary ingestion URL that you should use to stream video to YouTube.
   * You must stream video to this URL. Depending on which application or tool
   * you use to encode your video stream, you may need to enter the stream URL
   * and stream name separately or you may need to concatenate them in the
   * following format: *STREAM_URL/STREAM_NAME*
   *
   * @var string
   */
  public $ingestionAddress;
  /**
   * This ingestion url may be used instead of backupIngestionAddress in order
   * to stream via RTMPS. Not applicable to non-RTMP streams.
   *
   * @var string
   */
  public $rtmpsBackupIngestionAddress;
  /**
   * This ingestion url may be used instead of ingestionAddress in order to
   * stream via RTMPS. Not applicable to non-RTMP streams.
   *
   * @var string
   */
  public $rtmpsIngestionAddress;
  /**
   * The stream name that YouTube assigns to the video stream.
   *
   * @var string
   */
  public $streamName;

  /**
   * The backup ingestion URL that you should use to stream video to YouTube.
   * You have the option of simultaneously streaming the content that you are
   * sending to the ingestionAddress to this URL.
   *
   * @param string $backupIngestionAddress
   */
  public function setBackupIngestionAddress($backupIngestionAddress)
  {
    $this->backupIngestionAddress = $backupIngestionAddress;
  }
  /**
   * @return string
   */
  public function getBackupIngestionAddress()
  {
    return $this->backupIngestionAddress;
  }
  /**
   * The primary ingestion URL that you should use to stream video to YouTube.
   * You must stream video to this URL. Depending on which application or tool
   * you use to encode your video stream, you may need to enter the stream URL
   * and stream name separately or you may need to concatenate them in the
   * following format: *STREAM_URL/STREAM_NAME*
   *
   * @param string $ingestionAddress
   */
  public function setIngestionAddress($ingestionAddress)
  {
    $this->ingestionAddress = $ingestionAddress;
  }
  /**
   * @return string
   */
  public function getIngestionAddress()
  {
    return $this->ingestionAddress;
  }
  /**
   * This ingestion url may be used instead of backupIngestionAddress in order
   * to stream via RTMPS. Not applicable to non-RTMP streams.
   *
   * @param string $rtmpsBackupIngestionAddress
   */
  public function setRtmpsBackupIngestionAddress($rtmpsBackupIngestionAddress)
  {
    $this->rtmpsBackupIngestionAddress = $rtmpsBackupIngestionAddress;
  }
  /**
   * @return string
   */
  public function getRtmpsBackupIngestionAddress()
  {
    return $this->rtmpsBackupIngestionAddress;
  }
  /**
   * This ingestion url may be used instead of ingestionAddress in order to
   * stream via RTMPS. Not applicable to non-RTMP streams.
   *
   * @param string $rtmpsIngestionAddress
   */
  public function setRtmpsIngestionAddress($rtmpsIngestionAddress)
  {
    $this->rtmpsIngestionAddress = $rtmpsIngestionAddress;
  }
  /**
   * @return string
   */
  public function getRtmpsIngestionAddress()
  {
    return $this->rtmpsIngestionAddress;
  }
  /**
   * The stream name that YouTube assigns to the video stream.
   *
   * @param string $streamName
   */
  public function setStreamName($streamName)
  {
    $this->streamName = $streamName;
  }
  /**
   * @return string
   */
  public function getStreamName()
  {
    return $this->streamName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IngestionInfo::class, 'Google_Service_YouTube_IngestionInfo');
