# aiprovider_openai Upgrade notes

## 5.1.4

### Added

- A new `aiprovider_openai\aimodel\openai_image_base` interface has been added. Image generation model classes must now implement this interface to declare their `response_format`, `output_format`, size, and quality mappings. Existing custom model classes that handle image generation should implement this interface to ensure correct API parameters are sent.

  For more information see [MDL-85352](https://tracker.moodle.org/browse/MDL-85352)
- A new `gptimage1` model class has been added to support gpt-image-1.5.
  This model uses `output_format=png` instead of `response_format`, and maps Moodle quality values to the values expected by the API: 'standard' maps to 'medium' and 'hd' maps to 'high'.

  For more information see [MDL-85352](https://tracker.moodle.org/browse/MDL-85352)

### Changed

- The `dalle3` model class now implements `openai_image_base` and switches from returning a URL to returning `response_format=b64_json`.
  The image is now decoded directly from the API response instead of being downloaded via a second HTTP request. Size and quality logic has been moved into the model class.

  For more information see [MDL-85352](https://tracker.moodle.org/browse/MDL-85352)
