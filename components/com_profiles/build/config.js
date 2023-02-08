module.exports = {
  // compoenent version
  version: '1.0.0',

  // css files paths
  css: {
    front_src: './src/scss/front.scss',
    front_dest: './../../../media/com_profiles/css/',
    admin_src: './src/scss/admin.scss',
    admin_dest: '../../../administrator/components/com_profiles/assets/css/',
  },

  // js files paths
  js: {
    front_src: './src/js/front.js',
    front_dest: './../../../media/com_profiles/js/',
    admin_src: './src/js/admin.js',
    admin_dest: '../../../administrator/components/com_profiles/assets/js/',
  },

  // package files paths
  package: {
    administrator_src:
      './../../../../administrator/components/com_profiles/**/*.*',
    administrator_dest: './pkg/administrator/',

    site_src: './.././**/*.*',
    site_dest: './pkg/site/',

    installer_src:
      './../../../../administrator/components/com_profiles/installer/**/*.*',
    installer_dest: './pkg/installer/',

    media_src: './../../../../media/com_profiles/**/*.*',
    media_dest: './pkg/media/',

    script_file_src:
      './../../../../administrator/components/com_profiles/script.php',
    script_file_dest: './pkg/',
    profiles_file_src:
      './../../../../administrator/components/com_profiles/profiles.xml',
    profiles_file_dest: './pkg/',

    plugins_src: './../../../../plugins/search/studies/**/*.*',
    plugins_dest: './pkg/plugins/search/studies/',

    admin_language_gb_ini_src:
      './../../../../administrator/language/en-GB/en-GB.com_profiles.ini',
    admin_language_gb_sys_src:
      './../../../../administrator/language/en-GB/en-GB.com_profiles.sys.ini',
    admin_language_gb_dest: './pkg/administrator/languages/en-GB/',

    site_language_gb_src: './../../../../language/en-GB/en-GB.com_profiles.ini',
    site_language_gb_dest: './pkg/site/languages/en-GB/',
  },
};
