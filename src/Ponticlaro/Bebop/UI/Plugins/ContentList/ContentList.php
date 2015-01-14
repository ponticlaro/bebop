<?php

namespace Ponticlaro\Bebop\UI\Plugins\ContentList;

use Ponticlaro\Bebop;

class ContentList extends \Ponticlaro\Bebop\UI\PluginAbstract {

	/**
	 * Identifier Key to call this plugin
	 * 
	 * @var string
	 */
	protected static $__key = 'List';

	protected static $__base_url;

	protected static $__default_form;

	protected $__current_instance_key;

	public function __construct()
	{
		self::$__base_url = Bebop::util('getPathUrl', __DIR__);

		$args = func_get_args();

		if ($args) call_user_func_array(array($this, '__createInstance'), $args);
	}

	/**
	 * This function will register everything on the right hooks
	 * when the plugin is added to Bebop::UI
	 *  
	 * @return void
	 */
	public function load()
	{
		add_action('admin_enqueue_scripts', array($this, 'registerScripts'));
	}

	public function registerScripts()
	{
		// Register CSS
		$css_path         = 'ui/list/css/bebop-ui--list';
		$css_version      = Bebop::util('getFileVersion', Bebop::getPath('_bebop/static', $css_path .'.css'));
		$css_dependencies = array('bebop-ui', 'bebop-ui--media');

		wp_register_style('bebop-ui--list', Bebop::getUrl('_bebop/static', $css_path), $css_dependencies, $css_version);

		// Register development JS
		if (Bebop::isDevEnvEnabled()) {
			
			wp_register_script('bebop-ui--listView', Bebop::getUrl('_bebop/static', 'ui/list/js/views/List'), array(), false, true);
			wp_register_script('bebop-ui--listItemView', Bebop::getUrl('_bebop/static', 'ui/list/js/views/ListItemView'), array(), false, true);
			wp_register_script('bebop-ui--listItemModel', Bebop::getUrl('_bebop/static', 'ui/list/js/models/ListItemModel'), array(), false, true);
			wp_register_script('bebop-ui--listCollection', Bebop::getUrl('_bebop/static', 'ui/list/js/collections/ListCollection'), array(), false, true);

			$js_dependencies = array(
				'jquery',
				'jquery-ui-sortable',
				'underscore',
				'backbone',
				'mustache',
				'bebop-ui--media',
				'bebop-ui--listView',
				'bebop-ui--listItemView',
				'bebop-ui--listItemModel',
				'bebop-ui--listCollection',
			);
			
			wp_register_script('bebop-ui--list', Bebop::getUrl('_bebop/static', 'ui/list/js/bebop-ui--list'), $js_dependencies, false, true);
		}

		// Register optimized JS
		else {

			// The following dependencies should never be concatenated and minified
			// Some are use by other WordPress features and plugins
			// and other are register by Bebop UI
			$js_dependencies = array(
				'jquery',
				'jquery-ui-sortable',
				'underscore',
				'backbone',
				'mustache',
				'bebop-ui--media'
			);

			$js_path    = 'ui/list/js/bebop-ui--list.min';
			$js_version = Bebop::util('getFileVersion', Bebop::getPath('_bebop/static', $js_path .'.js'));

			wp_register_script('bebop-ui--list', Bebop::getUrl('_bebop/static', $js_path), $js_dependencies, $js_version, true);
		}
	}

	public function renderTemplates()
	{ 
		?>

		<div id="bebop-list--<?php echo $this->getFieldName(); ?>-templates-container">
			<script bebop-list--itemTemplate="main" class="bebop-list--item" type="text/template" style="display:none">

				<input bebop-list--el="data-container" type="hidden">
				
				<div class="bebop-list--drag-handle">
					<span class="bebop-ui-icon-move"></span>
				</div>
				
				<div bebop-list--el="content" class="bebop-ui-clrfix">

					<?php if ($this->isMode('gallery')) {

						\Ponticlaro\Bebop::UI()->Media('Image', '', array(
							'field_name' => 'id',
							'mime_types' => array(
								'image'
							)
						))->render();

					} ?>

					<div bebop-list--view="browse"></div>
					<div bebop-list--view="reorder"></div>
					<div bebop-list--view="edit"></div>
				</div>

				<div bebop-list--el="item-actions">
					<button bebop-list--action="edit" class="button button-small">
						<b>Edit</b>
						<span class="bebop-ui-icon-edit"></span>
					</button>
					<button bebop-list--action="remove" class="button button-small">
						<span class="bebop-ui-icon-remove"></span>
					</button>
				</div>
			</script>

			<?php 

			// Form Templates
			$forms = $this->getAllForms();

			if ($forms) {
				foreach ($forms as $form) { ?>
					 
					<script bebop-list--formTemplate="<?php echo $form->getId(); ?>" type="text/template" style="display:none">
						<?php echo $this->getFormHtml($form->getId()); ?>
					</script>

				<?php }
			}

			// Item Templates
			$items_views = $this->getAllItemViews();

			if ($items_views) {
				foreach ($items_views as $key => $template) { ?>
					 
					<script bebop-list--itemTemplate="<?php echo $key; ?>" type="text/template" style="display:none"><?php echo $template; ?></script>

				<?php }
			} ?>
		</div>

	<?php }

	private function __enqueueScripts()
	{
		global $wp_version;

		if (version_compare($wp_version, '3.5', '>=')) {
			
			// Enqueue media scripts ONLY if needed
			if (!did_action('wp_enqueue_media'))
				wp_enqueue_media();

		} else {

			// Handle WordPress lower than 3.5
		}

		wp_enqueue_style('bebop-ui--list');
		wp_enqueue_script('bebop-ui--list');
	}

	private function __createInstance($key, $data = array(), array $config = array())
	{	
		$this->__enqueueScripts();

		$title = $key;
		$key   = Bebop::util('slugify', $key);

		// Default main configuration
		$default_config = array(
			'key'              => $key,
			'title'            => $title,
			'description'      => '',
			'field_name'       => $key,
			'show_top_form'    => true,
			'show_bottom_form' => true,
			'type'             => 'single',
			'mode'             => 'default'
		);

		// Main configuration
		$this->config = Bebop::Collection(array_merge($default_config, $config));

		// Data
		$this->data = Bebop::Collection($data ?: array());

		// Views
		$this->views = Bebop::Collection(array(
			'browse'  => '',
			'reorder' => '',
			'edit'    => ''
		))->disableDottedNotation();

		// Labels
		$this->labels = Bebop::Collection(array(
			'add_button'  => 'Add Item',
			'sort_button' => 'Sort'
		))->disableDottedNotation();

		// Forms
		$this->forms = Bebop::Collection()->disableDottedNotation();

		// Add default form
		// This is inherited by user added forms without form elements
		$this->addForm('default', array(
			'add'  => __DIR__ .'/views/partials/form/default/elements/add.php',
			'sort' => __DIR__ .'/views/partials/form/default/elements/sort.php'
		));

		// Add main form
		$this->addForm('main', array(
			'add'  => __DIR__ .'/views/partials/form/default/elements/add.php',
			'sort' => __DIR__ .'/views/partials/form/default/elements/sort.php'
		));

		// Register templates on admin footer
		add_action('admin_footer', array($this, 'renderTemplates'));

		return $this;
	}

	public function setData(array $data = array())
	{
		$this->data->set($data);

		return $this;
	}

	public function getData()
	{
		return $this->data->getAll();
	}

	public function setTitle($title)
	{
		if (is_string($title)) 
			$this->config->set('title', $title);

		return $this;
	}

	public function getTitle()
	{
		return $this->config->get('title');
	}

	public function setDescription($description)
	{
		if (is_string($description)) 
			$this->config->set('description', $description);

		return $this;
	}

	public function getDescription()
	{
		return $this->config->get('description');
	}

	public function setFieldName($name)
	{
		if (is_string($name)) 
			$this->config->set('field_name', $name);

		return $this;
	}

	public function getFieldName()
	{
		return $this->config->get('field_name');
	}

	public function setLabel($key, $value)
	{	
		if (is_string($key) && is_string($value)) 
			$this->labels->set($key, $value);

		return $this;
	}

	public function getLabel($key)
	{	
		if (!is_string($key)) return '';

		return $this->labels->get($key);
	}

	public function setMode($mode)
	{	
		if (is_string($mode)) {

			$this->config->set('mode', $mode);

			if ($mode == 'gallery') {
				
				$this->labels->set('add_button', 'Add images');
			}
		}

		return $this;
	}

	public function getMode()
	{	
		return $this->config->get('mode');
	}

	public function isMode($mode)
	{
		return is_string($mode) && $this->config->get('mode') == $mode ? true : false;
	}

	public function addForm($id, array $elements = array())
	{
		$this->forms->set($id, new ContentListForm($id, $elements ?: $this->forms->get('default')->getAllElements()));

		return $this;
	}

	public function setFormElements($form_id, array $elements)
	{
		foreach ($elements as $element_id => $template) {
			
			$this->setFormElement($form_id, $element_id, $template);
		}

		return $this;
	}

	public function setFormElement($form_id, $element_id, $template)
	{
		if (!is_string($form_id) || !is_string($element_id))
			throw new \Exception("Form ID and Element ID must both be strings");

		// Add form if it does not exist
		if (!$this->forms->hasKey($form_id))
			$this->addForm($form_id);

		// Add element to form
		$this->forms->get($form_id)->addElement($element_id, $template);

		return $this;
	}

	public function addFormElements($form_id, array $elements)
	{
		foreach ($elements as $element_id => $template) {
			
			$this->addFormElement($form_id, $element_id, $template);
		}

		return $this;
	}

	public function addFormElement($form_id, $element_id, $template)
	{
		if (!is_string($form_id) || !is_string($element_id))
			throw new \Exception("Form ID and Element ID must both be strings");

		// Add form if it does not exist
		if (!$this->forms->hasKey($form_id))
			$this->addForm($form_id);

		// Add element to form
		$form = $this->forms->get($form_id);

		if ($form->hasElement($element_id))
			throw new \Exception("There is already a form element named '$element_id'. To replace it use setFormElement() instead.");
			
		$form->addElement($element_id, $template);

		return $this;
	}

	public function formHasElement($form_id, $element_id)
	{
		if (!is_string($form_id) || !is_string($element_id)) return false;

		return $this->forms->get($form_id) ? $this->forms->get($form_id)->hasElement($id) : falss;
	}

	public function removeFormElement($form_id, $element_id)
	{
		if (!is_string($form_id) || !is_string($element_id))
			throw new \Exception("Form ID and Element ID must both be strings");

		// Remove element from form
		if ($this->forms->hasKey($form_id))
			$this->forms->get($form_id)->removeElement($element_id);

		return $this;
	}

	public function clearFormElements($form_id)
	{
		// Remove element from form
		if ($this->forms->hasKey($form_id))
			$this->forms->get($form_id)->clearElements();

		return $this;
	}

	public function getForm($id)
	{
		if (!is_string($form_id))
			throw new \Exception("Form ID must both be a string");

		return $this->forms->get($id);
	}

	public function getAllForms()
	{
		return $this->forms->getAll();
	}

	public function setItemView($view, $template)
	{
		if(!is_string($view)) return $this;

		$this->views->set($view, $this->getHtml($template));

		return $this;
	}

	public function getItemView($view)
	{
		if(!is_string($view)) return $this;

		return $this->views->get($view);
	}

	public function getAllItemViews()
	{
		return $this->views->getAll();
	}

	//////////////////////////////////////////////
	// START OF OLD API                         //
	// Will be deprecated on next major version //
	//////////////////////////////////////////////
	public function clearForm()
	{
		$this->forms->get('main')->clearElements();

		return $this;
	}

	public function addFormEl($element_id, $template)
	{
		$this->forms->get('main')->addElement($element_id, $template);

		return $this;
	}

	public function prependFormEl($element_id, $template)
	{
		$this->forms->get('main')->addElement($element_id, $template);

		return $this;
	}

	public function appendFormEl($element_id, $template)
	{
		$this->forms->get('main')->addElement($element_id, $template);

		return $this;
	}

	public function replaceFormEl($element_id, $template)
	{
		$this->forms->get('main')->addElement($element_id, $template);

		return $this;
	}

	public function removeFormEl($element_id)
	{
		$this->forms->get('main')->removeElement($element_id);

		return $this;
	}

	public function showForms($value)
	{
		$this->showTopForm($value);
		$this->showBottomForm($value);

		return $this;
	}

	public function showTopForm($value)
	{
		if (is_bool($value))
			$this->config->set('show_top_form', $value);

		return $this;
	}

	public function showBottomForm($value)
	{
		if (is_bool($value))
			$this->config->set('show_bottom_form', $value);

		return $this;
	}
	//////////////////////////////////////////////
	// END OF OLD API                           //
	// Will be deprecated on next major version //
	//////////////////////////////////////////////

	public function getFormHtml($id)
	{
		$html     = '';
		$elements = $this->forms->get($id)->getAllElements();

		if ($elements) {
			
			foreach ($elements as $id => $template) {
				
				$html .= "<div bebop-list--formElementId='$id' class='bebop-list--formField'>";
				$html .= $this->getHtml($template);
				$html .= '</div>';
			}
		}

		return $html;
	}

	public function getHtml($source) 
	{
		if (is_callable($source)) {

			ob_start();
			call_user_func($source);
			$html = ob_get_contents();
			ob_end_clean();

		} elseif (is_file($source) && is_readable($source)) {

			ob_start();
			$this->__renderTemplate($source, $this);
			$html = ob_get_contents();
			ob_end_clean();

		} elseif (is_string($source)) {

			$html = $source;

		} else {

			$html = '';
		}

		return $html;
	}

	public function render()
	{
		// Force default reorder view if in gallery mode
		if ($this->isMode('gallery') && !$this->getItemView('reorder'))
			$this->setItemView('reorder', __DIR__ .'/views/partials/items/gallery/reorder.mustache');

		// Render list
		$this->__renderTemplate('default', $this);

		return $this;
	}

	private function __renderTemplate($template_name, $instance)
	{
		// Absolute path templates
		if (is_file($template_name) && is_readable($template_name)) {
			
			include $template_name;
		}

		// Main View Templates
		else {

			include __DIR__ . '/views/'. $template_name .'.php';
		}
	}
}