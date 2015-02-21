module.exports = function(grunt){
    "use strict";
    require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            assets: {
                files: ['sass/**/*.scss'],
                tasks: ['buildcss']
            },
            js: {
                files: [
					'js/**/*.js',
                    '!js/build/main.min.js'
				],
                tasks: ['optimize']
            }
        },
        uglify: {
            my_target: {
                files: {
                    'js/build/main.min.js': [
                        'vendor/jquery/dist/jquery.js',
                        'vendor/underscore/underscore.js',
                        'vendor/backbone/backbone.js',
                        'vendor/backbone.babysitter/libs/backbone.babysitter.js',
                        'vendor/backbone.wreqr/libs/backbone.wreqr.js',
                        'vendor/marionette/lib/backbone.marionette.js',
                        'vendor/bootstrap-sass/assets/javascripts/bootstrap.js',
                        'vendor/moment/moment.js',
                        'js/**/*.js',
                        '!js/build/main.min.js'
                    ]
                }
            }
        },
        cssc: {
            build: {
                options: {
                    consolidateViaDeclarations: true,
                    consolidateViaSelectors:    true,
                    consolidateMediaQueries:    true
                },
                files: {
                    'build/css/style.css': 'compiled/style.css'
                }
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0
            },
            build: {
                src: 'compiled/style.css',
                dest: 'compiled/style.min.css'
            }
        },
        sass: {
			options: {
				style: 'compressed'
			},
			build: {
                files: {
                    'compiled/style.css': 'sass/style.scss'
                }
            }
        }
    });

	grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-sass');

    grunt.registerTask('watch-jshint', ['watch:jshint']);
    grunt.registerTask('watch-js', ['watch:js']);
    grunt.registerTask('watch-assets', ['watch:assets']);

    grunt.registerTask('optimize', ['uglify']);
    grunt.registerTask('buildcss',  ['sass', 'cssmin']);

    grunt.registerTask('default',  []);
};