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

namespace Google\Service\StreetViewPublish;

class PhotoSequence extends \Google\Collection
{
  /**
   * The failure reason is unspecified, this is the default value.
   */
  public const FAILURE_REASON_PROCESSING_FAILURE_REASON_UNSPECIFIED = 'PROCESSING_FAILURE_REASON_UNSPECIFIED';
  /**
   * Video frame's resolution is too small.
   */
  public const FAILURE_REASON_LOW_RESOLUTION = 'LOW_RESOLUTION';
  /**
   * This video has been uploaded before.
   */
  public const FAILURE_REASON_DUPLICATE = 'DUPLICATE';
  /**
   * Too few GPS points.
   */
  public const FAILURE_REASON_INSUFFICIENT_GPS = 'INSUFFICIENT_GPS';
  /**
   * No overlap between the time frame of GPS track and the time frame of video.
   */
  public const FAILURE_REASON_NO_OVERLAP_GPS = 'NO_OVERLAP_GPS';
  /**
   * GPS is invalid (e.x. all GPS points are at (0,0))
   */
  public const FAILURE_REASON_INVALID_GPS = 'INVALID_GPS';
  /**
   * The sequence of photos could not be accurately located in the world.
   */
  public const FAILURE_REASON_FAILED_TO_REFINE_POSITIONS = 'FAILED_TO_REFINE_POSITIONS';
  /**
   * The sequence was taken down for policy reasons.
   */
  public const FAILURE_REASON_TAKEDOWN = 'TAKEDOWN';
  /**
   * The video file was corrupt or could not be decoded.
   */
  public const FAILURE_REASON_CORRUPT_VIDEO = 'CORRUPT_VIDEO';
  /**
   * A permanent failure in the underlying system occurred.
   */
  public const FAILURE_REASON_INTERNAL = 'INTERNAL';
  /**
   * The video format is invalid or unsupported.
   */
  public const FAILURE_REASON_INVALID_VIDEO_FORMAT = 'INVALID_VIDEO_FORMAT';
  /**
   * Invalid image aspect ratio found.
   */
  public const FAILURE_REASON_INVALID_VIDEO_DIMENSIONS = 'INVALID_VIDEO_DIMENSIONS';
  /**
   * Invalid capture time. Timestamps were from the future.
   */
  public const FAILURE_REASON_INVALID_CAPTURE_TIME = 'INVALID_CAPTURE_TIME';
  /**
   * GPS data contains a gap greater than 5 seconds in duration.
   */
  public const FAILURE_REASON_GPS_DATA_GAP = 'GPS_DATA_GAP';
  /**
   * GPS data is too erratic to be processed.
   */
  public const FAILURE_REASON_JUMPY_GPS = 'JUMPY_GPS';
  /**
   * IMU (Accelerometer, Gyroscope, etc.) data are not valid. They may be
   * missing required fields (x, y, z or time), may not be formatted correctly,
   * or any other issue that prevents our systems from parsing it.
   */
  public const FAILURE_REASON_INVALID_IMU = 'INVALID_IMU';
  /**
   * Too few IMU points.
   */
  public const FAILURE_REASON_INSUFFICIENT_IMU = 'INSUFFICIENT_IMU';
  /**
   * Insufficient overlap in the time frame between GPS, IMU, and other time
   * series data.
   */
  public const FAILURE_REASON_INSUFFICIENT_OVERLAP_TIME_SERIES = 'INSUFFICIENT_OVERLAP_TIME_SERIES';
  /**
   * IMU (Accelerometer, Gyroscope, etc.) data contain gaps greater than 0.1
   * seconds in duration.
   */
  public const FAILURE_REASON_IMU_DATA_GAP = 'IMU_DATA_GAP';
  /**
   * The camera is not supported.
   */
  public const FAILURE_REASON_UNSUPPORTED_CAMERA = 'UNSUPPORTED_CAMERA';
  /**
   * Some frames were indoors, which is unsupported.
   */
  public const FAILURE_REASON_NOT_OUTDOORS = 'NOT_OUTDOORS';
  /**
   * Not enough video frames.
   */
  public const FAILURE_REASON_INSUFFICIENT_VIDEO_FRAMES = 'INSUFFICIENT_VIDEO_FRAMES';
  /**
   * Not enough moving data.
   */
  public const FAILURE_REASON_INSUFFICIENT_MOVEMENT = 'INSUFFICIENT_MOVEMENT';
  /**
   * Mast is down.
   */
  public const FAILURE_REASON_MAST_DOWN = 'MAST_DOWN';
  /**
   * Camera is covered.
   */
  public const FAILURE_REASON_CAMERA_COVERED = 'CAMERA_COVERED';
  /**
   * GPS in raw_gps_timeline takes precedence if it exists.
   */
  public const GPS_SOURCE_PHOTO_SEQUENCE = 'PHOTO_SEQUENCE';
  /**
   * GPS in Camera Motion Metadata Track (CAMM) takes precedence if it exists.
   */
  public const GPS_SOURCE_CAMERA_MOTION_METADATA_TRACK = 'CAMERA_MOTION_METADATA_TRACK';
  /**
   * The state is unspecified, this is the default value.
   */
  public const PROCESSING_STATE_PROCESSING_STATE_UNSPECIFIED = 'PROCESSING_STATE_UNSPECIFIED';
  /**
   * The sequence has not yet started processing.
   */
  public const PROCESSING_STATE_PENDING = 'PENDING';
  /**
   * The sequence is currently in processing.
   */
  public const PROCESSING_STATE_PROCESSING = 'PROCESSING';
  /**
   * The sequence has finished processing including refining position.
   */
  public const PROCESSING_STATE_PROCESSED = 'PROCESSED';
  /**
   * The sequence failed processing. See FailureReason for more details.
   */
  public const PROCESSING_STATE_FAILED = 'FAILED';
  protected $collection_key = 'rawGpsTimeline';
  /**
   * Optional. Absolute time when the photo sequence starts to be captured. If
   * the photo sequence is a video, this is the start time of the video. If this
   * field is populated in input, it overrides the capture time in the video or
   * XDM file.
   *
   * @var string
   */
  public $captureTimeOverride;
  /**
   * Output only. The computed distance of the photo sequence in meters.
   *
   * @var 
   */
  public $distanceMeters;
  protected $failureDetailsType = ProcessingFailureDetails::class;
  protected $failureDetailsDataType = '';
  /**
   * Output only. If this sequence has processing_state = FAILED, this will
   * contain the reason why it failed. If the processing_state is any other
   * value, this field will be unset.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Output only. The filename of the upload. Does not include the directory
   * path. Only available if the sequence was uploaded on a platform that
   * provides the filename.
   *
   * @var string
   */
  public $filename;
  /**
   * Input only. If both raw_gps_timeline and the Camera Motion Metadata Track
   * (CAMM) contain GPS measurements, indicate which takes precedence.
   *
   * @var string
   */
  public $gpsSource;
  /**
   * Output only. Unique identifier for the photo sequence. This also acts as a
   * long running operation ID if uploading is performed asynchronously.
   *
   * @var string
   */
  public $id;
  protected $imuType = Imu::class;
  protected $imuDataType = '';
  protected $photosType = Photo::class;
  protected $photosDataType = 'array';
  /**
   * Output only. The processing state of this sequence.
   *
   * @var string
   */
  public $processingState;
  protected $rawGpsTimelineType = Pose::class;
  protected $rawGpsTimelineDataType = 'array';
  protected $sequenceBoundsType = LatLngBounds::class;
  protected $sequenceBoundsDataType = '';
  protected $uploadReferenceType = UploadRef::class;
  protected $uploadReferenceDataType = '';
  /**
   * Output only. The time this photo sequence was created in uSV Store service.
   *
   * @var string
   */
  public $uploadTime;
  /**
   * Output only. The total number of views that all the published images in
   * this PhotoSequence have received.
   *
   * @var string
   */
  public $viewCount;

  /**
   * Optional. Absolute time when the photo sequence starts to be captured. If
   * the photo sequence is a video, this is the start time of the video. If this
   * field is populated in input, it overrides the capture time in the video or
   * XDM file.
   *
   * @param string $captureTimeOverride
   */
  public function setCaptureTimeOverride($captureTimeOverride)
  {
    $this->captureTimeOverride = $captureTimeOverride;
  }
  /**
   * @return string
   */
  public function getCaptureTimeOverride()
  {
    return $this->captureTimeOverride;
  }
  public function setDistanceMeters($distanceMeters)
  {
    $this->distanceMeters = $distanceMeters;
  }
  public function getDistanceMeters()
  {
    return $this->distanceMeters;
  }
  /**
   * Output only. If this sequence has `failure_reason` set, this may contain
   * additional details about the failure.
   *
   * @param ProcessingFailureDetails $failureDetails
   */
  public function setFailureDetails(ProcessingFailureDetails $failureDetails)
  {
    $this->failureDetails = $failureDetails;
  }
  /**
   * @return ProcessingFailureDetails
   */
  public function getFailureDetails()
  {
    return $this->failureDetails;
  }
  /**
   * Output only. If this sequence has processing_state = FAILED, this will
   * contain the reason why it failed. If the processing_state is any other
   * value, this field will be unset.
   *
   * Accepted values: PROCESSING_FAILURE_REASON_UNSPECIFIED, LOW_RESOLUTION,
   * DUPLICATE, INSUFFICIENT_GPS, NO_OVERLAP_GPS, INVALID_GPS,
   * FAILED_TO_REFINE_POSITIONS, TAKEDOWN, CORRUPT_VIDEO, INTERNAL,
   * INVALID_VIDEO_FORMAT, INVALID_VIDEO_DIMENSIONS, INVALID_CAPTURE_TIME,
   * GPS_DATA_GAP, JUMPY_GPS, INVALID_IMU, INSUFFICIENT_IMU,
   * INSUFFICIENT_OVERLAP_TIME_SERIES, IMU_DATA_GAP, UNSUPPORTED_CAMERA,
   * NOT_OUTDOORS, INSUFFICIENT_VIDEO_FRAMES, INSUFFICIENT_MOVEMENT, MAST_DOWN,
   * CAMERA_COVERED
   *
   * @param self::FAILURE_REASON_* $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return self::FAILURE_REASON_*
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Output only. The filename of the upload. Does not include the directory
   * path. Only available if the sequence was uploaded on a platform that
   * provides the filename.
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * Input only. If both raw_gps_timeline and the Camera Motion Metadata Track
   * (CAMM) contain GPS measurements, indicate which takes precedence.
   *
   * Accepted values: PHOTO_SEQUENCE, CAMERA_MOTION_METADATA_TRACK
   *
   * @param self::GPS_SOURCE_* $gpsSource
   */
  public function setGpsSource($gpsSource)
  {
    $this->gpsSource = $gpsSource;
  }
  /**
   * @return self::GPS_SOURCE_*
   */
  public function getGpsSource()
  {
    return $this->gpsSource;
  }
  /**
   * Output only. Unique identifier for the photo sequence. This also acts as a
   * long running operation ID if uploading is performed asynchronously.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Input only. Three axis IMU data for the collection. If this data is too
   * large to put in the request, then it should be put in the CAMM track for
   * the video. This data always takes precedence over the equivalent CAMM data,
   * if it exists.
   *
   * @param Imu $imu
   */
  public function setImu(Imu $imu)
  {
    $this->imu = $imu;
  }
  /**
   * @return Imu
   */
  public function getImu()
  {
    return $this->imu;
  }
  /**
   * Output only. Photos with increasing timestamps.
   *
   * @param Photo[] $photos
   */
  public function setPhotos($photos)
  {
    $this->photos = $photos;
  }
  /**
   * @return Photo[]
   */
  public function getPhotos()
  {
    return $this->photos;
  }
  /**
   * Output only. The processing state of this sequence.
   *
   * Accepted values: PROCESSING_STATE_UNSPECIFIED, PENDING, PROCESSING,
   * PROCESSED, FAILED
   *
   * @param self::PROCESSING_STATE_* $processingState
   */
  public function setProcessingState($processingState)
  {
    $this->processingState = $processingState;
  }
  /**
   * @return self::PROCESSING_STATE_*
   */
  public function getProcessingState()
  {
    return $this->processingState;
  }
  /**
   * Input only. Raw GPS measurements with increasing timestamps from the device
   * that aren't time synced with each photo. These raw measurements will be
   * used to infer the pose of each frame. Required in input when InputType is
   * VIDEO and raw GPS measurements are not in Camera Motion Metadata Track
   * (CAMM). User can indicate which takes precedence using gps_source if raw
   * GPS measurements are provided in both raw_gps_timeline and Camera Motion
   * Metadata Track (CAMM).
   *
   * @param Pose[] $rawGpsTimeline
   */
  public function setRawGpsTimeline($rawGpsTimeline)
  {
    $this->rawGpsTimeline = $rawGpsTimeline;
  }
  /**
   * @return Pose[]
   */
  public function getRawGpsTimeline()
  {
    return $this->rawGpsTimeline;
  }
  /**
   * Output only. A rectangular box that encapsulates every image in this photo
   * sequence.
   *
   * @param LatLngBounds $sequenceBounds
   */
  public function setSequenceBounds(LatLngBounds $sequenceBounds)
  {
    $this->sequenceBounds = $sequenceBounds;
  }
  /**
   * @return LatLngBounds
   */
  public function getSequenceBounds()
  {
    return $this->sequenceBounds;
  }
  /**
   * Input only. Required when creating photo sequence. The resource name where
   * the bytes of the photo sequence (in the form of video) are uploaded.
   *
   * @param UploadRef $uploadReference
   */
  public function setUploadReference(UploadRef $uploadReference)
  {
    $this->uploadReference = $uploadReference;
  }
  /**
   * @return UploadRef
   */
  public function getUploadReference()
  {
    return $this->uploadReference;
  }
  /**
   * Output only. The time this photo sequence was created in uSV Store service.
   *
   * @param string $uploadTime
   */
  public function setUploadTime($uploadTime)
  {
    $this->uploadTime = $uploadTime;
  }
  /**
   * @return string
   */
  public function getUploadTime()
  {
    return $this->uploadTime;
  }
  /**
   * Output only. The total number of views that all the published images in
   * this PhotoSequence have received.
   *
   * @param string $viewCount
   */
  public function setViewCount($viewCount)
  {
    $this->viewCount = $viewCount;
  }
  /**
   * @return string
   */
  public function getViewCount()
  {
    return $this->viewCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PhotoSequence::class, 'Google_Service_StreetViewPublish_PhotoSequence');
