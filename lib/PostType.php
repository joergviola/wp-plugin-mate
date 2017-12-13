<?php


class Field {
	function __construct($posttype, $name, $label, $type, $renderer) {
		$this->name = $name;
		$this->label = $label;
		$this->type = $type;
		$this->renderer = $renderer;
		$this->linebreak = false;
		$posttype->addField($this);
	}

	public function render($post) {
		$renderer = $this->renderer;
		return $renderer($post);
	}

	public function linebreak() {
		$this->linebreak = true;
		return $this;
	}
}

class MetaBox {
	function __construct($post_type, $options) {
		$this->post_type = $post_type;
		$this->options = 		$args = array_merge([
			'columns' => 1
		],$options);
	}

	public function render($post) {
		$tmpl = $this->options['raw'] ? '../lib/views/metabox-raw.php' : '../lib/views/metabox.php';
		return $this->post_type->plugin->render($tmpl, [
			'post' => $post,
			'fields'=>$this->options['fields'],
			'columns'=>$this->options['columns']
		]);
	}
}

abstract class PostType {
	private $domain = "default";
	private $fields = [];
	private $metaboxes = [];

	function __construct($plugin, $name, $labels, $args) {

		$this->plugin = $plugin;
		$this->name = $name;

		$args = array_merge([
			'label'               => __( 'Unnamed',  $this->domain ),
			'description'         => __( 'Unnamed',  $this->domain ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title' ),
			// You can associate this CPT with a taxonomy or custom taxonomy.
			//'taxonomies'          => array( 'genres' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-chart-bar',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'register_meta_box_cb' => [$this, 'createMetaBoxes']
		], $args);

		register_post_type( $name, $args );

		add_action('save_post', [$this, 'savePost']);
	}

	protected function createLabels($singular, $plural) {
		return [
			"name"                => _x( $plural, "Post Type General Name", $this->domain ),
			"singular_name"       => _x( $singular, "Post Type Singular Name", $this->domain ),
			"menu_name"           => __( $plural, $this->domain ),
			"parent_item_colon"   => __( "Parent $singular", $this->domain ),
			"all_items"           => __( "All $plural", $this->domain ),
			"view_item"           => __( "View $singular", $this->domain ),
			"add_new_item"        => __( "Add New $singular", $this->domain ),
			"add_new"             => __( "Add New", $this->domain ),
			"edit_item"           => __( "Edit $singular", $this->domain ),
			"update_item"         => __( "Update $singular", $this->domain ),
			"search_items"        => __( "Search $singular", $this->domain ),
			"not_found"           => __( "Not Found", $this->domain ),
			"not_found_in_trash"  => __( "Not found in Trash", $this->domain ),
		];
	}

	protected function addMetaBox($options) {
		$this->metaboxes[] = new MetaBox($this, $options);
	}

	public function createMetaBoxes(WP_Post $post) {
		foreach ($this->metaboxes as $metabox) {
			add_meta_box($metabox->options['id'], $metabox->options['name'], function() use ($metabox, $post) {
				echo $metabox->render( $post );
			});
		}

	}

	public function addField(Field $field) {
		$this->fields[] = $field;
	}

	protected function createHTMLField($renderer) {
		return new Field($this, null, null, 'html', $renderer);
	}
	protected function createCheckboxField($name, $label, $def=false) {
		return new Field($this, $name, $label, 'checkbox',  function($post) use ($name, $def) {
			$value = get_post_meta($post->ID, $name, true);
			return $this->plugin->render('../lib/views/checkbox.php', [
				'name' => $name,
				'value' => $value
			]);
		});
	}


	protected function createTextField($name, $label, $def) {
		return new Field($this, $name, $label, 'text',  function($post) use ($name, $def) {
			$value = get_post_meta($post->ID, $name, true) ?: $def;
			return $this->plugin->render('../lib/views/text.php', [
				'name' => $name,
				'value' => $value
			]);
		});
	}

	protected function createSliderField($name, $label, $def, $min, $max, $step) {
		return new Field($this, $name, $label, 'slider',  function($post) use ($name, $def, $min, $max, $step) {
			$value = get_post_meta($post->ID, $name, true) ?: $def;
			return $this->plugin->render('../lib/views/slider.php', [
				'name' => $name,
				'value' => $value,
				'min' => $min,
				'max' => $max,
				'step' => $step
			]);
		});
	}

	protected function createSelectField($name, $label, $options) {
		return new Field($this, $name, $label, 'checkbox',  function($post) use ($name, $options) {
			return $this->plugin->render('../lib/views/select.php', [
				'name' => $name,
				'options' => $options,
				'selected' => get_post_meta($post->ID, $name, true)
			]);
		});
	}

	protected function createHiddenField($name, $def='') {
		return new Field($this, $name, null, 'hidden', function($post) use ($name, $def) {
			$value = get_post_meta($post->ID, $name, true) ?: $def;
			return '<input id="'.$name.'" name="'.$name.'" type="hidden" value="'.esc_attr($value).'">';
		});
	}
	public function savePost($post_id){
		$post = get_post($post_id);
		$is_revision = wp_is_post_revision($post_id);

		// Secure with nonce field check
		//if( ! check_admin_referer('table_nonce', 'table_nonce') )
		//	return;

		// Do not save meta for a revision or on autosave
		if ( $post->post_type != $this->name || $is_revision )
			return;

		foreach ($this->fields as $field) {
			error_log($field->name . "=> |" .$_POST[$field->name]. "|");
			update_post_meta($post_id, $field->name, trim($_POST[$field->name]));
		}
	}
	protected function includeAdminScripts($scripts) {
		add_action( 'admin_enqueue_scripts', function ($hook) use ($scripts) {

			if( in_array($hook, array('post.php', 'post-new.php') ) ){
				$screen = get_current_screen();

				if( is_object( $screen ) && $this->name == $screen->post_type ){

					$this->plugin->enqueueScripts($scripts);
				}
			}
		} );
	}

}