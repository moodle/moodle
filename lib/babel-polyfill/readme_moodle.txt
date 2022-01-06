Installation instructions can be found at https://babeljs.io/docs/en/babel-polyfill.html#usage-in-browser

The steps are essentially:
1.) npm install @babel/polyfill
2.) copy polyfill.js and polyfill.min.js from the "dist" folder from the npm release into lib/babel-polyfill/
3.) npm uninstall --save @babel/polyfill (don't want to include that with Moodle package.json)
