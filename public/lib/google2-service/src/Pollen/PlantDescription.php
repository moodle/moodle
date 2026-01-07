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

namespace Google\Service\Pollen;

class PlantDescription extends \Google\Model
{
  /**
   * Unspecified plant type.
   */
  public const TYPE_POLLEN_TYPE_UNSPECIFIED = 'POLLEN_TYPE_UNSPECIFIED';
  /**
   * Grass pollen type.
   */
  public const TYPE_GRASS = 'GRASS';
  /**
   * Tree pollen type.
   */
  public const TYPE_TREE = 'TREE';
  /**
   * Weed pollen type.
   */
  public const TYPE_WEED = 'WEED';
  /**
   * Textual description of pollen cross reaction plants. Example: Alder, Hazel,
   * Hornbeam, Beech, Willow, and Oak pollen.
   *
   * @var string
   */
  public $crossReaction;
  /**
   * A human readable representation of the plant family name. Example:
   * "Betulaceae (the Birch family)".
   *
   * @var string
   */
  public $family;
  /**
   * Link to the picture of the plant.
   *
   * @var string
   */
  public $picture;
  /**
   * Link to a closeup picture of the plant.
   *
   * @var string
   */
  public $pictureCloseup;
  /**
   * Textual list of explanations of seasons where the pollen is active.
   * Example: "Late winter, spring".
   *
   * @var string
   */
  public $season;
  /**
   * Textual description of the plants' colors of leaves, bark, flowers or seeds
   * that helps identify the plant.
   *
   * @var string
   */
  public $specialColors;
  /**
   * Textual description of the plants' shapes of leaves, bark, flowers or seeds
   * that helps identify the plant.
   *
   * @var string
   */
  public $specialShapes;
  /**
   * The plant's pollen type. For example: "GRASS". A list of all available
   * codes could be found here.
   *
   * @var string
   */
  public $type;

  /**
   * Textual description of pollen cross reaction plants. Example: Alder, Hazel,
   * Hornbeam, Beech, Willow, and Oak pollen.
   *
   * @param string $crossReaction
   */
  public function setCrossReaction($crossReaction)
  {
    $this->crossReaction = $crossReaction;
  }
  /**
   * @return string
   */
  public function getCrossReaction()
  {
    return $this->crossReaction;
  }
  /**
   * A human readable representation of the plant family name. Example:
   * "Betulaceae (the Birch family)".
   *
   * @param string $family
   */
  public function setFamily($family)
  {
    $this->family = $family;
  }
  /**
   * @return string
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * Link to the picture of the plant.
   *
   * @param string $picture
   */
  public function setPicture($picture)
  {
    $this->picture = $picture;
  }
  /**
   * @return string
   */
  public function getPicture()
  {
    return $this->picture;
  }
  /**
   * Link to a closeup picture of the plant.
   *
   * @param string $pictureCloseup
   */
  public function setPictureCloseup($pictureCloseup)
  {
    $this->pictureCloseup = $pictureCloseup;
  }
  /**
   * @return string
   */
  public function getPictureCloseup()
  {
    return $this->pictureCloseup;
  }
  /**
   * Textual list of explanations of seasons where the pollen is active.
   * Example: "Late winter, spring".
   *
   * @param string $season
   */
  public function setSeason($season)
  {
    $this->season = $season;
  }
  /**
   * @return string
   */
  public function getSeason()
  {
    return $this->season;
  }
  /**
   * Textual description of the plants' colors of leaves, bark, flowers or seeds
   * that helps identify the plant.
   *
   * @param string $specialColors
   */
  public function setSpecialColors($specialColors)
  {
    $this->specialColors = $specialColors;
  }
  /**
   * @return string
   */
  public function getSpecialColors()
  {
    return $this->specialColors;
  }
  /**
   * Textual description of the plants' shapes of leaves, bark, flowers or seeds
   * that helps identify the plant.
   *
   * @param string $specialShapes
   */
  public function setSpecialShapes($specialShapes)
  {
    $this->specialShapes = $specialShapes;
  }
  /**
   * @return string
   */
  public function getSpecialShapes()
  {
    return $this->specialShapes;
  }
  /**
   * The plant's pollen type. For example: "GRASS". A list of all available
   * codes could be found here.
   *
   * Accepted values: POLLEN_TYPE_UNSPECIFIED, GRASS, TREE, WEED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlantDescription::class, 'Google_Service_Pollen_PlantDescription');
