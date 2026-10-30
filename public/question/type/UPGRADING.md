# qtype (plugin type) Upgrade notes

## 5.3+

### Changed

- The standard question types no longer hardcode colours for the repeated answer, feedback and hint panels in their editing forms, so the panels follow the colour mode. Question type plugins with similar rules should replace them with design system tokens. For example `background-color: var(--mds-bg-surface-strong)` and `border-color: var(--mds-border-default)`.

  For more information see [MDL-90067](https://tracker.moodle.org/browse/MDL-90067)

