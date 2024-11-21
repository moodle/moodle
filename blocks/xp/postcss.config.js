module.exports = {
  plugins: {
    tailwindcss: {},
    // Moodle SCSS compiler require a fallback for older versions.
    'postcss-color-rgba-fallback': {},
    autoprefixer: {},
  }
}