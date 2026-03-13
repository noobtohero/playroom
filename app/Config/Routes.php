<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
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
    
    // Media Streaming Route
    $routes->get('stream/video/(:num)/(:num)/(:any)', 'StreamController::video/$1/$2/$3');
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

    // Lessons CRUD
    $routes->get('sections/(:num)/lessons', 'Admin\LessonController::index/$1');
    $routes->get('sections/(:num)/lessons/create', 'Admin\LessonController::create/$1');
    $routes->post('sections/(:num)/lessons/store', 'Admin\LessonController::store/$1');
    $routes->get('lessons/edit/(:num)', 'Admin\LessonController::edit/$1');
    $routes->post('lessons/update/(:num)', 'Admin\LessonController::update/$1');
    $routes->get('lessons/delete/(:num)', 'Admin\LessonController::delete/$1');
});
