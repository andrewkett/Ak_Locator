module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

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
                    'src/js/locator/locator.min.js': ['src/js/locator/locator.js']
                }
            }
        },

        watch: {
            files: "./src/js/locator/locator.js",
            tasks: ["uglify"]

        }
    });

    // Default task(s).
    grunt.registerTask('default', ['less']);
    grunt.registerTask( 'serve', [ 'connect', 'watch'] );
};