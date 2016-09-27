var gulp  = require('gulp');
var del = require('del');
var zip = require('gulp-zip');
var shell = require('gulp-shell');
var args   = require('yargs').argv;
var composer = require('gulp-composer');
var runSequence = require('run-sequence');

gulp.task('setupTestEnv', shell.task([
    "bash bin/install-wp-tests.sh wordpress2 root root 127.0.0.1 latest"
]));

var releaseBase = '/Users/brandonbraner/projects/releases/leadpages-wordpress-v2/'
var releaseFolder = releaseBase+'leadpagesv2';
var zipsFolder = '/Users/brandonbraner/projects/releases/leadpages-wordpress-v2/archive/';

gulp.task('removeallfiles',function(){
    return del([releaseFolder+'/**/*'], {force: true});
});

//compress whole folder and move it for a full backup

gulp.task('compressandmove', function(){
    var date = new Date();
    return gulp.src('.')
        .pipe(zip('archive '+date+'.zip'))
        .pipe(gulp.dest(zipsFolder));
});

gulp.task('runcomposer', function(){
    return composer("update --no-dev");
});

gulp.task('run_composer_release', function(){
    return composer("update --no-dev --working-dir /Users/brandonbraner/projects/releases/leadpages-wordpress-v2/beta2/leadpages");
});

gulp.task('movetoreleases', function(){

    return gulp.src(['**/*'], {"base" : "."})
        .pipe(gulp.dest(releaseFolder));
});

gulp.task('removeUnneedFiles',function(){
    return del([releaseFolder+'/node_modules', releaseFolder+'/tests', releaseFolder+'/bin'], {force: true});
});

gulp.task('runcomposer2', function(){
    return composer("update");
});

gulp.task('compressrelease', function(){
    return gulp.src(releaseFolder)
        .pipe(zip('leadpagesv2.zip'))
        .pipe(gulp.dest(releaseBase));
});

gulp.task('deploy', function(){
    runSequence(
        //'compressandmove',
        'removeallfiles',
        'runcomposer',
        'movetoreleases',
        'removeUnneedFiles',
        'runcomposer2'
    );
});


/******
 * Unit test setup
 */

var testingFolder ='/Applications/MAMP/htdocs/wordpress_unit_test/wp-content/plugins/leadpages';
var enviroment = args.env;

gulp.task('move_to_test', function(){

    return gulp.src(['**/*'], {"base" : "."})
        .pipe(gulp.dest(testingFolder));
});

gulp.task('run_composer_tests', function(){
    return composer("update --working-dir /Applications/MAMP/htdocs/wordpress_unit_test/wp-content/plugins/leadpages");
});

gulp.task('run_integration_tests', shell.task([
    "php vendor/bin/wpcept run integration --env "+ enviroment
]));

gulp.task('setup_unit_test_plugin', function(){
    runSequence(
        'move_to_test',
        'run_composer_tests'
    );
});


