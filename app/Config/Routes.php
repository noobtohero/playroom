<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('courses', 'HomeController::index');
$routes->get('course/(:segment)', 'HomeController::course/$1');

// Authentication Routes
$routes->group('', function($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::loginAttempt');
    
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::registerAttempt');

    $routes->get('logout', 'AuthController::logout');
});

// Student Protected Routes
$routes->group('student', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'StudentController::index');
    $routes->get('course/(:num)', 'StudentController::course/$1');
    $routes->get('course/(:num)/lesson/(:num)', 'StudentController::lesson/$1/$2');
    
    // Redeem Code
    $routes->get('redeem', 'StudentController::redeem');
    $routes->post('redeem', 'StudentController::redeemAttempt');
});

// Media Streaming Route (Shared auth)
$routes->group('media', ['filter' => 'auth'], function($routes) {
    $routes->get('stream/video/(:num)/(:num)/(.+)', 'StreamController::video/$1/$2/$3');
    $routes->get('stream/key/(:any)', 'StreamController::key/$1');
});

// Admin CMS Routes
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // Courses CRUD
    $routes->get('courses', 'Admin\CourseController::index');
    $routes->get('courses/create', 'Admin\CourseController::create');
    $routes->post('courses/store', 'Admin\CourseController::store');
    $routes->get('courses/edit/(:num)', 'Admin\CourseController::edit/$1');
    $routes->post('courses/update/(:num)', 'Admin\CourseController::update/$1');
    $routes->get('courses/delete/(:num)', 'Admin\CourseController::delete/$1');

    // Sections CRUD (Nested under course)
    $routes->get('courses/(:num)/sections', 'Admin\SectionController::index/$1');
    $routes->post('courses/(:num)/sections', 'Admin\SectionController::store/$1');
    $routes->post('sections/update/(:num)', 'Admin\SectionController::update/$1');
    $routes->get('sections/delete/(:num)', 'Admin\SectionController::delete/$1');

    // User Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
    $routes->get('users/delete/(:num)', 'Admin\UserController::delete/$1');

    // Enrollment Management
    $routes->get('enrollments', 'Admin\EnrollmentController::index');
    $routes->get('enrollments/create', 'Admin\EnrollmentController::create');
    $routes->post('enrollments/store', 'Admin\EnrollmentController::store');
    $routes->get('enrollments/delete/(:num)', 'Admin\EnrollmentController::delete/$1');

    // Redeem Code Management
    $routes->get('codes', 'Admin\CodeController::index');
    $routes->get('codes/create', 'Admin\CodeController::create');
    $routes->post('codes/store', 'Admin\CodeController::store');
    $routes->get('codes/delete/(:num)', 'Admin\CodeController::delete/$1');

    // Purchase Management
    $routes->get('purchases', 'Admin\PurchaseController::index');
    $routes->get('purchases/approve/(:num)', 'Admin\PurchaseController::approve/$1');
    $routes->get('purchases/reject/(:num)', 'Admin\PurchaseController::reject/$1');

    // Lessons CRUD
    $routes->get('sections/(:num)/lessons', 'Admin\LessonController::index/$1');
    $routes->get('sections/(:num)/lessons/create', 'Admin\LessonController::create/$1');
    $routes->post('sections/(:num)/lessons/store', 'Admin\LessonController::store/$1');
    $routes->get('lessons/edit/(:num)', 'Admin\LessonController::edit/$1');
    $routes->post('lessons/update/(:num)', 'Admin\LessonController::update/$1');
    $routes->get('lessons/delete/(:num)', 'Admin\LessonController::delete/$1');
});

// Teacher Routes
$routes->group('teacher', ['filter' => 'teacher'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index'); 
    
    // Courses CRUD
    $routes->get('courses', 'Admin\CourseController::index');
    $routes->get('courses/create', 'Admin\CourseController::create');
    $routes->post('courses/store', 'Admin\CourseController::store');
    $routes->get('courses/edit/(:num)', 'Admin\CourseController::edit/$1');
    $routes->post('courses/update/(:num)', 'Admin\CourseController::update/$1');
    $routes->get('courses/delete/(:num)', 'Admin\CourseController::delete/$1');

    // Sections CRUD
    $routes->get('courses/(:num)/sections', 'Admin\SectionController::index/$1');
    $routes->post('courses/(:num)/sections', 'Admin\SectionController::store/$1');
    $routes->post('sections/update/(:num)', 'Admin\SectionController::update/$1');
    $routes->get('sections/delete/(:num)', 'Admin\SectionController::delete/$1');

    // Lessons CRUD
    $routes->get('sections/(:num)/lessons', 'Admin\LessonController::index/$1');
    $routes->get('sections/(:num)/lessons/create', 'Admin\LessonController::create/$1');
    $routes->post('sections/(:num)/lessons/store', 'Admin\LessonController::store/$1');
    $routes->get('lessons/edit/(:num)', 'Admin\LessonController::edit/$1');
    $routes->post('lessons/update/(:num)', 'Admin\LessonController::update/$1');
    $routes->get('lessons/delete/(:num)', 'Admin\LessonController::delete/$1');
});
