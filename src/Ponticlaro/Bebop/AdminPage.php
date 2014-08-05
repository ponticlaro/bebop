<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\AdminPage\Tab;

class AdminPage
{
    /**
     * Configuration parameters
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $config;

    /**
     * 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $tabs;

    /**
     * Instantiates a new Admin page
     * 
     * @param string   $title    Admin page tile. Used to create its access slug
     * @param callable $function Function to call that will contain all the page logic
     */
    public function __construct($title, $function = null)
    {
        // Set config object with default configuration
        $this->config = Bebop::Collection(array(
            'page_title' => '',
            'menu_title' => '',
            'capability' => 'manage_options',
            'menu_slug'  => '',
            'function'   => '',
            'icon_url'   => '',
            'position'   => null,
            'url'        => '',
            'parent'     => ''
        ));

        // Set tabs object
        $this->tabs = Bebop::Collection();

        // Set Page Title
        if ($title)
            $this->setPageTitle($title);

        // Set Function
        if ($function)
            $this->setFunction($function);

        // Register Admin Page
        add_action('admin_menu', array($this, 'handleRegistration'));
    }

    /**
     * Sets page title
     * 
     * @param string $title
     */
    public function setPageTitle($title)
    {
        if (!is_string($title))
            throw new \Exception('AdminPage title must be a string');

        $this->config->set('page_title', $title);

        if (!$this->getMenuTitle())
            $this->setMenuTitle($title);

        if (!$this->getMenuSlug())
            $this->setMenuSlug($title);

        return $this;
    }

    /**
     * Returns page title
     * 
     * @return string
     */
    public function getPageTitle()
    {
        return $this->config->get('page_title');
    }

    /**
     * Sets menu title
     * 
     * @param string $title
     */
    public function setMenuTitle($title)
    {
        if (!is_string($title))
            throw new \Exception('AdminPage menu title must be a string');

        $this->config->set('menu_title', $title);

        return $this;
    }

    /**
     * Returns menu title
     * 
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->config->get('menu_title');
    }

    /**
     * Sets menu slug
     * 
     * @param string $slug
     */
    public function setMenuSlug($slug)
    {
        if (!is_string($slug))
            throw new \Exception('AdminPage menu slug must be a string');

        $slug = Bebop::util('slugify', $slug, array('separator' => '-'));

        $this->config->set('menu_slug', $slug);

        if (!$this->config->get('url'))
            $this->config->set('url', '/wp-admin/admin.php?page='. $slug);

        return $this;
    }

    /**
     * Returns menu slug
     * 
     * @return string
     */
    public function getMenuSlug()
    {
        return $this->config->get('menu_slug');
    }

    /**
     * Sets parent page
     * 
     * @param string $parent
     */
    public function setParent($parent)
    {
        if (!is_string($parent))
            throw new \Exception('AdminPage parent must be a string');

        $this->config->set('parent', $parent);

        return $this;
    }

    /**
     * Returns parent
     * 
     * @return string
     */
    public function getParent()
    {
        return $this->config->get('parent');
    }

    /**
     * Sets capability
     * 
     * @param string $capability
     */
    public function setCapability($capability)
    {
        if (!is_string($capability))
            throw new \Exception('AdminPage capability must be a string');

        $this->config->set('capability', $capability);

        return $this;
    }

    /**
     * Returns capability
     * 
     * @return string
     */
    public function getCapability()
    {
        return $this->config->get('capability');
    }

    /**
     * Sets function
     * 
     * @param callable $fn
     */
    public function setFunction($function)
    {   
        if (!is_callable($function))
            throw new \Exception('AdminPage function must be callable');
            
        $this->config->set('function', $function);

        return $this;
    }

    /**
     * Returns function
     * 
     * @return callable
     */
    public function getFunction()
    {
        return $this->config->get('function');
    }

    /**
     * Sets position
     * 
     * @param mixed $position
     */
    public function setPosition($position)
    {   
        if (!is_string($position) && !is_integer($position))
            throw new \Exception('AdminPage position must be either a string or an integer');
            
        $this->config->set('position', $position);

        return $this;
    }

    /**
     * Returns position
     * 
     * @return mixed
     */
    public function getPosition()
    {
        return $this->config->get('position');
    }

    /**
     * Sets icon url
     * 
     * @param mixed $url
     */
    public function setIconUrl($url)
    {   
        if (!is_string($url))
            throw new \Exception('AdminPage icon url must be a string');
            
        $this->config->set('icon_url', $url);

        return $this;
    }

    /**
     * Returns icon url
     * 
     * @return string
     */
    public function getIconUrl()
    {
        return $this->config->get('icon_url');
    }

    /**
     * Adds a single tab
     * 
     * @param string   $title    Tab title
     * @param callable $function Tab function
     */
    public function addTab($title, $function)
    {
        if (is_string($title) && is_callable($function)) {

            $this->tabs->push(new Tab($title, $function));
        }

        return $this;
    }

    /**
     * Returns all tabs
     * 
     * @return array
     */
    public function getTabs()
    {
        return $this->tabs->getAll();
    }

    /**
     * Defines which function should be used based on the settings provided
     * See http://codex.wordpress.org/Administration_Menus
     * 
     * @return [type] [description]
     */
    public function handleRegistration()
    {
        $parent = $this->getParent();

        if ($parent) {

            switch ($parent) {
                case 'dashboard':
                    $fn = 'add_dashboard_page';
                    break;

                case 'posts':
                    $fn = 'add_posts_page';
                    break;

                case 'pages':
                    $fn = 'add_pages_page';
                    break;

                case 'media':
                    $fn = 'add_media_page';
                    break;

                case 'links':
                    $fn = 'add_links_page';
                    break;

                case 'comments':
                    $fn = 'add_comments_page';
                    break;

                case 'theme':
                    $fn = 'add_theme_page';
                    break;

                case 'plugins':
                    $fn = 'add_plugins_page';
                    break;

                case 'users':
                    $fn = 'add_users_page';
                    break;

                case 'tools':
                    $fn = 'add_management_page';
                    break;

                case 'settings':
                    $fn = 'add_options_page';
                    break;
                
                default:
                    $fn = 'add_submenu_page';
                    break;
            }

            if ($fn == 'add_submenu_page') {

                $fn(
                    $this->getParent(),
                    $this->getPage_title(), 
                    $this->getMenu_title(), 
                    $this->getCapability(), 
                    $this->getMenuSlug(), 
                    array($this, 'baseHtml')
                );
            }

            else {

                $fn(
                    $this->getPageTitle(), 
                    $this->getMenuTitle(), 
                    $this->getCapability(), 
                    $this->getMenuSlug(), 
                    array($this, 'baseHtml')
                );
            }
        }

        else {

            add_menu_page(
                $this->getPageTitle(), 
                $this->getMenuTitle(), 
                $this->getCapability(), 
                $this->getMenuSlug(), 
                array($this, 'baseHtml'), 
                $this->getIconUrl(), 
                $this->getPosition() 
            );
        }   
    }

    /**
     * Removes this admin page
     * 
     * @return void
     */
    public function destroy()
    {
        remove_menu_page($this->getMenuSlug());
    }

    /**
     * Checks if the user have permission to access this page
     * 
     * @return void
     */
    protected function __checkPermissions()
    {
        if (!current_user_can($this->getCapability()))
            wp_die(__( 'You do not have sufficient permissions to access this page.'));
    }

    /**
     * Base Html for any administration page
     * Executes the function passed in the "function" parameter
     * 
     * @return void
     */
    public function baseHtml()
    { 
        // Check if currently authenticated user can access this page
        $this->__checkPermissions(); ?>

        <div class="wrap">
            <h2><?php echo $this->getPageTitle(); ?></h2>
            
            <form method="post" action="options.php">
                <?php 

                settings_errors();

                $tabs     = $this->tabs->getAll();
                $function = $this->getFunction();

                if ($tabs) { 

                    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $tabs[0]->getId();

                    ?>
                    
                    <h2 class="nav-tab-wrapper">
                        <?php foreach ($tabs as $tab) { ?>
                            <a href="<?php echo $this->config->get('url') . '&tab=' . $tab->getId(); ?>" class="nav-tab<?php if ($current_tab == $tab->getId()) echo ' nav-tab-active'; ?>">
                                <?php echo $tab->getTitle(); ?>
                            </a>
                        <?php } ?>
                    </h2>

                    <?php foreach ($tabs as $tab) {
                        
                        if ($current_tab == $tab->getId())
                            call_user_func_array($tab->getFunction(), array(array())); 
                    }
                }

                elseif ($function)
                    call_user_func_array($function, array(array())); 

                ?>
            <form>
            
        </div><!-- /.wrap -->
        
    <?php }
}