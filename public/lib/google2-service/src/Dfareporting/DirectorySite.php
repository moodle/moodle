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

class DirectorySite extends \Google\Collection
{
  protected $collection_key = 'interstitialTagFormats';
  /**
   * ID of this directory site. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Tag types for regular placements. Acceptable values are: - "STANDARD" -
   * "IFRAME_JAVASCRIPT_INPAGE" - "INTERNAL_REDIRECT_INPAGE" -
   * "JAVASCRIPT_INPAGE"
   *
   * @var string[]
   */
  public $inpageTagFormats;
  /**
   * Tag types for interstitial placements. Acceptable values are: -
   * "IFRAME_JAVASCRIPT_INTERSTITIAL" - "INTERNAL_REDIRECT_INTERSTITIAL" -
   * "JAVASCRIPT_INTERSTITIAL"
   *
   * @var string[]
   */
  public $interstitialTagFormats;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#directorySite".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this directory site.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Default publisher specification ID of video placements under
   * this directory site. Possible values are: * `1`, Hulu * `2`, NBC * `3`, CBS
   * * `4`, CBS Desktop * `5`, Discovery * `6`, VEVO HD * `7`, VEVO Vertical *
   * `8`, Fox * `9`, CW Network * `10`, Disney * `11`, IGN * `12`, NFL.com *
   * `13`, Turner Broadcasting * `14`, Tubi on Fox * `15`, Hearst Corporation *
   * `16`, Twitch Desktop * `17`, ABC * `18`, Univision * `19`, MLB.com * `20`,
   * MLB.com Mobile * `21`, MLB.com OTT * `22`, Polsat * `23`, TVN * `24`,
   * Mediaset * `25`, Antena 3 * `26`, Mediamond * `27`, Sky Italia * `28`, Tubi
   * on CBS * `29`, Spotify * `30`, Paramount * `31`, Max
   *
   * @var string
   */
  public $publisherSpecificationId;
  protected $settingsType = DirectorySiteSettings::class;
  protected $settingsDataType = '';
  /**
   * URL of this directory site.
   *
   * @var string
   */
  public $url;

  /**
   * ID of this directory site. This is a read-only, auto-generated field.
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
   * Dimension value for the ID of this directory site. This is a read-only,
   * auto-generated field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Tag types for regular placements. Acceptable values are: - "STANDARD" -
   * "IFRAME_JAVASCRIPT_INPAGE" - "INTERNAL_REDIRECT_INPAGE" -
   * "JAVASCRIPT_INPAGE"
   *
   * @param string[] $inpageTagFormats
   */
  public function setInpageTagFormats($inpageTagFormats)
  {
    $this->inpageTagFormats = $inpageTagFormats;
  }
  /**
   * @return string[]
   */
  public function getInpageTagFormats()
  {
    return $this->inpageTagFormats;
  }
  /**
   * Tag types for interstitial placements. Acceptable values are: -
   * "IFRAME_JAVASCRIPT_INTERSTITIAL" - "INTERNAL_REDIRECT_INTERSTITIAL" -
   * "JAVASCRIPT_INTERSTITIAL"
   *
   * @param string[] $interstitialTagFormats
   */
  public function setInterstitialTagFormats($interstitialTagFormats)
  {
    $this->interstitialTagFormats = $interstitialTagFormats;
  }
  /**
   * @return string[]
   */
  public function getInterstitialTagFormats()
  {
    return $this->interstitialTagFormats;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#directorySite".
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
   * Name of this directory site.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Default publisher specification ID of video placements under
   * this directory site. Possible values are: * `1`, Hulu * `2`, NBC * `3`, CBS
   * * `4`, CBS Desktop * `5`, Discovery * `6`, VEVO HD * `7`, VEVO Vertical *
   * `8`, Fox * `9`, CW Network * `10`, Disney * `11`, IGN * `12`, NFL.com *
   * `13`, Turner Broadcasting * `14`, Tubi on Fox * `15`, Hearst Corporation *
   * `16`, Twitch Desktop * `17`, ABC * `18`, Univision * `19`, MLB.com * `20`,
   * MLB.com Mobile * `21`, MLB.com OTT * `22`, Polsat * `23`, TVN * `24`,
   * Mediaset * `25`, Antena 3 * `26`, Mediamond * `27`, Sky Italia * `28`, Tubi
   * on CBS * `29`, Spotify * `30`, Paramount * `31`, Max
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
   * Directory site settings.
   *
   * @param DirectorySiteSettings $settings
   */
  public function setSettings(DirectorySiteSettings $settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return DirectorySiteSettings
   */
  public function getSettings()
  {
    return $this->settings;
  }
  /**
   * URL of this directory site.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectorySite::class, 'Google_Service_Dfareporting_DirectorySite');
