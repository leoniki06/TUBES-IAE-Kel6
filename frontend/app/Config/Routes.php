<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Public & Auth
$routes->get('/', 'Auth::splash');
$routes->get('logout', 'Auth::logout');

// Auth Test Routes (Untuk keperluan testing saja)
$routes->get('test-login', 'AuthTest::loginDummy');
$routes->get('test-logout', 'AuthTest::logoutDummy');


$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->get('register', 'Auth::register');
    $routes->post('login', 'Auth::doLogin');
    $routes->post('register', 'Auth::doRegister');
    $routes->get('logout', 'Auth::logout');
});

// Librarian Routes (Tetap diproteksi)
$routes->group('librarian', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Librarian\Dashboard::index', ['filter' => 'role:librarian']);
    $routes->group('books', ['filter' => 'role:librarian'], static function ($routes) {
        $routes->get('/', 'Librarian\Books::index');
        $routes->post('/', 'Librarian\Books::store');
        $routes->post('(:num)/update', 'Librarian\Books::update/$1');
        $routes->post('(:num)/delete', 'Librarian\Books::delete/$1');
    });
});

// Member Routes (FILTER DIHAPUS AGAR TIDAK MENTAL)
$routes->group('member', static function ($routes) {

    // Dashboard & Katalog (Tery)
    $routes->get('dashboard', 'Member\Dashboard::index');
    $routes->get('books', 'Member\BookController::index');
    $routes->get('books/detail/(:num)', 'Member\BookController::detail/$1');

    // Jobdesk 2 (Akses Langsung)
    $routes->get('borrowed', 'Member\BorrowController::borrowed');
    $routes->get('history', 'Member\BorrowController::history');
    $routes->get('return', 'Member\ReturnController::index');

    $routes->post('borrow/save', 'Member\BorrowController::save');
    $routes->post('return-process/(:num)', 'Member\BorrowController::processReturn/$1');

    // Pinjam Buku
    $routes->post('books/borrow/(:num)', 'Member\BookController::borrow/$1');

    // Alias Sidebar
    $routes->get('transactions', 'Member\BorrowController::history');
});