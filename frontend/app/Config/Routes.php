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

/*
|--------------------------------------------------------------------------
| Auth Routes (LOGIN & REGISTER)
|--------------------------------------------------------------------------
*/
$routes->group('auth', static function ($routes) {

    $routes->get('login', 'Auth::login');
    $routes->get('register', 'Auth::register');

    $routes->post('login', 'Auth::doLogin');
    $routes->post('register', 'Auth::doRegister');

    // ⚠️ ROUTE LAMA TETAP ADA (TIDAK DIHAPUS)
    $routes->get('logout', 'Auth::logout');
});

/*
|--------------------------------------------------------------------------
| 🔥 LOGOUT GLOBAL (SOLUSI 404)
|--------------------------------------------------------------------------
| Bisa dipanggil dari:
| - /member/dashboard
| - /librarian/dashboard
| - sidebar mana pun
*/
$routes->get('logout', 'Auth::logout');

/*
|--------------------------------------------------------------------------
| Debug
|--------------------------------------------------------------------------
*/
$routes->get('debug', 'Debug::index');

/*
|--------------------------------------------------------------------------
| Alias (biar /transactions tetap jalan)
|--------------------------------------------------------------------------
*/
$routes->get('transactions', static fn() => redirect()->to('/librarian/transactions'));
$routes->get('transactions/(:segment)', static fn($any) => redirect()->to('/librarian/transactions/' . $any));

/*
|--------------------------------------------------------------------------
| Librarian Routes (TETAP, TIDAK DIHAPUS)
|--------------------------------------------------------------------------
*/
$routes->group('librarian', ['filter' => 'auth'], static function ($routes) {

    $routes->get('dashboard', 'Librarian\Dashboard::index', [
        'filter' => 'role:librarian'
    ]);

    // Books
    $routes->get('books', 'Librarian\Books::index', ['filter' => 'role:librarian']);
    $routes->post('books', 'Librarian\Books::store', ['filter' => 'role:librarian']);
    $routes->post('books/(:num)/update', 'Librarian\Books::update/$1', ['filter' => 'role:librarian']);
    $routes->post('books/(:num)/delete', 'Librarian\Books::delete/$1', ['filter' => 'role:librarian']);

    // Members
    $routes->get('members', 'Librarian\Members::index', ['filter' => 'role:librarian']);
    $routes->get('members/new', 'Librarian\Members::new', ['filter' => 'role:librarian']);
    $routes->post('members', 'Librarian\Members::create', ['filter' => 'role:librarian']);
    $routes->get('members/(:num)', 'Librarian\Members::show/$1', ['filter' => 'role:librarian']);
    $routes->get('members/(:num)/edit', 'Librarian\Members::edit/$1', ['filter' => 'role:librarian']);
    $routes->post('members/(:num)/update', 'Librarian\Members::update/$1', ['filter' => 'role:librarian']);
    $routes->post('members/(:num)/delete', 'Librarian\Members::delete/$1', ['filter' => 'role:librarian']);

    // Transactions
    $routes->group('transactions', ['filter' => 'role:librarian'], static function ($routes) {
        $routes->get('/', 'Librarian\Transactions::index');
        $routes->post('(:num)/return', 'Librarian\Transactions::markReturned/$1');
    });

    $routes->group('member', ['filter' => 'auth'], static function ($routes) {
        $routes->get('books', 'Member\BookController::index');
        $routes->get('books/(:num)', 'Member\BookController::detail/$1');
    });
});

$routes->group('member', ['filter' => 'auth'], static function ($routes) {

    $routes->get('dashboard', 'Member\Dashboard::index', [
        'filter' => 'role:member'
    ]);

    $routes->get('books', 'Member\BookController::index', [
        'filter' => 'role:member'
    ]);

    $routes->get('books/(:num)', 'Member\BookController::detail/$1', [
        'filter' => 'role:member'
    ]);
});

$routes->group('member', function ($routes) {
    $routes->get('caribuku', 'Member\BookController::index');
    $routes->get('books/detail/(:num)', 'Member\BookController::detail/$1');
});

$routes->group('member', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Member\DashboardController::index');

    // Fitur Buku & Search
    $routes->get('books', 'Member\BookController::index');

    // Fitur Detail Buku (INI YG BIKIN 404 KALAU TIDAK ADA)
    $routes->get('books/detail/(:num)', 'Member\BookController::detail/$1');
});

