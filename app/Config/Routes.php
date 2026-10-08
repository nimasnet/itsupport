<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('login', 'AuthController::login');
$routes->post('login/process', 'AuthController::processLogin');
$routes->get('logout', 'AuthController::logout');

$routes->group('master', ['filter' => 'auth'], function($routes) {
    // Master VLAN
    $routes->get('vlan', 'MasterController::vlan');
    $routes->post('vlan/store', 'MasterController::storeVlan');
    $routes->get('vlan/delete/(:num)', 'MasterController::deleteVlan/$1');
    
    // Master NVR
    $routes->get('nvr', 'MasterController::nvr');
    $routes->post('nvr/store', 'MasterController::storeNvr');
    $routes->get('nvr/delete/(:num)', 'MasterController::deleteNvr/$1');
    
    // Master Nama CCTV
    $routes->get('nama', 'MasterController::nama');
    $routes->post('nama/store', 'MasterController::storeNama');
    $routes->get('nama/delete/(:num)', 'MasterController::deleteNama/$1');
});

// CCTV CRUD
$routes->group('cctv', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'CctvController::index');
    $routes->get('create', 'CctvController::create');
    $routes->get('viewer', 'CctvController::viewer');
    $routes->get('stream/(:num)', 'CctvController::streamProxy/$1');
    $routes->get('nvr-viewer', 'CctvController::nvrViewer');
    $routes->get('nvr-stream/(:num)/(:num)', 'CctvController::nvrStreamProxy/$1/$2');
    $routes->get('nvr-config', 'CctvController::nvrConfig');
    $routes->post('nvr-config-update', 'CctvController::nvrConfigUpdate');
    
    $routes->get('it-respon-actions', 'ItResponActionsController::index');
    $routes->post('it-respon-actions/update', 'ItResponActionsController::updateItRespon');
    $routes->post('store', 'CctvController::store');
    $routes->get('edit/(:num)', 'CctvController::edit/$1');
    $routes->post('update/(:num)', 'CctvController::update/$1');
    $routes->get('delete/(:num)', 'CctvController::delete/$1');
    $routes->post('bulkDelete', 'CctvController::bulkDelete');
    $routes->post('importExcel', 'CctvController::importExcel');
    $routes->get('downloadTemplate', 'CctvController::downloadTemplate');
    $routes->get('exportExcel', 'CctvController::exportExcel');
    $routes->get('exportExcel/(:any)', 'CctvController::exportExcelByNvr/$1');
    $routes->get('nvr', 'CctvController::cctvByNvr');
    $routes->get('search_api', 'CctvController::searchApi');

    // Hardisk Log Replacement
    $routes->get('hardisk-log', 'HardiskLogController::index');
    $routes->post('hardisk-log/store', 'HardiskLogController::store');
    $routes->post('hardisk-log/update/(:num)', 'HardiskLogController::update/$1');
    $routes->get('hardisk-log/delete/(:num)', 'HardiskLogController::delete/$1');
    $routes->get('hardisk-log/delete-record/(:num)/(:num)/(:num)', 'HardiskLogController::deleteRecord/$1/$2/$3');
    $routes->post('hardisk-log/bulk-delete', 'HardiskLogController::bulkDelete');
    $routes->post('hardisk-log/importExcel', 'HardiskLogController::importExcel');
    $routes->get('hardisk-log/exportExcel', 'HardiskLogController::exportExcel');
    $routes->get('hardisk-log/downloadTemplate', 'HardiskLogController::downloadTemplate');
    
    // Hardisk Manage
    $routes->get('hardisk-manage', 'HardiskManageController::index');
    $routes->post('hardisk-manage/save', 'HardiskManageController::save');
});

// Checklist Status CCTV
$routes->group('checklist-cctv', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'ChecklistCctvController::index');
    $routes->get('create', 'ChecklistCctvController::create');
    $routes->post('store', 'ChecklistCctvController::store');
    $routes->get('edit/(:num)', 'ChecklistCctvController::edit/$1');
    $routes->post('update/(:num)', 'ChecklistCctvController::update/$1');
    $routes->get('delete/(:num)', 'ChecklistCctvController::delete/$1');
    $routes->post('bulk-delete', 'ChecklistCctvController::bulkDelete');
    $routes->post('importExcel', 'ChecklistCctvController::importExcel');
    $routes->get('exportExcel', 'ChecklistCctvController::exportExcel');
    $routes->get('downloadTemplate', 'ChecklistCctvController::downloadTemplate');
    $routes->post('getNamaCctv', 'ChecklistCctvController::getNamaCctv');
    $routes->post('getCctvsByNvr', 'ChecklistCctvController::getCctvsByNvr');
    $routes->post('update-it-respon', 'ChecklistCctvController::updateItRespon');
});

// Monitoring System
$routes->group('monitoring', ['filter' => 'auth'], function($routes) {
    $routes->get('cctv', 'MonitoringController::cctv');
    $routes->get('cctv/logs', 'MonitoringController::cctvLogs');
    $routes->post('cctv/logs/reset', 'MonitoringController::cctvLogsReset');
    $routes->get('cctv/logs/stats', 'MonitoringController::cctvLogsStats');
    $routes->get('cctv/logs/export', 'MonitoringController::cctvLogsExport');
    $routes->get('cctv/stats-chart', 'MonitoringController::cctvStatsChart');
    $routes->post('cctv/get-stats', 'MonitoringController::getCctvStats');
    $routes->get('cctv/setup', 'MonitoringController::cctvSetup');
    $routes->post('cctv/setup/save', 'MonitoringController::cctvSetupSave');
    $routes->get('cctv/setup/run', 'MonitoringController::cctvSetupRun');
    $routes->post('cctv/setup/run', 'MonitoringController::cctvSetupRun');
    
    $routes->get('device', 'MonitoringController::device');
    $routes->get('printer', 'MonitoringController::printer');
    $routes->get('cekPing', 'MonitoringController::cekPing');
    $routes->get('cekStatusDb', 'MonitoringController::cekStatusDb');
    $routes->get('getSetupIps', 'MonitoringController::getSetupIps');
    $routes->get('updateLastRun', 'MonitoringController::updateLastRun');
});

$routes->get('schedule-bell', 'ScheduleBellController::index', ['filter' => 'auth']);
$routes->get('schedule-bell/display', 'ScheduleBellController::display');
$routes->get('schedule-bell/print', 'ScheduleBellController::printView', ['filter' => 'auth']);
$routes->post('schedule-bell/store', 'ScheduleBellController::store', ['filter' => 'auth']);
$routes->post('schedule-bell/update/(:num)', 'ScheduleBellController::update/$1', ['filter' => 'auth']);
$routes->post('schedule-bell/inline-update', 'ScheduleBellController::inlineUpdate', ['filter' => 'auth']);
$routes->post('schedule-bell/duplicate/(:num)', 'ScheduleBellController::duplicate/$1', ['filter' => 'auth']);
$routes->post('schedule-bell/preset', 'ScheduleBellController::applyPreset', ['filter' => 'auth']);
$routes->post('schedule-bell/reorder', 'ScheduleBellController::reorder', ['filter' => 'auth']);
$routes->post('schedule-bell/toggle-holiday', 'ScheduleBellController::toggleHoliday', ['filter' => 'auth']);
$routes->get('schedule-bell/get-holidays', 'ScheduleBellController::getHolidays', ['filter' => 'auth']);
$routes->get('schedule-bell/get-profiles', 'ScheduleBellController::getProfiles', ['filter' => 'auth']);
$routes->post('schedule-bell/save-profile', 'ScheduleBellController::saveProfile', ['filter' => 'auth']);
$routes->post('schedule-bell/switch-profile', 'ScheduleBellController::switchProfile', ['filter' => 'auth']);
$routes->post('schedule-bell/generate-periodic', 'ScheduleBellController::generatePeriodic', ['filter' => 'auth']);
$routes->get('schedule-bell/logs', 'ScheduleBellController::getLogs', ['filter' => 'auth']);
$routes->get('schedule-bell/delete/(:num)', 'ScheduleBellController::delete/$1', ['filter' => 'auth']);
$routes->get('schedule-bell/export', 'ScheduleBellController::exportExcel', ['filter' => 'auth']);
$routes->post('schedule-bell/import', 'ScheduleBellController::importExcel', ['filter' => 'auth']);
$routes->get('schedule-bell/get-audio-files', 'ScheduleBellController::getAudioFiles', ['filter' => 'auth']);
$routes->post('schedule-bell/upload-audio', 'ScheduleBellController::uploadAudio', ['filter' => 'auth']);
$routes->post('schedule-bell/delete-audio', 'ScheduleBellController::deleteAudio', ['filter' => 'auth']);

// Tools System
$routes->group('tools', ['filter' => 'auth'], function($routes) {
    $routes->get('scan', 'ToolsController::scan');
    $routes->post('scan', 'ToolsController::scan');
    $routes->get('scan/ping', 'MonitoringController::cekPing');
    $routes->get('graphs', 'ToolsController::graphs');

    // Script Inject Manager
    $routes->get('script-inject', 'ScriptInjectController::index');
    $routes->post('script-inject/upload', 'ScriptInjectController::upload');
    $routes->get('script-inject/download/(:any)', 'ScriptInjectController::download/$1');
    $routes->get('script-inject/delete/(:any)', 'ScriptInjectController::delete/$1');
    $routes->post('script-inject/bulk-delete', 'ScriptInjectController::bulkDelete');
});

// IP List Management
$routes->group('ip-management', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'IpManagementController::index');
    $routes->get('user-pc', 'IpManagementController::userPc');
    $routes->get('switch-network', 'IpManagementController::switchNetwork');
    $routes->post('switch-network/store', 'IpManagementController::switchNetworkStore');
    $routes->post('switch-network/update/(:num)', 'IpManagementController::switchNetworkUpdate/$1');
    $routes->get('switch-network/delete/(:num)', 'IpManagementController::switchNetworkDelete/$1');
    $routes->post('switch-network/bulk-delete', 'IpManagementController::switchNetworkBulkDelete');
    $routes->post('switch-network/importExcel', 'IpManagementController::switchNetworkImportExcel');
    $routes->get('switch-network/exportExcel', 'IpManagementController::switchNetworkExportExcel');
    $routes->get('switch-network/downloadTemplate', 'IpManagementController::switchNetworkDownloadTemplate');
    $routes->get('cekrool', 'IpManagementController::cekrool');
    $routes->post('cekrool/store', 'IpManagementController::cekroolStore');
    $routes->post('cekrool/update/(:num)', 'IpManagementController::cekroolUpdate/$1');
    $routes->get('cekrool/delete/(:num)', 'IpManagementController::cekroolDelete/$1');
    $routes->post('cekrool/bulk-delete', 'IpManagementController::cekroolBulkDelete');
    $routes->post('cekrool/importExcel', 'IpManagementController::cekroolImportExcel');
    $routes->get('cekrool/exportExcel', 'IpManagementController::cekroolExportExcel');
    $routes->get('cekrool/downloadTemplate', 'IpManagementController::cekroolDownloadTemplate');
    $routes->get('downloadTemplate', 'IpManagementController::downloadTemplate');
    $routes->post('store', 'IpManagementController::store');
    $routes->post('update/(:num)', 'IpManagementController::update/$1');
    $routes->get('delete/(:num)', 'IpManagementController::delete/$1');
    $routes->post('bulk-delete', 'IpManagementController::bulkDelete');
    $routes->post('importExcel', 'IpManagementController::importExcel');
});

// STB Management
$routes->group('stb', ['filter' => 'auth'], function($routes) {
    $routes->get('mess', 'StbController::index');
    $routes->get('stb/kelola', 'StbController::kelolaStb');
    $routes->post('stb/save', 'StbController::save');
    $routes->get('stb/delete/(:num)', 'StbController::delete/$1');

    // Admin & Setting
    $routes->get('kelola', 'StbController::create');
    $routes->post('store', 'StbController::store');
    $routes->get('edit/(:num)', 'StbController::edit/$1');
    $routes->get('delete/(:num)', 'StbController::delete/$1');
    $routes->post('importExcel', 'StbController::importExcel');
    $routes->get('downloadTemplate', 'StbController::downloadTemplate');
    $routes->get('exportExcel', 'StbController::exportExcel');
});

// Security Monitoring
$routes->group('security', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'SecurityController::index');
    $routes->get('get-events', 'SecurityController::getEvents');
    $routes->post('save-panel', 'SecurityController::savePanel');
    $routes->get('delete-panel/(:num)', 'SecurityController::deletePanel/$1');
    $routes->post('remote-control', 'SecurityController::remoteControl');
});

// Monitoring Support
$routes->group('monitoring-support', ['filter' => 'auth'], function($routes) {
    $routes->get('ups-checklist', 'UpsChecklistController::index');
    $routes->post('ups-checklist/store', 'UpsChecklistController::store');
    
    $routes->get('ups-manage-list', 'UpsChecklistController::manageList');
    $routes->post('ups-manage-list/store', 'UpsChecklistController::storeLocation');
    $routes->get('ups-manage-list/delete/(:num)', 'UpsChecklistController::deleteLocation/$1');
});

// Wemos Dashboard & API
$routes->group('wemos', function($routes) {
    $routes->get('/', 'WemosController::index', ['filter' => 'auth']);
    $routes->get('apiGetLogs', 'WemosController::apiGetLogs', ['filter' => 'auth']);
    $routes->post('apiReceiveLog', 'WemosController::apiReceiveLog'); // API without auth for Arduino
});

// Admin Management
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->post('/', 'AdminController::index');
    $routes->post('storeUser', 'AdminController::storeUser');
    $routes->get('deleteUser/(:num)', 'AdminController::deleteUser/$1');
    $routes->post('updateAccess', 'AdminController::updateAccess');
    $routes->post('mainEdit', 'AdminController::mainEdit');
    $routes->post('pingControl', 'AdminController::pingControl');
    $routes->post('scheduleBypass', 'AdminController::scheduleBypass');
    $routes->post('defaultHomepage', 'AdminController::defaultHomepage');
    $routes->post('userDefaultHomepage', 'AdminController::userDefaultHomepage');
    $routes->get('generateBat', 'AdminController::generateBat');
    $routes->get('login-log', 'AdminController::loginLog');
});
