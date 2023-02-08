const run = require('gulp');
const config = require('../config');
const path = require('path');
const clean = require('gulp-clean');
const replace = require('gulp-string-replace');
const zip = require('gulp-zip');

run.task('clean', () => {
  return run.src(['./pkg/', './package/'], { read: false }).pipe(clean());
});

run.task('administrator', () => {
  return run
    .src(path.join(__dirname, config.package.administrator_src))
    .pipe(replace(/CVS(.{7})/g, `CVS: ${config.version}`))
    .pipe(run.dest(config.package.administrator_dest));
});

run.task('site', () => {
  return run
    .src([config.package.site_src, '!./node_modules/**'], { dot: true })
    .pipe(replace(/CVS(.{7})/g, `CVS: ${config.version}`))
    .pipe(run.dest(config.package.site_dest));
});

run.task('installer', () => {
  return run
    .src(path.join(__dirname, config.package.installer_src))
    .pipe(replace(/CVS(.{7})/g, `CVS: ${config.version}`))
    .pipe(run.dest(config.package.installer_dest));
});

run.task('media', () => {
  return run
    .src(path.join(__dirname, config.package.media_src))
    .pipe(run.dest(config.package.media_dest));
});

run.task('plugins', () => {
  return run
    .src(path.join(__dirname, config.package.plugins_src))
    .pipe(replace(/CVS(.{7})/g, `CVS: ${config.version}`))
    .pipe(run.dest(config.package.plugins_dest));
});

run.task('administrator-language-gb', () => {
  return run
    .src([
      path.join(__dirname, config.package.admin_language_gb_ini_src),
      path.join(__dirname, config.package.admin_language_gb_sys_src),
    ])
    .pipe(run.dest(config.package.admin_language_gb_dest));
});

run.task('site-language-gb', () => {
  return run
    .src(path.join(__dirname, config.package.site_language_gb_src))
    .pipe(run.dest(config.package.site_language_gb_dest));
});

run.task('installer-script', () => {
  return run
    .src([
      path.join(__dirname, config.package.script_file_src),
      path.join(__dirname, config.package.profiles_file_src),
    ])
    .pipe(replace(/CVS(.{7})/g, `CVS: ${config.version}`))
    .pipe(run.dest(config.package.profiles_file_dest));
});

run.task('indexhtml-file', () => {
  return run.src(['./index.html']).pipe(run.dest('./pkg/site/build/package/'));
});

run.task('updates-file', () => {
  return run
    .src(
      path.join(
        __dirname,
        './../../../../administrator/components/com_profiles/updates.xml'
      )
    )
    .pipe(run.dest('./package/'));
});

run.task('zip', () => {
  return run
    .src(['./pkg/**/*.*', '!./pkg/site/build/**/*.*'], { dot: true })
    .pipe(zip(`com_profiles-${config.version}.zip`))
    .pipe(run.dest('./package/'));
});
