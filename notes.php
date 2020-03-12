<?php
/*
Plugin Name:  Notes
Description:  This plugin enables notes for the WordPress dashboard users
Author: 	  Lucia Developer
Author URI:   https://luciadeveloper.com
Version:      0.1
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Domain Path:  /languages
Text Domain:  notes
*/

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Note
 *
 */
class Note {
    /**
     * @var string
     *
     * Set post type params
     */
    private $type               = 'note';
    private $slug               = 'notes';
    private $name               = 'Notes';
    private $singular_name      = 'Note';
   
   
    public function __construct() {
        // Register the post type
        add_action('init', array($this, 'registerCPT'));
    }
    
     /**
     * Register post type
     */
    public function registerCPT() {
        $labels = array(
            'name'                  => $this->name,
            'singular_name'         => $this->singular_name,
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New '   . $this->singular_name,
            'edit_item'             => 'Edit '      . $this->singular_name,
            'new_item'              => 'New '       . $this->singular_name,
            'all_items'             => 'All '       . $this->name,
            'view_item'             => 'View '      . $this->name,
            'search_items'          => 'Search '    . $this->name,
            'not_found'             => 'No '        . strtolower($this->name) . ' found',
            'not_found_in_trash'    => 'No '        . strtolower($this->name) . ' found in Trash',
            'parent_item_colon'     => '',
            'menu_name'             => $this->name
        );
        $args = array(
            'labels'                => $labels,
            'public'                => false, // the CPT will not have a page on the front-end
            'publicly_queryable'    => false, // the content of the notes will not be found on search
            'show_ui'               => false, // notes are not editable. 
            'show_in_menu'          => false, //the CPT will not have its own page on the admin
            'query_var'             => true,
            'rewrite'               => array( 'slug' => $this->slug ),
            'capability_type'       => 'post',
            'has_archive'           => false,
            'hierarchical'          => false,
            'menu_position'         => 0,
            'supports'              => array(),
            'yarpp_support'         => false
        );
        register_post_type( $this->type, $args );
    }
      
}

/**
 * Instantiate class, creating post type Notes
 */
 
global $note;
$note = new Note();


/**
 * Class NotesPage
 *
 */

class NotesPage { 
	
	public function wp_enqueue_styles() {
		wp_register_style( 'notes', plugins_url( 'assets/css/custom.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/custom.css' ) );
		wp_enqueue_style( 'notes');
	}
	
	public function __construct() {
		// Enqueue styles for the admin
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_styles' ), 20 );
		add_action( 'admin_menu', array($this,'notes_admin_page_menu') );
	}
	
	public function notes_admin_page_menu() {
		add_menu_page( 'notes', 'Notes', 'edit_posts', 'notes', array($this,'notes_admin_page'), 'dashicons-format-aside', 6 ) ;
	}
	
	function renderForm(){
		?>
		<form method="POST" action="" id="notes-form">
			<label for="post_content"><h2><?php echo _e('Add your note','Notes')?></h2></label>
			<textarea name="post_content" id="post_content" required/></textarea>	
			<input type="text" value="1" name="new_post" hidden />
			<input type="submit" value="<?php echo _e('Add note','Notes');?>" />		
		</form>
		<?php
	}
	
	function insertNote(){
		//insterts a new note, with the info submited on the form.
		if(isset($_POST['new_post']) == '1'){
			
			$user_id = get_current_user_id();
			$post_content = $_POST['post_content'];
			
			// Create post object
			$my_post = array(
			  'post_title'    => 'note',
			  'post_content'  => sanitize_text_field($post_content),
			  'post_type'	  => 'note',
			  'post_author'   => $user_id,
			  'post_status'   => 'publish',
		
			);
			 
			// Insert the post into the database
			wp_insert_post($my_post);
		}
	}
	
	function showNotes(){ 
		// get notes		
		$args = array(
		  'post_type'      => 'note',
		  'numberposts'   => -1, //shows all the notes
		);
		 
		$notes = get_posts( $args ); ?>
		
		<h2><?php _e('Notes submited:','Notes') ?></h2>
		<ul><?php
			//prin the list of notes, if there are some		
			if ( $notes ) {
		        foreach ( $notes as $note ) : 
					setup_postdata( $note ); 
					$authorId = $note->post_author;  
					$author = get_user_by( 'id', $authorId )->display_name;
					$url = get_bloginfo('url');
					$noteContent = esc_html($note->post_content)
					?>
					<li>
						<?php echo $noteContent ;?> <!-- print note content -->
						<span class="note-author"><?php echo '- by '.$author; ?></span><!-- print note author -->
						<?php echo "<a class='remove-note' href='" . wp_nonce_url( get_bloginfo('url') . "/wp-admin/post.php?action=delete&amp;post=" . $note->ID, 'delete-post_' . $note->ID) . "'></a>"; ?><!-- print delete button -->
					</li>
					<?php
		        endforeach;
		        wp_reset_postdata();
		    }
		    else { 
			    //in case there are no notes yet, It will show a message
			    echo _e('No notes submited yet','Notes');
		    } ?>
	    </ul><?php 
	}
	
	function notes_admin_page() { 
		//creating the admin page
		?> 
		<section id="notes-plugin">
		<h1 class="wp-heading-inline"><?php  _e('Notes','Notes') ?></h1>
		<div class="wrapper">
				<div class="span7">		
					<?php $this->renderForm(); ?>
					<?php $this->insertNote(); ?>
				</div>
				<div class="span5">
					<?php $this->showNotes();?>
				</div>
		 </div>
		</section>
	<?php 
	}

}


/**
 * Instantiate class, creating admin page
 */
 
global $NotesPage;
$notesPage = new NotesPage();





