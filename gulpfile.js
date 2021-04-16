const { watch, src, dest, parallel } = require('gulp');
const concat		= require('gulp-concat');
const autoprefixer	= require('gulp-autoprefixer');
const clean_css		= require('gulp-clean-css');
const scss			= require('gulp-sass');
const uglify		= require('gulp-uglify-es').default;


function styles() {
	return src([
		'css/scss/template.scss'
	])
	.pipe(concat('all.min.css'))
	.pipe(scss())
	.pipe(autoprefixer({ overrideBrowserslist: ['last 3 versions'], grid: true }))
	.pipe(clean_css({ level: { 1: { specialComments: 0 } } }))
	.pipe(dest('css/'));
}

function scripts() {
	return src([
		'js/srcs/JQuery.js',
		'js/srcs/main.js'
	])
	.pipe(concat('all.min.js'))
	.pipe(uglify())
	.pipe(dest('js/'));
}

function startwatch() {
	watch('js/srcs/*', scripts);
	watch('css/scss/*', styles);
}

exports.styles = styles();
exports.default = parallel(styles, scripts, startwatch);