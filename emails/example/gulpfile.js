var gulp = require('gulp'),
	argv = require('yargs').argv,
	premailer = require('gulp-premailer'),
	rename = require('gulp-rename'),
	c = require('ansi-colors');

var minify = require('gulp-minifier');


var emailsPath = './';

gulp.task('compile', function (done) {
	// if (!argv.email) {
	// 	console.log(c.red('Usage: gulp compile'));
	// 	return done();
	// }

	// var emailName = argv.email,
	// 	emailPath = emailsPath + emailName + '/source.html';
	var emailPath = 'source.html';

	return gulp.src(emailPath)
		.pipe(premailer({}))
		.pipe(rename(function (path) {
			path.basename = "compiled";
		}))
		.pipe(gulp.dest(emailsPath))

});

gulp.task('minify', function(done) {
	return gulp.src('./compiled.html').pipe(minify({
    minify: true,
    minifyHTML: {
      collapseWhitespace: true,
      conservativeCollapse: true,
    },
    minifyJS: {
      sourceMap: true
    },
    minifyCSS: false,
    getKeptComment: function (content, filePath) {
        var m = content.match(/\/\*![\s\S]*?\*\//img);
        return m && m.join('\n') + '\n' || '';
    }
  })).pipe(gulp.dest('./'));
});