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

class JobConfig extends \Google\Collection
{
  protected $collection_key = 'spriteSheets';
  protected $adBreaksType = AdBreak::class;
  protected $adBreaksDataType = 'array';
  protected $editListType = EditAtom::class;
  protected $editListDataType = 'array';
  protected $elementaryStreamsType = ElementaryStream::class;
  protected $elementaryStreamsDataType = 'array';
  protected $encryptionsType = Encryption::class;
  protected $encryptionsDataType = 'array';
  protected $inputsType = Input::class;
  protected $inputsDataType = 'array';
  protected $manifestsType = Manifest::class;
  protected $manifestsDataType = 'array';
  protected $muxStreamsType = MuxStream::class;
  protected $muxStreamsDataType = 'array';
  protected $outputType = Output::class;
  protected $outputDataType = '';
  protected $overlaysType = Overlay::class;
  protected $overlaysDataType = 'array';
  protected $pubsubDestinationType = PubsubDestination::class;
  protected $pubsubDestinationDataType = '';
  protected $spriteSheetsType = SpriteSheet::class;
  protected $spriteSheetsDataType = 'array';

  /**
   * List of ad breaks. Specifies where to insert ad break tags in the output
   * manifests.
   *
   * @param AdBreak[] $adBreaks
   */
  public function setAdBreaks($adBreaks)
  {
    $this->adBreaks = $adBreaks;
  }
  /**
   * @return AdBreak[]
   */
  public function getAdBreaks()
  {
    return $this->adBreaks;
  }
  /**
   * List of edit atoms. Defines the ultimate timeline of the resulting file or
   * manifest.
   *
   * @param EditAtom[] $editList
   */
  public function setEditList($editList)
  {
    $this->editList = $editList;
  }
  /**
   * @return EditAtom[]
   */
  public function getEditList()
  {
    return $this->editList;
  }
  /**
   * List of elementary streams.
   *
   * @param ElementaryStream[] $elementaryStreams
   */
  public function setElementaryStreams($elementaryStreams)
  {
    $this->elementaryStreams = $elementaryStreams;
  }
  /**
   * @return ElementaryStream[]
   */
  public function getElementaryStreams()
  {
    return $this->elementaryStreams;
  }
  /**
   * List of encryption configurations for the content. Each configuration has
   * an ID. Specify this ID in the MuxStream.encryption_id field to indicate the
   * configuration to use for that `MuxStream` output.
   *
   * @param Encryption[] $encryptions
   */
  public function setEncryptions($encryptions)
  {
    $this->encryptions = $encryptions;
  }
  /**
   * @return Encryption[]
   */
  public function getEncryptions()
  {
    return $this->encryptions;
  }
  /**
   * List of input assets stored in Cloud Storage.
   *
   * @param Input[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return Input[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * List of output manifests.
   *
   * @param Manifest[] $manifests
   */
  public function setManifests($manifests)
  {
    $this->manifests = $manifests;
  }
  /**
   * @return Manifest[]
   */
  public function getManifests()
  {
    return $this->manifests;
  }
  /**
   * List of multiplexing settings for output streams.
   *
   * @param MuxStream[] $muxStreams
   */
  public function setMuxStreams($muxStreams)
  {
    $this->muxStreams = $muxStreams;
  }
  /**
   * @return MuxStream[]
   */
  public function getMuxStreams()
  {
    return $this->muxStreams;
  }
  /**
   * Output configuration.
   *
   * @param Output $output
   */
  public function setOutput(Output $output)
  {
    $this->output = $output;
  }
  /**
   * @return Output
   */
  public function getOutput()
  {
    return $this->output;
  }
  /**
   * List of overlays on the output video, in descending Z-order.
   *
   * @param Overlay[] $overlays
   */
  public function setOverlays($overlays)
  {
    $this->overlays = $overlays;
  }
  /**
   * @return Overlay[]
   */
  public function getOverlays()
  {
    return $this->overlays;
  }
  /**
   * Destination on Pub/Sub.
   *
   * @param PubsubDestination $pubsubDestination
   */
  public function setPubsubDestination(PubsubDestination $pubsubDestination)
  {
    $this->pubsubDestination = $pubsubDestination;
  }
  /**
   * @return PubsubDestination
   */
  public function getPubsubDestination()
  {
    return $this->pubsubDestination;
  }
  /**
   * List of output sprite sheets. Spritesheets require at least one VideoStream
   * in the Jobconfig.
   *
   * @param SpriteSheet[] $spriteSheets
   */
  public function setSpriteSheets($spriteSheets)
  {
    $this->spriteSheets = $spriteSheets;
  }
  /**
   * @return SpriteSheet[]
   */
  public function getSpriteSheets()
  {
    return $this->spriteSheets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobConfig::class, 'Google_Service_Transcoder_JobConfig');
