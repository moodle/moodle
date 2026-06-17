DESCRIPTION OF NOTO SANS IMPORT INTO MOODLE
--------------------------------------------

Note: woff2 subset files are obtained via Google Fonts CDN, which distributes
subsetted builds based on the upstream Noto Sans project. The canonical source,
version numbering, and license are from the upstream GitHub repository.

1. Open the following URL in your browser:
   https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap

2. Locate the /* latin-ext */ and /* latin */ @font-face blocks for normal and italic
   styles, copy the woff2 URLs, and download:
   - noto-sans-normal-latin-ext.woff2
   - noto-sans-normal-latin.woff2
   - noto-sans-italic-latin-ext.woff2
   - noto-sans-italic-latin.woff2

3. Place the files in this folder (theme/boost/fonts/noto-sans/).

4. Update thirdpartylibs.xml with the new version number.

5. Update the unicode-range values in theme/boost/scss/moodle/fonts.scss
   if they have changed in the new version.

6. Run `grunt` to update the CSS style files.
