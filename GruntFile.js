module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');

    grunt.initConfig({
        uglify: {
            locatorjs: {
                options: {
                    mangle: false,
                    compress: true,
                    sourceMap: true,
                    sourceMapName: 'src/js/locator/locator.js.map'
                },
                files: {
                    'src/js/locator/locator.min.js': [
                        'src/js/locator/src/history.js',
                        'src/js/locator/src/locator.js'
                    ]
                }
            }
        },

        watch: {
            files: "./src/js/locator/src/**",
            tasks: ["uglify","concat"]

        },
        concat: {
            dist: {
                src: ['src/js/locator/src/history.js', 'src/js/locator/src/locator.js'],
                dest: 'src/js/locator/locator.js'
            }
        }
    });

    // Default task(s).
    grunt.registerTask('default', ['less']);
    grunt.registerTask( 'serve', [ 'connect', 'watch'] );
};