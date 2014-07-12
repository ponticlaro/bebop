<?php

use \Ponticlaro\Bebop;

///////////////////////////
// Autoload dependencies //
///////////////////////////
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

////////////////
// Boot Bebop //
////////////////
Bebop::boot();

// Add development local host
Bebop::Env('development')->addHost('localhost');

// Enable development environment
if (Bebop::Env()->is('development')) Bebop::setDevEnv(true);

/////////////
// Scripts //
/////////////
$front_css = Bebop::CSS('front');
$back_css  = Bebop::CSS('back');

$front_css->register('main', 'assets/css/main.css')
          ->enqueue('main')
          ->onEnv('production', function($hook) {

             $hook->setBaseUrl('http://cdn.ponticlaro.com/bebop/');
             $hook->getFile('main')->setBaseUrl('http://designdesu.com');
          });

$back_css->register('admin-main', 'assets/css/admin-main.css')
         ->register('admin-module', 'assets/css/admin-module.css')
         ->enqueue('admin-main', 'admin-module')
         ->onEnv(array('production', 'development'), function($hook) {

             $hook->setBaseUrl('http://cdn.ponticlaro.com/bebop/');


         });

$front_js = Bebop::JS('front');
$back_js  = Bebop::JS('back');

$front_js//->deregister('jquery')
         //->register('jquery', 'assets/js/jquery.js')
         ->register('google.api', '//maps.googleapis.com/maps/api/js?key=AIzaSyAAZvy-SdMnZMljIcd3r4FRC9uKydrWoYU&sensor=false', array('jquery'))
         ->register('app', 'assets/js/app.js', array())
         ->enqueue('jquery', 'google.api', 'app')
         ->onEnv('production', function($hook) {

             $hook->setBaseUrl('http://cdn.ponticlaro.com/bebop/');

             $script = $hook->getFile('app');
             $script->setPath('assets/js/app.min.js')
                    ->setDependencies(array());
         });


$back_js->register('admin-app', 'assets/js/admin-app.js', array('jquery'))
        ->enqueue('admin-app')
        ->onEnv('production', function($hook) {

            $hook->setBaseUrl('http://cdn.ponticlaro.com/bebop/');

            $script = $hook->getFile('admin-app');
            $script->setPath('assets/js/admin-app.min.js')
                   ->setDependencies(array());
        });

////////////////////////
// Custom Admin Pages //
////////////////////////

// Custom Admin Page
Bebop::AdminPage('Custom Admin Page', function() {

    $collection = Bebop::Collection();

    $data = [
        'one',
        'two',
        'three',
        'four',
        'five',
        'six'
    ];

    $collection->set($data);

    var_dump($collection->get());

    $collection->push('seven');

    var_dump($collection->get());

    $collection->unshift('zero');

    var_dump($collection->get());
});

// Custom Admin Sub-Page
Bebop::AdminPage('Custom Admin Sub-page', function() {

}, array(
    'parent' => 'settings'
));

////////////////
// Post Types //
////////////////

// Product Post Type
$products_post_type = Bebop::PostType('Product', array(
    'has_archive' => true
));

////////////////
// Taxonomies //
////////////////

// Products: Manufacturer
Bebop::Taxonomy('Manufacturer', $products_post_type, array(
    'rewrite' => array(
        'slug' => 'products/manufacturer'
    )
));

Bebop::Taxonomy('Product Year', $products_post_type, array(
    'rewrite' => array(
        'slug' => 'products/year'
    )
));

///////////////
// Metaboxes //
///////////////

// Products: Manufacturer
Bebop::Metabox('Manufacturer', $products_post_type, array('color', 'agenda', 'image', 'images', 'videos', 'audio', 'gallery'), function($data, $post) {

    ?>

    <input type="text" name="color" value="<?php echo $data->get('color', true); ?>">
    
    <?php 

    // Bebop::UI()->List('Agenda', $data->get('agenda'))
    //            ->setItemView('browse', __DIR__ .'/list-browse.mustache')
    //            ->setItemView('edit', __DIR__ .'/list-edit.mustache')
    //            ->setItemView('sessions.browse', __DIR__ .'/sessions-browse.mustache')
    //            ->setItemView('sessions.edit', __DIR__ .'/sessions-edit.mustache')
    //            ->render();
 
    // echo '<br><br>';

    // Bebop::UI()->List('Gallery', $data->get('gallery'))
    //            ->setMode('gallery')
    //            ->setItemView('browse', __DIR__ .'/templates/gallery/images-browse.mustache')
    //            ->setItemView('edit', __DIR__ .'/templates/gallery/images-edit.mustache')
    //            ->render();

    // echo '<br><br>';

    // $images = Bebop::UI()->List('Images', $data->get('images'))->setMode('gallery');

    // $videos = Bebop::UI()->List('Videos', $data->get('videos'));

    // $audio = Bebop::UI()->List('Audio', $data->get('audio'));

    // Bebop::UI()->MultiList('Multi List')
    //            ->addList($images)
    //            ->addList($videos)
    //            ->addList($audio)
    //            ->render();

    // echo '<br><br>';

    Bebop::UI()->Media('Image', $data->get('image', true))->render();

    Bebop::UI()->Media('API Image')
               ->setApiResource('products/meta:get', $post->ID, 'api_image')
               ->render();

    
});

Bebop::API()->Routes()->add('test/param', 'get', 'test/:param', function($param) {
    
    return array($param);
});

class Product extends \Ponticlaro\Bebop\Mvc\Model {

    public static $type = 'product';
}

// $query    = new Ponticlaro\Bebop\Db\Query;   
// $products = $query->type(['product', 'service'])
//                   ->tax('manufacturer')->in(['ford'])
//                   ->tax('model')->in(['focus', 'mondeo', 'fiesta'])
//                   ->meta('year')->between(2006, 2014)
//                   ->meta('price')->in(6000)
//                   ->findAll();

// $products = Product::cat()->is('used')
//                    ->tax('manufacturer')->in(['ford', 'toyota'])
//                    ->tax('year')->between('2008', '2010')
//                    ->tax('fuel')->is('diesel')
//                    ->meta('color')->in(['blue', 'red', 'black'])
//                    ->meta('price')->lte(10000)
//                    ->date()->gt('2014-01-01')
//                    ->metaKey('price')
//                    ->order('meta_value', 'asc')
//                    ->findAll();

Product::onInit(function($product) {

    $product->name = $product->post_title;
});

Product::addLoadable('test', function($product) {

    $product->test = 'TEST: SINGLE';
});

Product::addLoadable('test2', function($product) {

    $product->test = 'TEST: ARCHIVE';
});

Product::onContext('single/product', function($product) {

    $product->load(['test']);
});

Product::onContext('archive/product', function($product) {

    $product->load(['test2']);
});