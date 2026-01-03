<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

/*
|--------------------------------------------------------------------------
| Router Setup
|--------------------------------------------------------------------------
*/
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
$routes->get('/', static fn() => redirect()->to('/auth/login'));

$routes->group('auth', static function ($routes) {
    // pages
    $routes->get('login',    'Auth::login');
    $routes->get('register', 'Auth::register');

    // actions
    $routes->post('login',    'Auth::doLogin');
    $routes->post('register', 'Auth::doRegister');

    $routes->get('logout', 'Auth::logout');
});

$routes->get('debug', 'Debug::index');

/*
|--------------------------------------------------------------------------
| Librarian Only (Login + Role librarian)
|--------------------------------------------------------------------------
*/
$routes->group('librarian', static function ($routes) {

    // dashboard
    $routes->get('dashboard', 'Librarian\Dashboard::index');

    // Books (pakai view: librarian/Books/*)
    $routes->get('books', 'Librarian\Books::index');
    $routes->get('books/create', 'Librarian\Books::create');
    $routes->post('books', 'Librarian\Books::store');
    $routes->get('books/(:num)', 'Librarian\Books::show/$1');
    $routes->get('books/(:num)/edit', 'Librarian\Books::edit/$1');
    $routes->post('books/(:num)', 'Librarian\Books::update/$1');
    $routes->post('books/(:num)/delete', 'Librarian\Books::destroy/$1');

    // ✅ Members (librarian) -> jadi /librarian/members
    $routes->get('members', 'Members::index');
    $routes->get('members/create', 'Members::create');
    $routes->get('members/(:num)', 'Members::show/$1');
    $routes->get('members/(:num)/edit', 'Members::edit/$1');

    // ✅ Transactions (librarian) -> jadi /librarian/transactions
    $routes->get('transactions', 'Librarian\Transactions::index');
    $routes->get('transactions/(:num)', 'Librarian\Transactions::show/$1');
});
