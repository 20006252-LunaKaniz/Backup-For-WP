<?php
/*
Plugin Name: Wordpress GitHub Backup
Description: This plugin integrates Wordpress with GitHub for backup purposes.
Version: 1.0
Author: Luna Kaniz
*/

class WP_GitHub_Backup {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
        add_action( 'admin_init', array( $this, 'backup_to_github' ) );
    }
    
    public function add_menu_item() {
        add_management_page( 'Wordpress GitHub Backup', 'GitHub Backup', 'manage_options', 'wp_github_backup', array( $this, 'backup_page' ) );
    }
    
    public function backup_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }
        
        $backup_url = add_query_arg( 'backup', 'github' );
        echo '<h1>Wordpress GitHub Backup</h1>';
        echo '<p>Click the button below to backup your Wordpress website to GitHub.</p>';
        echo '<p><a href="' . $backup_url . '" class="button button-primary">Backup to GitHub</a></p>';
    }
    
    public function backup_to_github() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        if ( ! isset( $_GET['backup'] ) || $_GET['backup'] != 'github' ) {
            return;
        }
        
        // Code to backup the Wordpress website to GitHub

        // Define the path to your Wordpress site and the backup file
        define('WORDPRESS_DIR', '/path/to/your/wordpress/site');
        define('BACKUP_FILE', 'wordpress-backup.zip');

        // Export the database
        $db_file = WORDPRESS_DIR . '/database.sql';
        $command = 'mysqldump -u username -p password database_name > ' . $db_file;
        exec($command);

        // Compress the files into a single archive
        $command = 'zip -r ' . BACKUP_FILE . ' ' . WORDPRESS_DIR;
        exec($command);

        // Push the archive to GitHub
        $command = 'cd ' . WORDPRESS_DIR . ' && git init && git add . && git commit -m "Backup ' . date('Y-m-d H:i:s') . '" && git remote add origin https://github.com/username/repository.git && git push -u origin master';
        exec($command);


        
        // Redirect to the plugin page
        wp_safe_redirect( menu_page_url( 'wp_github_backup', false ) );
        exit;
    }
}

new WP_GitHub_Backup();
?>