<?php

	/* 
		Plugin Name: Currently Reading Book
		Plugin URI: http://wpplugz.is-leet.com
		Description: A simple plugin that shows the current book you are reading along with book preview and book information.
		Version: 1.1
		Author: Bostjan Cigan
		Author URI: http://bostjan.gets-it.net
		License: GPL v2
	*/ 

	// Wordpress formalities here ...
	
	// Lets register things
	register_activation_hook(__FILE__, 'currently_reading_book_install');
	register_deactivation_hook(__FILE__, 'currently_reading_book_uninstall');
	add_action('admin_menu', 'currently_reading_book_admin_menu_create');
	add_action('widgets_init', create_function('', 'return register_widget("currently_reading_book_widget");')); // Register the widget

	// Prepare the array for our DB variables
	function currently_reading_book_install() {
		
		$plugin_options = array(
			'book_isbn' => '',
			'book_title' => '',
			'book_author' => '',
			'book_cover' => '',
			'book_custom_msg' => '',
			'previous_book_title' => '',
			'before_reading_msg' => 'Before this one I was reading ',
			'book_preview' => '',
			'visibility_settings' => array(
				'show_isbn' => false,
				'show_title' => false,
				'show_preview' => false,
				'show_author' => false,
				'show_previous' => false,
				'show_custom_msg' => false,
				'show_powered_by' => false
			)
		);
		add_option('currently_reading_book_settings', $plugin_options);
	
	}

	function currently_reading_book_uninstall() {
		delete_option('currently_reading_book_settings');
	}

	// Create the admin menu
	function currently_reading_book_admin_menu_create() {
		add_options_page('Currently Reading Book Settings', 'Currently Reading Book', 'administrator', __FILE__, 'currently_reading_book_settings');
	}
	
	// Output this anywhere in the blog
	function currently_reading_book() {
		$crb_settings = get_option('currently_reading_book_settings');

		if($crb_settings['visibility_settings']['show_preview']) {

?>

			<a href="<?php echo $crb_settings['book_preview']; ?>">
	        <img src="<?php echo $crb_settings['book_cover'] ?>" /></a>

<?php

		}
			
		else {

?>

			<br />
	        <img src="<?php echo $crb_settings['book_cover'] ?>" />

<?php
		}

		if($crb_settings['visibility_settings']['show_custom_msg']) {
				
?>
			<br /><em> <?php echo stripslashes($crb_settings['book_custom_msg']); ?> </em><br />
<?php

		}

		if($crb_settings['visibility_settings']['show_title']) {
?>
			<br /><em><?php echo stripslashes($crb_settings['book_title']); ?></em>
<?php
		}
			
		if($crb_settings['visibility_settings']['show_author']) {
				
?>
			<br /><strong>Author:</strong> <?php echo stripslashes(htmlentities($crb_settings['book_author'])); ?>
<?php

		}

		if($crb_settings['visibility_settings']['show_isbn']) {
				
?>
			<br /><strong>ISBN:</strong> <?php echo stripslashes(htmlentities($crb_settings['book_isbn'])); ?>
<?php

		}

		if($crb_settings['visibility_settings']['show_previous']) {
				
?>
			<br /><br /><?php echo stripslashes($crb_settings['before_reading_msg']); ?><em> 

<?php 

		if(strlen($crb_settings['previous_book_title']) > 0) {
			echo stripslashes($crb_settings['previous_book_title']);
		}
		
		else {
			echo 'nothing';	
		}

?>
</em>.
<?php

		}

		if($crb_settings['visibility_settings']['show_powered_by']) {
				
?>
			<br /><br />Powered by <a href="http://wpplugz.is-leet.com">wpPlugz</a>.
            
<?php
	
		}

?>
            
<?php
	
	}
	

	// The plugin admin page
	function currently_reading_book_settings() {
		
		$crb_settings = get_option('currently_reading_book_settings');
		$message = '';

		if(isset($_POST['crb_isbn'])) {
			$message = 'Settings updated.';
			$isbn = html_entity_decode($_POST['crb_isbn']);
			$previous_book = html_entity_decode($_POST['crb_previous_book']);
			// Get the show settings
			$show_isbn = $_POST['crb_show_isbn'];
			$show_title = $_POST['crb_show_title'];
			$show_preview = $_POST['crb_show_preview'];
			$show_author = $_POST['crb_show_author'];
			$show_previous = $_POST['crb_show_previous'];
			$show_custom_msg = $_POST['crb_show_custom_msg'];
			$show_powered_by = $_POST['crb_show_powered_by'];

			$crb_settings['visibility_settings']['show_isbn'] = ($show_isbn) ? true : false;
			$crb_settings['visibility_settings']['show_title'] = ($show_title) ? true : false;
			$crb_settings['visibility_settings']['show_preview'] = ($show_preview) ? true : false;
			$crb_settings['visibility_settings']['show_author'] = ($show_author) ? true : false;
			$crb_settings['visibility_settings']['show_previous'] = ($show_previous) ? true : false;
			$crb_settings['visibility_settings']['show_custom_msg'] = ($show_custom_msg) ? true : false;
			$crb_settings['visibility_settings']['show_powered_by'] = ($show_powered_by) ? true : false;
			
			if($isbn != $crb_settings['book_isbn']) {
				$crb_settings['previous_book_title'] = $crb_settings['book_title'];
			}
			else {
				$crb_settings['previous_book_title'] = $previous_book;
			}
			$custom_msg = html_entity_decode($_POST['crb_custom_msg']);
			$before_msg = html_entity_decode($_POST['crb_before_msg']);
			$book = get_book_data($isbn);
			$crb_settings['book_custom_msg'] = $custom_msg;
			$crb_settings['book_isbn'] = $book->getISBN();
			$crb_settings['book_title'] = $book->getTitle();
			$crb_settings['book_author'] = $book->getAuthor();
			$crb_settings['book_cover'] = $book->getCoverURL();
			$crb_settings['book_preview'] = $book->getBookPreviewURL();
			$crb_settings['before_reading_msg'] = $before_msg;
			update_option('currently_reading_book_settings', $crb_settings);
		}

		$crb_settings = get_option('currently_reading_book_settings');
		
?>

		<div id="icon-options-general" class="icon32"></div><h2>Currently Reading Book Settings</h2>
<?php

		if(strlen($message) > 0) {
		
?>

			<div id="message" class="updated">
				<p><strong><?php echo $message; ?></strong></p>
			</div>

<?php
			
		}

?>
        
                <form method="post" action="">
				<table class="form-table">
					<tr>
						<th scope="row"><img src="<?php echo plugin_dir_url(__FILE__).'book.png'; ?>" height="96px" width="96px" /></th>
						<td>
							<p>Thank you for using this plugin.</p> 
                            <p>That means you wanted to have the same thing on your blog than me, to tell your readers what you're currently reading.</p>
							<p>Visit the official website @ <a href="http://wpplugz.is-leet.com">wpPlugz</a>.</p>
                        </td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_isbn">ISBN</label></th>
						<td>
							<input type="text" name="crb_isbn" value="<?php echo stripslashes(htmlentities($crb_settings['book_isbn'])); ?>" />
							<br />
            				<span class="description">13 digit number on the back of the book, <a href="http://en.wikipedia.org/wiki/Isbn">read more</a>.</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="crb_isbn">Your book message</label></th>
						<td>
		                    <textarea rows="5" cols="50" name="crb_custom_msg"><?php echo stripslashes(htmlentities($crb_settings['book_custom_msg'])); ?></textarea>		
							<br />
            				<span class="description">A custom message that will be outputted in the widget (thoughts on the book etc.).</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="crb_before_msg">Previous book prefix</label></th>
						<td>
							<input size="50" type="text" name="crb_before_msg" value="<?php echo stripslashes(htmlentities($crb_settings['before_reading_msg'])); ?>" />
                            <br /> 
            				<span class="description">If your prefix is 'Before this I was reading', the ouput would be 'Before this I was reading Game Coding Complete'.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_isbn">Show ISBN</label></th>
						<td>
		    	            <input type="checkbox" name="crb_show_isbn" value="true" <?php if($crb_settings['visibility_settings']['show_isbn'] == true) { ?>checked="checked"<?php } ?> />
							<br />
            				<span class="description">Check to show ISBN in widget output.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_title">Show Title</label></th>
						<td>
    		    	        <input type="checkbox" name="crb_show_title" value="true" <?php if($crb_settings['visibility_settings']['show_title'] == true) { ?>checked="checked"<?php } ?> />
							<br />
            				<span class="description">Check to show book title in widget output.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_author">Show Book Author</label></th>
						<td>
							<input type="checkbox" name="crb_show_author" value="true" <?php if($crb_settings['visibility_settings']['show_author'] == true) { ?>checked="checked"<?php } ?> />							
                            <br />
            				<span class="description">Check to show book author in widget output.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_preview">Show Book Preview</label></th>
						<td>
    		    	  		<input type="checkbox" name="crb_show_preview" value="true" <?php if($crb_settings['visibility_settings']['show_preview'] == true) { ?>checked="checked"<?php } ?> />
							<br />
            				<span class="description">If checked, the book cover will link to a webpage with the book preview.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_previous">Show Previous book</label></th>
						<td>
							<input type="checkbox" name="crb_show_previous" value="true" <?php if($crb_settings['visibility_settings']['show_previous'] == true) { ?>checked="checked"<?php } ?> />							
                            <br />
            				<span class="description">Check to show the previous book title in widget output.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_custom_msg">Show Custom Message</label></th>
						<td>
		  					<input type="checkbox" name="crb_show_custom_msg" value="true" <?php if($crb_settings['visibility_settings']['show_custom_msg'] == true) { ?>checked="checked"<?php } ?> /> 							
                            <br />
            				<span class="description">Check to show your custom book message.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="crb_show_powered_by">Show Powered by Message</label></th>
						<td>
		  					<input type="checkbox" name="crb_show_powered_by" value="true" <?php if($crb_settings['visibility_settings']['show_powered_by'] == true) { ?>checked="checked"<?php } ?> />
                            <br />
                            <span class="description">Check to show 'Powered by wpPlugz' in widget output (optional, if you decide to check it, thank you for your support).</span>
						</td>
					</tr>		
				</table>					
				<p><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Update options') ?>" /></p>
				</form>


<?

	}
	
	// Get the book data using Google Books API
	function get_book_data($isbn) {
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://www.googleapis.com/books/v1/volumes?q=ISBN".$isbn);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($curl);
		curl_close($curl);
		$book_array = (array) json_decode($result, true);
		$book_title = $book_array["items"][0]["volumeInfo"]["title"];
		$book_author = $book_array["items"][0]["volumeInfo"]["authors"][0];
		$book_cover_url = $book_array["items"][0]["volumeInfo"]["imageLinks"]["thumbnail"];
		$book_isbn = $book_array["items"][0]["volumeInfo"]["industryIdentifiers"][1]["identifier"]; // ISBN13
		$book_preview_url = $book_array["items"][0]["accessInfo"]["webReaderLink"];

		$book = new Book($book_title, $book_isbn, $book_author, $book_cover_url, $book_preview_url);

		return $book;
		
	}
		
	// The book constructor, simple object programming here
	class Book {
		
		private $book_title;
		private $book_isbn;
		private $author;
		private $cover_url;
		private $book_preview_url;

		public function __construct($book_title, $book_isbn, $author, $cover_url, $book_preview_url) {
			$this->author = $author;
			$this->book_isbn = $book_isbn;
			$this->cover_url = $cover_url;
			$this->book_title = $book_title;
			$this->book_preview_url = $book_preview_url;
		}

		public function getTitle() {
			return $this->book_title;
		}

		public function getISBN() {
			return $this->book_isbn;
		}

		public function getAuthor() {
			return $this->author;
		}

		public function getCoverURL() {
			return $this->cover_url;
		}
		
		public function getBookPreviewURL() {
			return $this->book_preview_url;	
		}
		
	}
	
	// Here, the widget code begins
	class currently_reading_book_widget extends WP_Widget {
		
		function currently_reading_book_widget() {
			parent::WP_Widget(false, $name="Currently Reading Book");
		}
		
		function widget($args, $instance) {
			
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			
			echo $before_widget;

			if($title) {
				echo $before_title . $title . $after_title;
			}
			
			// The widget code and the widgeet output
			
			currently_reading_book();
			
			// End of widget output
			
			echo $after_widget;
			
		}
		
	    function update($new_instance, $old_instance) {		
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
	        return $instance;
    	}
		
		function form($instance) {	

        	$title = esc_attr($instance['title']);
		
?>

			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">
					<?php _e('Title: '); ?>
	            </label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

<?php 

		}

	}
	
?>
