# aiprovider_gemini Upgrade notes

## 5.2.2+

### Added

- A new `gemini31flashimage` model class has been added to support `gemini-3.1-flash-image`, the model Google recommends as the migration path for the retiring Imagen 4 endpoints (`imagen-4.0-generate-001`, `-ultra`, `-fast`), and it is now the default model for the "Generate image" action.

  For more information see [MDL-89431](https://tracker.moodle.org/browse/MDL-89431)

### Changed

- `process_generate_image` now branches its request and response handling on the configured endpoint's method (`:predict` for Imagen vs `:generateContent` for Gemini's native image generation), instead of assuming the Imagen protocol. This is determined from the endpoint URL rather than the model name, so it also applies to any custom model an admin configures.

  For more information see [MDL-89431](https://tracker.moodle.org/browse/MDL-89431)

