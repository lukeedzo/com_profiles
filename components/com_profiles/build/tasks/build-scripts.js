const run = require('gulp');
const babel = require('gulp-babel');
const config = require('../config');
const uglify = require('gulp-uglify');
const webpack = require('webpack-stream');

// Build es6 to es5
const buildScripts = (task, src, dest) => {
  run.task(task, () => {
    return run
      .src(src)
      .pipe(
        webpack({
          mode: 'production',
          optimization: {
            minimize: true,
          },
        })
      )
      .pipe(
        babel({
          presets: ['@babel/env'],
        })
      )
      .pipe(uglify())
      .pipe(run.dest(dest));
  });
};

buildScripts('build-admin-scripts', config.js.admin_src, config.js.admin_dest);
buildScripts('build-front-scripts', config.js.front_src, config.js.front_dest);
