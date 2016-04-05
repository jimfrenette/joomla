'use strict';

var gulp        = require('gulp'),
    assign      = require('lodash.assign'),
    browserify  = require('browserify'),
    buffer      = require('vinyl-buffer'),
    cssnano     = require('gulp-cssnano'),
    gutil       = require('gulp-util'),
    source      = require('vinyl-source-stream'),
    sass        = require('gulp-sass'),
    sourcemaps  = require('gulp-sourcemaps'),
    uglify      = require('gulp-uglify'),
    watchify    = require('watchify');

var argv = require('yargs').argv;

var config = {
    js: {
        defaultSrcFile: 'images.js',
        defaultSrcPath: './src/js/',
        defaultDest: './js'
    }
}

// gulp js argv example:
// gulp js --src=./src/js/imageslist.js --dest=./js
//
var jsSrcFile = config.js.defaultSrcFile,
    jsSrcPath = config.js.defaultSrcPath,
    jsSrc = config.js.defaultSrcPath + config.js.defaultSrcFile,
    jsDest    = config.js.defaultDest;
if (argv.src && argv.dest) {
    jsSrc = argv.src;
    jsSrcFile = jsSrc.split('/').pop();
    jsDest = argv.dest;
}

// custom browserify options
var customOpts = {
    entries: [jsSrc],
    debug: true
};
var opts = assign({}, watchify.args, customOpts);
var b = watchify(browserify(opts));

gulp.task('js', bundle); // run `gulp js` to build the file
b.on('update', bundle); // on any dep update, runs the bundler
b.on('log', gutil.log); // output build logs to terminal

function bundle() {
  return b.bundle()
    // log errors if they happen
    .on('error', gutil.log.bind(gutil, 'Browserify Error'))
    .pipe(source(jsSrcFile))
    // optional, remove if you don't need to buffer file contents
    .pipe(buffer())
    // optional, remove if you dont want sourcemaps
    .pipe(sourcemaps.init({loadMaps: true})) // loads map from browserify file
    // Add transformation tasks to the pipeline here.
    .pipe(uglify()) // minify
    .pipe(sourcemaps.write('./')) // writes .map file
    .pipe(gulp.dest(jsDest));
}


// autoprefixer
var supported = [
    'last 2 versions',
    'safari >= 8',
    'ie >= 9',
    'ff >= 20',
    'ios 6',
    'android 4'
];

/**
 * Sass compile
 */
gulp.task('css', function(){
    return gulp.src(['src/sass/**/*.scss'])
        .pipe(sass())
        .pipe(cssnano({
            autoprefixer: {browsers: supported, add: true}
        }))
        .pipe(gulp.dest('css'));
});
