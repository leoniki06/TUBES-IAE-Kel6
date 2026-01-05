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
$routes->get('/', 'Auth::splash');

$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->get('register', 'Auth::register');

    $routes->post('login', 'Auth::doLogin');
    $routes->post('register', 'Auth::doRegister');

    $routes->get('logout', 'Auth::logout');
});

$routes->get('debug', 'Debug::index');

/*
|--------------------------------------------------------------------------
| Alias (biar /transactions tetap jalan)
|--------------------------------------------------------------------------
| Jadi kalau ada link lama /transactions, diarahkan ke /librarian/transactions
*/
$routes->get('transactions', static fn() => redirect()->to('/librarian/transactions'));
$routes->get('transactions/(:segment)', static fn($any) => redirect()->to('/librarian/transactions/' . $any));

/*
|--------------------------------------------------------------------------
| Librarian Routes
|--------------------------------------------------------------------------
*/
$routes->group('librarian', static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Librarian\Dashboard::index');

    // Books
    $routes->get('books', 'Librarian\Books::index');
    $routes->post('books', 'Librarian\Books::store');
    $routes->post('books/(:num)/update', 'Librarian\Books::update/$1');
    $routes->post('books/(:num)/delete', 'Librarian\Books::delete/$1');

    // Members
    $routes->get('members', 'Librarian\Members::index');
    $routes->get('members/new', 'Librarian\Members::new');
    $routes->post('members', 'Librarian\Members::create');
    $routes->get('members/(:num)', 'Librarian\Members::show/$1');
    $routes->get('members/(:num)/edit', 'Librarian\Members::edit/$1');
    $routes->post('members/(:num)/update', 'Librarian\Members::update/$1');
    $routes->post('members/(:num)/delete', 'Librarian\Members::delete/$1');

    // Transactions
    $routes->group('transactions', static function ($routes) {
        $routes->get('/', 'Librarian\Transactions::index');
        $routes->post('(:num)/return', 'Librarian\Transactions::markReturned/$1');
    });
});
