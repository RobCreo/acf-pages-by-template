<?php
class acf_field_acf_pages_by_template extends acf_field {	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		// vars
		$this->name = 'acf_pages_by_template';
		$this->label = __( 'Pages by Template', 'acf-acf_pages_by_template' );
		$this->category = 'relational';
		$this->defaults = array(
			'page_template' => array( 'all' ),
			'multiple'      => 0,
			'allow_null'    => 0,
		);

		// do not delete!
		parent::__construct();
		
	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/

		$key = $field['name'];

		global $wpdb;
		$page_templates = get_page_templates();

		foreach ( $page_templates as $page_template_name => $page_template ) {
			$choices[$page_template] = $page_template_name;
		}

		// page_template
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Page template', 'acf-acf_pages_by_template' ),
			'instructions'	=> __( 'Choose for which template(s) you want to get pages', 'acf-acf_pages_by_template' ),
			'type'			=> 'select',
			'name'			=> 'page_template',
			'value'			=> $field['page_template'],
			'choices'       => $choices,
			'multiple'		=> 1,
		));

		// multiple
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Select multiple values?', 'acf-acf_pages_by_template' ),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'multiple',
			'choices'		=> array(
				1				=> __( 'Yes', 'acf' ),
				0				=> __( 'No', 'acf' ),
			),
			'layout'	=>	'horizontal',
		));

	}


	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {

		/*
		*  Review the data of $field.
		*  This will show what data is available
		*/
		
		// Change Field into a select
		$field['type'] = 'select';
		$field['ui'] = 0;
		$field['ajax'] = 0;
		$field['choices'] = array();

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'page',
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_status'      => array( 'publish', 'private', 'draft', 'inherit', 'future' ),
			'sort_column'      => 'menu_order, post_title',
			'sort_order'       => 'ASC',
			'suppress_filters' => false,
			'meta_key'         => '_wp_page_template',
			'meta_value'       => ''
		);


		$template_name_by_file = array();

		$page_templates = get_page_templates();
		foreach ( $page_templates as $template_name => $template_file ) {
			$template_name_by_file[$template_file] = $template_name;
		}

		foreach ( $field['page_template'] as $page_template ) {
			$template_name = $template_name_by_file[$page_template];
			// get pages
			$args['meta_value'] = $page_template;
			$pages = get_posts( $args );

			if ( $pages ) {
				foreach ( $pages as $post ) {
					// find page title. Could use get_the_title, but that uses get_post(), so I think this uses less Memory
					$title = '';
					$ancestors = get_ancestors( $post->ID, $post->post_type );

					if ( $ancestors ) {
						foreach ( $ancestors as $a ) {
							$title .= '–';
						}
					}

					$title .= ' ' . apply_filters( 'the_title', $post->post_title, $post->ID );

					// status
					if ( $post->post_status != 'publish' ) {
						$title .= " ($post->post_status)";
					}

					// WPML
					if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
						$title .= ' (' . ICL_LANGUAGE_CODE . ')';
					}

					// add to choices
					$field['choices'][ $template_name ][ $post->ID ] = $title;
				}
			}
		}

		// render
		acf_render_field( $field );
	}
}

// create field
new acf_field_acf_pages_by_template();