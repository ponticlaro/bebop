<?php

use Ponticlaro\Bebop;

wp_head();

var_dump(Bebop::Context()->getCurrent());
var_dump(Bebop::Context()->is('/single\/(product|post|page)/', true));

wp_footer();