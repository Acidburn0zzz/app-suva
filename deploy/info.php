<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'suva';
$app['version'] = '2.3.20';
$app['release'] = '1';
$app['vendor'] = 'ClearCenter';
$app['packager'] = 'ClearCenter';
$app['license'] = 'Proprietary';
$app['license_core'] = 'Proprietary';
$app['description'] = lang('suva_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('suva_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('base_subcategory_settings');
$app['menu_enabled'] = FALSE;

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_only'] = TRUE;

$app['core_requires'] = array(
    'suva-client => 3.1.17',
    'app-events-core >= 1:2.3.0',
);

$app['core_file_manifest'] = array(
    'suvad.php'=> array('target' => '/var/clearos/base/daemon/suvad.php'),
    'onboot-event'=> array(
        'target' => '/var/clearos/events/onboot/suva',
        'mode' => '0755'
    ),
);

$app['core_directory_manifest'] = array(
    '/var/clearos/suva' => array( 'target' => '/var/clearos/suva' ),
);

