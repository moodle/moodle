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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1InputAudioConfig extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_UNSPECIFIED = 'AUDIO_ENCODING_UNSPECIFIED';
  /**
   * Uncompressed 16-bit signed little-endian samples (Linear PCM). LINT:
   * LEGACY_NAMES
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_LINEAR_16 = 'AUDIO_ENCODING_LINEAR_16';
  /**
   * [`FLAC`](https://xiph.org/flac/documentation.html) (Free Lossless Audio
   * Codec) is the recommended encoding because it is lossless (therefore
   * recognition is not compromised) and requires only about half the bandwidth
   * of `LINEAR16`. `FLAC` stream encoding supports 16-bit and 24-bit samples,
   * however, not all fields in `STREAMINFO` are supported.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_FLAC = 'AUDIO_ENCODING_FLAC';
  /**
   * 8-bit samples that compand 14-bit audio samples using G.711 PCMU/mu-law.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_MULAW = 'AUDIO_ENCODING_MULAW';
  /**
   * Adaptive Multi-Rate Narrowband codec. `sample_rate_hertz` must be 8000.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_AMR = 'AUDIO_ENCODING_AMR';
  /**
   * Adaptive Multi-Rate Wideband codec. `sample_rate_hertz` must be 16000.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_AMR_WB = 'AUDIO_ENCODING_AMR_WB';
  /**
   * Opus encoded audio frames in Ogg container
   * ([OggOpus](https://wiki.xiph.org/OggOpus)). `sample_rate_hertz` must be
   * 16000.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_OGG_OPUS = 'AUDIO_ENCODING_OGG_OPUS';
  /**
   * Although the use of lossy encodings is not recommended, if a very low
   * bitrate encoding is required, `OGG_OPUS` is highly preferred over Speex
   * encoding. The [Speex](https://speex.org/) encoding supported by Dialogflow
   * API has a header byte in each block, as in MIME type `audio/x-speex-with-
   * header-byte`. It is a variant of the RTP Speex encoding defined in [RFC
   * 5574](https://tools.ietf.org/html/rfc5574). The stream is a sequence of
   * blocks, one block per RTP packet. Each block starts with a byte containing
   * the length of the block, in bytes, followed by one or more frames of Speex
   * data, padded to an integral number of bytes (octets) as specified in RFC
   * 5574. In other words, each RTP header is replaced with a single byte
   * containing the block length. Only Speex wideband is supported.
   * `sample_rate_hertz` must be 16000.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_SPEEX_WITH_HEADER_BYTE = 'AUDIO_ENCODING_SPEEX_WITH_HEADER_BYTE';
  /**
   * 8-bit samples that compand 13-bit audio samples using G.711 PCMU/a-law.
   */
  public const AUDIO_ENCODING_AUDIO_ENCODING_ALAW = 'AUDIO_ENCODING_ALAW';
  /**
   * No model variant specified. In this case Dialogflow defaults to
   * USE_BEST_AVAILABLE.
   */
  public const MODEL_VARIANT_SPEECH_MODEL_VARIANT_UNSPECIFIED = 'SPEECH_MODEL_VARIANT_UNSPECIFIED';
  /**
   * Use the best available variant of the Speech model that the caller is
   * eligible for.
   */
  public const MODEL_VARIANT_USE_BEST_AVAILABLE = 'USE_BEST_AVAILABLE';
  /**
   * Use standard model variant even if an enhanced model is available. See the
   * [Cloud Speech documentation](https://cloud.google.com/speech-to-
   * text/docs/enhanced-models) for details about enhanced models.
   */
  public const MODEL_VARIANT_USE_STANDARD = 'USE_STANDARD';
  /**
   * Use an enhanced model variant: * If an enhanced variant does not exist for
   * the given model and request language, Dialogflow falls back to the standard
   * variant. The [Cloud Speech documentation](https://cloud.google.com/speech-
   * to-text/docs/enhanced-models) describes which models have enhanced
   * variants.
   */
  public const MODEL_VARIANT_USE_ENHANCED = 'USE_ENHANCED';
  protected $collection_key = 'phraseHints';
  /**
   * Required. Audio encoding of the audio content to process.
   *
   * @var string
   */
  public $audioEncoding;
  protected $bargeInConfigType = GoogleCloudDialogflowCxV3beta1BargeInConfig::class;
  protected $bargeInConfigDataType = '';
  /**
   * Optional. If `true`, Dialogflow returns SpeechWordInfo in
   * StreamingRecognitionResult with information about the recognized speech
   * words, e.g. start and end time offsets. If false or unspecified, Speech
   * doesn't return any word-level information.
   *
   * @var bool
   */
  public $enableWordInfo;
  /**
   * Optional. Which Speech model to select for the given request. For more
   * information, see [Speech
   * models](https://cloud.google.com/dialogflow/cx/docs/concept/speech-models).
   *
   * @var string
   */
  public $model;
  /**
   * Optional. Which variant of the Speech model to use.
   *
   * @var string
   */
  public $modelVariant;
  /**
   * If `true`, the request will opt out for STT conformer model migration. This
   * field will be deprecated once force migration takes place in June 2024.
   * Please refer to [Dialogflow CX Speech model
   * migration](https://cloud.google.com/dialogflow/cx/docs/concept/speech-
   * model-migration).
   *
   * @var bool
   */
  public $optOutConformerModelMigration;
  /**
   * Optional. A list of strings containing words and phrases that the speech
   * recognizer should recognize with higher likelihood. See [the Cloud Speech
   * documentation](https://cloud.google.com/speech-to-text/docs/basics#phrase-
   * hints) for more details.
   *
   * @var string[]
   */
  public $phraseHints;
  /**
   * Sample rate (in Hertz) of the audio content sent in the query. Refer to
   * [Cloud Speech API documentation](https://cloud.google.com/speech-to-
   * text/docs/basics) for more details.
   *
   * @var int
   */
  public $sampleRateHertz;
  /**
   * Optional. If `false` (default), recognition does not cease until the client
   * closes the stream. If `true`, the recognizer will detect a single spoken
   * utterance in input audio. Recognition ceases when it detects the audio's
   * voice has stopped or paused. In this case, once a detected intent is
   * received, the client should close the stream and start a new request with a
   * new stream as needed. Note: This setting is relevant only for streaming
   * methods.
   *
   * @var bool
   */
  public $singleUtterance;

  /**
   * Required. Audio encoding of the audio content to process.
   *
   * Accepted values: AUDIO_ENCODING_UNSPECIFIED, AUDIO_ENCODING_LINEAR_16,
   * AUDIO_ENCODING_FLAC, AUDIO_ENCODING_MULAW, AUDIO_ENCODING_AMR,
   * AUDIO_ENCODING_AMR_WB, AUDIO_ENCODING_OGG_OPUS,
   * AUDIO_ENCODING_SPEEX_WITH_HEADER_BYTE, AUDIO_ENCODING_ALAW
   *
   * @param self::AUDIO_ENCODING_* $audioEncoding
   */
  public function setAudioEncoding($audioEncoding)
  {
    $this->audioEncoding = $audioEncoding;
  }
  /**
   * @return self::AUDIO_ENCODING_*
   */
  public function getAudioEncoding()
  {
    return $this->audioEncoding;
  }
  /**
   * Configuration of barge-in behavior during the streaming of input audio.
   *
   * @param GoogleCloudDialogflowCxV3beta1BargeInConfig $bargeInConfig
   */
  public function setBargeInConfig(GoogleCloudDialogflowCxV3beta1BargeInConfig $bargeInConfig)
  {
    $this->bargeInConfig = $bargeInConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1BargeInConfig
   */
  public function getBargeInConfig()
  {
    return $this->bargeInConfig;
  }
  /**
   * Optional. If `true`, Dialogflow returns SpeechWordInfo in
   * StreamingRecognitionResult with information about the recognized speech
   * words, e.g. start and end time offsets. If false or unspecified, Speech
   * doesn't return any word-level information.
   *
   * @param bool $enableWordInfo
   */
  public function setEnableWordInfo($enableWordInfo)
  {
    $this->enableWordInfo = $enableWordInfo;
  }
  /**
   * @return bool
   */
  public function getEnableWordInfo()
  {
    return $this->enableWordInfo;
  }
  /**
   * Optional. Which Speech model to select for the given request. For more
   * information, see [Speech
   * models](https://cloud.google.com/dialogflow/cx/docs/concept/speech-models).
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Optional. Which variant of the Speech model to use.
   *
   * Accepted values: SPEECH_MODEL_VARIANT_UNSPECIFIED, USE_BEST_AVAILABLE,
   * USE_STANDARD, USE_ENHANCED
   *
   * @param self::MODEL_VARIANT_* $modelVariant
   */
  public function setModelVariant($modelVariant)
  {
    $this->modelVariant = $modelVariant;
  }
  /**
   * @return self::MODEL_VARIANT_*
   */
  public function getModelVariant()
  {
    return $this->modelVariant;
  }
  /**
   * If `true`, the request will opt out for STT conformer model migration. This
   * field will be deprecated once force migration takes place in June 2024.
   * Please refer to [Dialogflow CX Speech model
   * migration](https://cloud.google.com/dialogflow/cx/docs/concept/speech-
   * model-migration).
   *
   * @param bool $optOutConformerModelMigration
   */
  public function setOptOutConformerModelMigration($optOutConformerModelMigration)
  {
    $this->optOutConformerModelMigration = $optOutConformerModelMigration;
  }
  /**
   * @return bool
   */
  public function getOptOutConformerModelMigration()
  {
    return $this->optOutConformerModelMigration;
  }
  /**
   * Optional. A list of strings containing words and phrases that the speech
   * recognizer should recognize with higher likelihood. See [the Cloud Speech
   * documentation](https://cloud.google.com/speech-to-text/docs/basics#phrase-
   * hints) for more details.
   *
   * @param string[] $phraseHints
   */
  public function setPhraseHints($phraseHints)
  {
    $this->phraseHints = $phraseHints;
  }
  /**
   * @return string[]
   */
  public function getPhraseHints()
  {
    return $this->phraseHints;
  }
  /**
   * Sample rate (in Hertz) of the audio content sent in the query. Refer to
   * [Cloud Speech API documentation](https://cloud.google.com/speech-to-
   * text/docs/basics) for more details.
   *
   * @param int $sampleRateHertz
   */
  public function setSampleRateHertz($sampleRateHertz)
  {
    $this->sampleRateHertz = $sampleRateHertz;
  }
  /**
   * @return int
   */
  public function getSampleRateHertz()
  {
    return $this->sampleRateHertz;
  }
  /**
   * Optional. If `false` (default), recognition does not cease until the client
   * closes the stream. If `true`, the recognizer will detect a single spoken
   * utterance in input audio. Recognition ceases when it detects the audio's
   * voice has stopped or paused. In this case, once a detected intent is
   * received, the client should close the stream and start a new request with a
   * new stream as needed. Note: This setting is relevant only for streaming
   * methods.
   *
   * @param bool $singleUtterance
   */
  public function setSingleUtterance($singleUtterance)
  {
    $this->singleUtterance = $singleUtterance;
  }
  /**
   * @return bool
   */
  public function getSingleUtterance()
  {
    return $this->singleUtterance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1InputAudioConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1InputAudioConfig');
