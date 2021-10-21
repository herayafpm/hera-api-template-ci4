<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->namespaceApiV1 = APP_NAMESPACE . "\Controllers\Api\V1";
$routes->group("api/{locale}/v1", ['namespace' => $routes->namespaceApiV1], function ($routes) {
	$routes->group('hera',function($routes){
		$routes->group("auth", ['namespace' => $routes->namespaceApiV1 . "\Auth"], function ($routes) {
			$routes->group("login", function ($routes) {
				$routes->post("", "LoginApi::login");
				$routes->post("validation", "LoginApi::validation");
			});
			$routes->post("registrasi", "RegistrasiApi::registrasi");
		});
		$routes->group("user", ['filter' => 'auth', 'namespace' => $routes->namespaceApiV1 . "\User"], function ($routes) {
			$routes->get("data", "UserApi::index");
			$routes->post("update_profil", "UserApi::update_profil");
			$routes->get("group", "GroupApi::index");
		});
		$routes->group("client", ['filter' => 'auth', 'namespace' => $routes->namespaceApiV1], function ($routes) {
			$routes->get("", "ClientApi::index");
			$routes->get("show/(:segment)", "ClientApi::show/$1");
			$routes->get("permission/(:segment)", "ClientApi::permission/$1");
			$routes->post("permission/(:segment)/save", "ClientApi::permissionSave/$1");
			$routes->get("restore/(:segment)", "ClientApi::restore/$1");
			$routes->post("", "ClientApi::create");
			$routes->post("(:segment)/update", "ClientApi::update/$1");
			$routes->post("(:segment)/delete", "ClientApi::delete/$1");
		});
		$routes->group("group", ['filter' => 'auth', 'namespace' => $routes->namespaceApiV1], function ($routes) {
			$routes->get("", "GroupApi::index");
			$routes->get("show/(:segment)", "GroupApi::show/$1");
			$routes->get("permission/(:segment)", "GroupApi::permission/$1");
			$routes->post("permission/(:segment)/save", "GroupApi::permissionSave/$1");
			$routes->get("restore/(:segment)", "GroupApi::restore/$1");
			$routes->post("", "GroupApi::create");
			$routes->post("(:segment)/update", "GroupApi::update/$1");
			$routes->post("(:segment)/delete", "GroupApi::delete/$1");
		});
		$routes->group("permission", ['filter' => 'auth', 'namespace' => $routes->namespaceApiV1], function ($routes) {
			$routes->get("", "PermissionApi::index");
			$routes->get("restore/(:segment)", "PermissionApi::restore/$1");
			$routes->post("", "PermissionApi::create");
			$routes->post("(:segment)/update", "PermissionApi::update/$1");
			$routes->post("(:segment)/delete", "PermissionApi::delete/$1");
		});
		$routes->group("users", ['filter' => 'auth', 'namespace' => $routes->namespaceApiV1], function ($routes) {
			$routes->get("", "UsersApi::index");
			$routes->get("show/(:segment)", "UsersApi::show/$1");
			$routes->get("group/(:segment)", "UsersApi::group/$1");
			$routes->post("group/(:segment)/save", "UsersApi::groupSave/$1");
			$routes->get("restore/(:segment)", "UsersApi::restore/$1");
			$routes->post("", "UsersApi::create");
			$routes->post("(:segment)/update", "UsersApi::update/$1");
			$routes->post("(:segment)/delete", "UsersApi::delete/$1");
		});
		$routes->group("usersallapp", ['filter' => 'auth', 'namespace' => $routes->namespaceApiV1], function ($routes) {
			$routes->get("", "UsersAllAppApi::index");
			$routes->get("show/(:segment)", "UsersAllAppApi::show/$1");
			$routes->get("group/(:segment)", "UsersAllAppApi::group/$1");
			$routes->post("group/(:segment)/save", "UsersAllAppApi::groupSave/$1");
		});
	});
});

$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
