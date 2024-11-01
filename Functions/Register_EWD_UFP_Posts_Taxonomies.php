<?php
add_action( 'init', 'EWD_UFP_Create_Posttypes' );
function EWD_UFP_Create_Posttypes() {
		$labels = array(
				'name' => __('Forms', 'ultimate-forms'),
				'singular_name' => __('Form', 'ultimate-forms'),
				'menu_name' => __('Forms', 'ultimate-forms'),
				'add_new' => __('Add New', 'ultimate-forms'),
				'add_new_item' => __('Add New Form', 'ultimate-forms'),
				'edit_item' => __('Edit Form', 'ultimate-forms'),
				'new_item' => __('New Form', 'ultimate-forms'),
				'view_item' => __('View Form', 'ultimate-forms'),
				'search_items' => __('Search Forms', 'ultimate-forms'),
				'not_found' =>  __('Nothing found', 'ultimate-forms'),
				'not_found_in_trash' => __('Nothing found in Trash', 'ultimate-forms'),
				'parent_item_colon' => ''
		);

		$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'has_archive' => true,
				'menu_icon' => null,
				'rewrite' => array('slug' => 'form'),
				'capability_type' => 'post',
				'menu_position' => null,
				'menu_icon' => 'dashicons-format-status',
				'supports' => array('title','editor')
	  );

	register_post_type( 'ufp_form' , $args );

	$labels = array(
				'name' => __('Form Elements', 'ultimate-forms'),
				'singular_name' => __('Form Element', 'ultimate-forms'),
				'menu_name' => __('Form Elements', 'ultimate-forms'),
				'add_new' => __('Add New', 'ultimate-forms'),
				'add_new_item' => __('Add New Form Element', 'ultimate-forms'),
				'edit_item' => __('Edit Form Element', 'ultimate-forms'),
				'new_item' => __('New Form Element', 'ultimate-forms'),
				'view_item' => __('View Form Element', 'ultimate-forms'),
				'search_items' => __('Search Form Elements', 'ultimate-forms'),
				'not_found' =>  __('Nothing found', 'ultimate-forms'),
				'not_found_in_trash' => __('Nothing found in Trash', 'ultimate-forms'),
				'parent_item_colon' => ''
		);

		$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => false,
				'has_archive' => false,
				'menu_icon' => null,
				'rewrite' => array('slug' => 'form_element'),
				'capability_type' => 'post',
				'menu_position' => null,
				'menu_icon' => 'dashicons-format-status',
				'supports' => array('title','editor')
	  );

	register_post_type( 'ufp_form_element' , $args );
}

?>