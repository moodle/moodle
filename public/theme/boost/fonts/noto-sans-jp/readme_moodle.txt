DESCRIPTION OF NOTO SANS JP IMPORT INTO MOODLE
-----------------------------------------------

Note: woff2 subset files are obtained via Google Fonts CDN, which distributes
subsetted builds based on the upstream Noto CJK project. The canonical source,
version numbering, and license are from the upstream GitHub repository.

1. Open the following URL in your browser:
   https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap

2. Locate the 120 numbered CJK @font-face blocks (no named subset comment,
   indices .0 through .119), copy each woff2 URL, and download, renaming to:
   - noto-sans-jp-normal-000.woff2  (index .0)
   - noto-sans-jp-normal-001.woff2  (index .1)
   - ...
   - noto-sans-jp-normal-119.woff2  (index .119)

   Skip the 4 named subsets (/* cyrillic */, /* vietnamese */, /* latin-ext */,
   /* latin */).

3. Place the files in this folder (theme/boost/fonts/noto-sans-jp/).

4. Update thirdpartylibs.xml with the new version number.

5. Update the unicode-range values in the Noto Sans JP section of
   theme/boost/scss/moodle/fonts.scss if they have changed in the new version.

6. Run `grunt` to update the CSS style files.
