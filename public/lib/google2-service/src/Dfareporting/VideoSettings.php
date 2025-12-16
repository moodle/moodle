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

namespace Google\Service\Dfareporting;

class VideoSettings extends \Google\Model
{
  public const ORIENTATION_ANY = 'ANY';
  public const ORIENTATION_LANDSCAPE = 'LANDSCAPE';
  public const ORIENTATION_PORTRAIT = 'PORTRAIT';
  protected $companionSettingsType = CompanionSetting::class;
  protected $companionSettingsDataType = '';
  /**
   * Duration of a video placement in seconds.
   *
   * @var int
   */
  public $durationSeconds;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#videoSettings".
   *
   * @var string
   */
  public $kind;
  /**
   * Whether OBA icons are enabled for this placement.
   *
   * @var bool
   */
  public $obaEnabled;
  protected $obaSettingsType = ObaIcon::class;
  protected $obaSettingsDataType = '';
  /**
   * Orientation of a video placement. If this value is set, placement will
   * return assets matching the specified orientation.
   *
   * @var string
   */
  public $orientation;
  /**
   * Publisher specification ID of a video placement. Possible values are: *
   * `1`, Hulu * `2`, NBC * `3`, CBS * `4`, CBS Desktop * `5`, Discovery * `6`,
   * VEVO HD * `7`, VEVO Vertical * `8`, Fox * `9`, CW Network * `10`, Disney *
   * `11`, IGN * `12`, NFL.com * `13`, Turner Broadcasting * `14`, Tubi on Fox *
   * `15`, Hearst Corporation * `16`, Twitch Desktop * `17`, ABC * `18`,
   * Univision * `19`, MLB.com * `20`, MLB.com Mobile * `21`, MLB.com OTT *
   * `22`, Polsat * `23`, TVN * `24`, Mediaset * `25`, Antena 3 * `26`,
   * Mediamond * `27`, Sky Italia * `28`, Tubi on CBS * `29`, Spotify * `30`,
   * Paramount * `31`, Max
   *
   * @var string
   */
  public $publisherSpecificationId;
  protected $skippableSettingsType = SkippableSetting::class;
  protected $skippableSettingsDataType = '';
  protected $transcodeSettingsType = TranscodeSetting::class;
  protected $transcodeSettingsDataType = '';

  /**
   * Settings for the companion creatives of video creatives served to this
   * placement.
   *
   * @param CompanionSetting $companionSettings
   */
  public function setCompanionSettings(CompanionSetting $companionSettings)
  {
    $this->companionSettings = $companionSettings;
  }
  /**
   * @return CompanionSetting
   */
  public function getCompanionSettings()
  {
    return $this->companionSettings;
  }
  /**
   * Duration of a video placement in seconds.
   *
   * @param int $durationSeconds
   */
  public function setDurationSeconds($durationSeconds)
  {
    $this->durationSeconds = $durationSeconds;
  }
  /**
   * @return int
   */
  public function getDurationSeconds()
  {
    return $this->durationSeconds;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#videoSettings".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Whether OBA icons are enabled for this placement.
   *
   * @param bool $obaEnabled
   */
  public function setObaEnabled($obaEnabled)
  {
    $this->obaEnabled = $obaEnabled;
  }
  /**
   * @return bool
   */
  public function getObaEnabled()
  {
    return $this->obaEnabled;
  }
  /**
   * Settings for the OBA icon of video creatives served to this placement. If
   * this object is provided, the creative-level OBA settings will be
   * overridden.
   *
   * @param ObaIcon $obaSettings
   */
  public function setObaSettings(ObaIcon $obaSettings)
  {
    $this->obaSettings = $obaSettings;
  }
  /**
   * @return ObaIcon
   */
  public function getObaSettings()
  {
    return $this->obaSettings;
  }
  /**
   * Orientation of a video placement. If this value is set, placement will
   * return assets matching the specified orientation.
   *
   * Accepted values: ANY, LANDSCAPE, PORTRAIT
   *
   * @param self::ORIENTATION_* $orientation
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;
  }
  /**
   * @return self::ORIENTATION_*
   */
  public function getOrientation()
  {
    return $this->orientation;
  }
  /**
   * Publisher specification ID of a video placement. Possible values are: *
   * `1`, Hulu * `2`, NBC * `3`, CBS * `4`, CBS Desktop * `5`, Discovery * `6`,
   * VEVO HD * `7`, VEVO Vertical * `8`, Fox * `9`, CW Network * `10`, Disney *
   * `11`, IGN * `12`, NFL.com * `13`, Turner Broadcasting * `14`, Tubi on Fox *
   * `15`, Hearst Corporation * `16`, Twitch Desktop * `17`, ABC * `18`,
   * Univision * `19`, MLB.com * `20`, MLB.com Mobile * `21`, MLB.com OTT *
   * `22`, Polsat * `23`, TVN * `24`, Mediaset * `25`, Antena 3 * `26`,
   * Mediamond * `27`, Sky Italia * `28`, Tubi on CBS * `29`, Spotify * `30`,
   * Paramount * `31`, Max
   *
   * @param string $publisherSpecificationId
   */
  public function setPublisherSpecificationId($publisherSpecificationId)
  {
    $this->publisherSpecificationId = $publisherSpecificationId;
  }
  /**
   * @return string
   */
  public function getPublisherSpecificationId()
  {
    return $this->publisherSpecificationId;
  }
  /**
   * Settings for the skippability of video creatives served to this placement.
   * If this object is provided, the creative-level skippable settings will be
   * overridden.
   *
   * @param SkippableSetting $skippableSettings
   */
  public function setSkippableSettings(SkippableSetting $skippableSettings)
  {
    $this->skippableSettings = $skippableSettings;
  }
  /**
   * @return SkippableSetting
   */
  public function getSkippableSettings()
  {
    return $this->skippableSettings;
  }
  /**
   * Settings for the transcodes of video creatives served to this placement. If
   * this object is provided, the creative-level transcode settings will be
   * overridden.
   *
   * @param TranscodeSetting $transcodeSettings
   */
  public function setTranscodeSettings(TranscodeSetting $transcodeSettings)
  {
    $this->transcodeSettings = $transcodeSettings;
  }
  /**
   * @return TranscodeSetting
   */
  public function getTranscodeSettings()
  {
    return $this->transcodeSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoSettings::class, 'Google_Service_Dfareporting_VideoSettings');
