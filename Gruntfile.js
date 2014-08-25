/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    meta: {

      ui_main_js_path: 'src/Ponticlaro/Bebop/Resources/static/ui/core/js',
      ui_list_js_path: 'src/Ponticlaro/Bebop/Resources/static/ui/list/js',
      ui_multilist_js_path: 'src/Ponticlaro/Bebop/Resources/static/ui/multilist/assets/js',
      ui_media_js_path: 'src/Ponticlaro/Bebop/Resources/static/ui/media/js',
    },

    jshint: {

      ui_main: '<%= meta.ui_main_js_path %>/bebop-ui.js',
      ui_list: [
        '<%= meta.ui_list_js_path %>/collections',
        '<%= meta.ui_list_js_path %>/models',
        '<%= meta.ui_list_js_path %>/views',
        '<%= meta.ui_list_js_path %>/bebop-ui--list.js'
      ],
      ui_multilist: [
        '<%= meta.ui_list_js_path %>/views',
        '<%= meta.ui_multilist_js_path %>/bebop-ui--multilist.js'
      ],
      ui_media: [
        '<%= meta.ui_list_js_path %>/views',
        '<%= meta.ui_media_js_path %>/bebop-ui--media.js'
      ]
    },
    concat: {

      ui_main_js: {
        src: [
          '<%= meta.ui_main_js_path %>/vendor/jquery.ba-throttle-debounce.min.js',
          '<%= meta.ui_main_js_path %>/bebop-ui.js'
        ],
        dest: '<%= meta.ui_main_js_path %>/bebop-ui.min.js'
      },

      ui_list_js: {
        src: [
          '<%= meta.ui_list_js_path %>/views/List.js',
          '<%= meta.ui_list_js_path %>/views/ListItemView.js',
          '<%= meta.ui_list_js_path %>/models/ListItemModel.js',
          '<%= meta.ui_list_js_path %>/collections/ListCollection.js',
          '<%= meta.ui_list_js_path %>/bebop-ui--list.js'
        ],
        dest: '<%= meta.ui_list_js_path %>/bebop-ui--list.min.js'
      },

      ui_multilist_js: {
        src: [
          '<%= meta.ui_multilist_js_path %>/views/MultiList.js',
          '<%= meta.ui_multilist_js_path %>/bebop-ui--multilist.js',
        ],
        dest: '<%= meta.ui_multilist_js_path %>/bebop-ui--multilist.min.js'
      },

      ui_media_js: {
        src: [
          '<%= meta.ui_media_js_path %>/views/Media.js',
          '<%= meta.ui_media_js_path %>/bebop-ui--media.js',
        ],
        dest: '<%= meta.ui_media_js_path %>/bebop-ui--media.min.js'
      }
    },

    uglify: {

      mustache: {
        src: '<%= meta.ui_main_js_path %>/vendor/mustache.js',
        dest: '<%= meta.ui_main_js_path %>/vendor/mustache.min.js'
      },

      ui_main_js: {
        src: '<%= concat.ui_main_js.dest %>',
        dest: '<%= concat.ui_main_js.dest %>'
      },

      ui_list_js: {
        src: '<%= concat.ui_list_js.dest %>',
        dest: '<%= concat.ui_list_js.dest %>'
      },

      ui_multilist_js: {
        src: '<%= concat.ui_multilist_js.dest %>',
        dest: '<%= concat.ui_multilist_js.dest %>'
      },

      ui_media_js: {
        src: '<%= concat.ui_media_js.dest %>',
        dest: '<%= concat.ui_media_js.dest %>'
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Default task.
  grunt.registerTask('default', [
    'jshint', 
    'concat', 
    'uglify'
  ]);

};