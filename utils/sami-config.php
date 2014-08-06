<?php

use Sami\Version\GitVersionCollection;

$dir = __DIR__ .'/../src';

$versions = GitVersionCollection::create($dir)
                                ->addFromTags('1.*')
                                ->add('master', 'master branch');

return new Sami\Sami($dir, array(
    'theme'                => 'enhanced',
    'title'                => 'Bebop API',
    'build_dir'            => __DIR__.'/../docs/%version%',
    'cache_dir'            => __DIR__.'/../docs/cache/%version%',
    'default_opened_level' => 1,
    'versions'             => $versions
));