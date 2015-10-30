<?php
/*
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


  /**
   * The "languages" collection of methods.
   * Typical usage is:
   *  <code>
   *   $translateService = new Google_TranslateService(...);
   *   $languages = $translateService->languages;
   *  </code>
   */
  class Google_LanguagesServiceResource extends Google_ServiceResource {


    /**
     * List the source/target languages supported by the API (languages.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string target the language and collation in which the localized results should be returned
     * @return Google_LanguagesListResponse
     */
    public function listLanguages($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_LanguagesListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "detections" collection of methods.
   * Typical usage is:
   *  <code>
   *   $translateService = new Google_TranslateService(...);
   *   $detections = $translateService->detections;
   *  </code>
   */
  class Google_DetectionsServiceResource extends Google_ServiceResource {


    /**
     * Detect the language of text. (detections.list)
     *
     * @param string $q The text to detect
     * @param array $optParams Optional parameters.
     * @return Google_DetectionsListResponse
     */
    public function listDetections($q, $optParams = array()) {
      $params = array('q' => $q);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_DetectionsListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "translations" collection of methods.
   * Typical usage is:
   *  <code>
   *   $translateService = new Google_TranslateService(...);
   *   $translations = $translateService->translations;
   *  </code>
   */
  class Google_TranslationsServiceResource extends Google_ServiceResource {


    /**
     * Returns text translations from one language to another. (translations.list)
     *
     * @param string $q The text to translate
     * @param string $target The target language into which the text should be translated
     * @param array $optParams Optional parameters.
     *
     * @opt_param string source The source language of the text
     * @opt_param string format The format of the text
     * @opt_param string cid The customization id for translate
     * @return Google_TranslationsListResponse
     */
    public function listTranslations($q, $target, $optParams = array()) {
      $params = array('q' => $q, 'target' => $target);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TranslationsListResponse($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Translate (v2).
 *
 * <p>
 * Lets you translate text from one language to another
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/language/translate/v2/using_rest.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_TranslateService extends Google_Service {
  public $languages;
  public $detections;
  public $translations;
  /**
   * Constructs the internal representation of the Translate service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'language/translate/';
    $this->version = 'v2';
    $this->serviceName = 'translate';

    $client->addService($this->serviceName, $this->version);
    $this->languages = new Google_LanguagesServiceResource($this, $this->serviceName, 'languages', json_decode('{"methods": {"list": {"httpMethod": "GET", "response": {"$ref": "LanguagesListResponse"}, "id": "language.languages.list", "parameters": {"target": {"type": "string", "location": "query"}}, "path": "v2/languages"}}}', true));
    $this->detections = new Google_DetectionsServiceResource($this, $this->serviceName, 'detections', json_decode('{"methods": {"list": {"httpMethod": "GET", "response": {"$ref": "DetectionsListResponse"}, "id": "language.detections.list", "parameters": {"q": {"repeated": true, "required": true, "type": "string", "location": "query"}}, "path": "v2/detect"}}}', true));
    $this->translations = new Google_TranslationsServiceResource($this, $this->serviceName, 'translations', json_decode('{"methods": {"list": {"httpMethod": "GET", "response": {"$ref": "TranslationsListResponse"}, "id": "language.translations.list", "parameters": {"q": {"repeated": true, "required": true, "type": "string", "location": "query"}, "source": {"type": "string", "location": "query"}, "format": {"enum": ["html", "text"], "type": "string", "location": "query"}, "target": {"required": true, "type": "string", "location": "query"}, "cid": {"repeated": true, "type": "string", "location": "query"}}, "path": "v2"}}}', true));

  }
}

class Google_DetectionsListResponse extends Google_Model {
  protected $__detectionsType = 'Google_DetectionsResourceItems';
  protected $__detectionsDataType = 'array';
  public $detections;
  public function setDetections(/* array(Google_DetectionsResourceItems) */ $detections) {
    $this->assertIsArray($detections, 'Google_DetectionsResourceItems', __METHOD__);
    $this->detections = $detections;
  }
  public function getDetections() {
    return $this->detections;
  }
}

class Google_DetectionsResourceItems extends Google_Model {
  public $isReliable;
  public $confidence;
  public $language;
  public function setIsReliable($isReliable) {
    $this->isReliable = $isReliable;
  }
  public function getIsReliable() {
    return $this->isReliable;
  }
  public function setConfidence($confidence) {
    $this->confidence = $confidence;
  }
  public function getConfidence() {
    return $this->confidence;
  }
  public function setLanguage($language) {
    $this->language = $language;
  }
  public function getLanguage() {
    return $this->language;
  }
}

class Google_LanguagesListResponse extends Google_Model {
  protected $__languagesType = 'Google_LanguagesResource';
  protected $__languagesDataType = 'array';
  public $languages;
  public function setLanguages(/* array(Google_LanguagesResource) */ $languages) {
    $this->assertIsArray($languages, 'Google_LanguagesResource', __METHOD__);
    $this->languages = $languages;
  }
  public function getLanguages() {
    return $this->languages;
  }
}

class Google_LanguagesResource extends Google_Model {
  public $name;
  public $language;
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setLanguage($language) {
    $this->language = $language;
  }
  public function getLanguage() {
    return $this->language;
  }
}

class Google_TranslationsListResponse extends Google_Model {
  protected $__translationsType = 'Google_TranslationsResource';
  protected $__translationsDataType = 'array';
  public $translations;
  public function setTranslations(/* array(Google_TranslationsResource) */ $translations) {
    $this->assertIsArray($translations, 'Google_TranslationsResource', __METHOD__);
    $this->translations = $translations;
  }
  public function getTranslations() {
    return $this->translations;
  }
}

class Google_TranslationsResource extends Google_Model {
  public $detectedSourceLanguage;
  public $translatedText;
  public function setDetectedSourceLanguage($detectedSourceLanguage) {
    $this->detectedSourceLanguage = $detectedSourceLanguage;
  }
  public function getDetectedSourceLanguage() {
    return $this->detectedSourceLanguage;
  }
  public function setTranslatedText($translatedText) {
    $this->translatedText = $translatedText;
  }
  public function getTranslatedText() {
    return $this->translatedText;
  }
}
