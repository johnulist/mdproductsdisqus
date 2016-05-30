module.exports = function(grunt) {

    grunt.initConfig({
        compress: {
            main: {
                options: {
                    archive: 'mdproductsdisqus.zip'
                },
                files: [
                    {src: ['controllers/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['classes/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['docs/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['override/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['logs/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['vendor/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['translations/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['upgrade/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['optionaloverride/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['oldoverride/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['sql/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['lib/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['defaultoverride/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: ['views/**'], dest: 'mdproductsdisqus/', filter: 'isFile'},
                    {src: 'config.xml', dest: 'mdproductsdisqus/'},
                    {src: 'index.php', dest: 'mdproductsdisqus/'},
                    {src: 'mdproductsdisqus.php', dest: 'mdproductsdisqus/'},
                    {src: 'logo.png', dest: 'mdproductsdisqus/'},
                    {src: 'logo.gif', dest: 'mdproductsdisqus/'},
                    {src: 'LICENSE', dest: 'mdproductsdisqus/'}
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-compress');

    grunt.registerTask('default', ['compress']);
};