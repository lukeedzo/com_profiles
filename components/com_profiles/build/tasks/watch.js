const run = require('gulp');
const config = require('../config');

run.task('watch', (cb) => {
  // watch css
  run.watch(
    './src/scss/*.scss',
    run.series('build-front-css', 'build-admin-css')
  );

  // watch js
  run.watch(
    './src/js/*.js',
    run.series('build-front-scripts', 'build-admin-scripts')
  );
  cb();
});
