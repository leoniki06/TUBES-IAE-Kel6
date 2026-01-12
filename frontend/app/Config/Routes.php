<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// --------------------------------------------------------------------
// Router Setup
// --------------------------------------------------------------------
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// --------------------------------------------------------------------
// Public & Auth
// --------------------------------------------------------------------
$routes->get('/', 'Auth::splash');
$routes->get('logout', 'Auth::logout');

// Auth Test Routes (testing only)
$routes->get('test-login', 'AuthTest::loginDummy');
$routes->get('test-logout', 'AuthTest::logoutDummy');

$routes->group('auth', static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->get('register', 'Auth::register');

    $routes->post('login', 'Auth::doLogin');
    $routes->post('register', 'Auth::doRegister');

    $routes->get('logout', 'Auth::logout');
});

// --------------------------------------------------------------------
// Librarian Routes (PROTECTED)
// --------------------------------------------------------------------
$routes->group('librarian', ['filter' => 'auth'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Librarian\Dashboard::index', ['filter' => 'role:librarian']);

    // Books
    $routes->get('books', 'Librarian\Books::index', ['filter' => 'role:librarian']);
    $routes->post('books', 'Librarian\Books::store', ['filter' => 'role:librarian']);
    $routes->post('books/(:num)/update', 'Librarian\Books::update/$1', ['filter' => 'role:librarian']);
    $routes->post('books/(:num)/delete', 'Librarian\Books::delete/$1', ['filter' => 'role:librarian']);

    // ✅ MEMBERS (FIX 404)
    $routes->get('members', 'Librarian\Members::index', ['filter' => 'role:librarian']);
    $routes->post('members/(:num)/delete', 'Librarian\Members::delete/$1', ['filter' => 'role:librarian']); // nonaktif

    // ✅ TRANSACTIONS (FIX 404)
    $routes->get('transactions', 'Librarian\Transactions::index', ['filter' => 'role:librarian']);
    $routes->post('transactions/(:num)/return', 'Librarian\Transactions::markReturned/$1', ['filter' => 'role:librarian']);
});

// --------------------------------------------------------------------
// Member Routes (PROTECTED)
// --------------------------------------------------------------------
$routes->group('member', ['filter' => 'auth'], static function ($routes) {

    $routes->get('dashboard', 'Member\Dashboard::index', ['filter' => 'role:member']);

    $routes->get('books', 'Member\BookController::index', ['filter' => 'role:member']);
    $routes->get('books/detail/(:num)', 'Member\BookController::detail/$1', ['filter' => 'role:member']);

    $routes->get('borrowed', 'Member\BorrowController::borrowed', ['filter' => 'role:member']);
    $routes->get('history', 'Member\BorrowController::history', ['filter' => 'role:member']);
    $routes->get('return', 'Member\ReturnController::index', ['filter' => 'role:member']);

    $routes->post('borrow/save', 'Member\BorrowController::save', ['filter' => 'role:member']);
    $routes->post('return-process/(:num)', 'Member\BorrowController::processReturn/$1', ['filter' => 'role:member']);

    $routes->post('books/borrow/(:num)', 'Member\BookController::borrow/$1', ['filter' => 'role:member']);

    $routes->get('transactions', 'Member\BorrowController::history', ['filter' => 'role:member']);
});
