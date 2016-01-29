<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\Router;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass('DashedRoute');

// from http://thomasv.nl/2013/12/cakephp-restful-routes-basics/
//GET 	    /projects 	        index 	    display all projects
//GET 	    /projects/add 	    new 	    return a HTML Form to add a new project
//POST 	    /projects 	        create 	    create a new project
//GET 	    /projects/:id 	    show 	    display a specific project
//GET 	    /projects/:id/edit 	edit 	    return a HTML Form to edit a project
//PATCH/PUT /projects/:id 	    update 	    update a specific project
//DELETE 	/projects/:id 	    destroy 	delete a specific project



// from cookbook
//HTTP format URL.format          Controller action invoked
//GET         /recipes.format     BooksController::index()
//GET         /recipes/123.format BooksController::view(123)
//POST        /recipes.format     BooksController::add()
//PUT         /recipes/123.format BooksController::edit(123)
//PATCH       /recipes/123.format BooksController::edit(123)
//DELETE      /recipes/123.format BooksController::delete(123)
//Router::scope('/', function ($routes) {
    //$routes->extensions(['json']);
    //$routes->resources('Books');
//});


/*Router::scope('/api', function ($routes) {
    $routes->resources('Books', function ($routes) {
        $routes->resources('Accounts');
    });
});*/


Router::scope('/', function ($routes) {




    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
     //$routes->fallbacks('DashedRoute');


    //$routes->connect('/account', ['controller' => 'Accounts', 'action' => 'index']);

    //$routes->resources('Cats');
    //$routes->connect('/cats', ['controller' => 'Cats', 'action' => 'index']);
    //$routes->connect('/cats/add', ['controller' => 'Cats', 'action' => 'add']);
    //$routes->connect('/cats/delete/*', ['controller' => 'Cats', 'action' => 'delete']);
    //$routes->connect('/cats/edit/*', ['controller' => 'Cats', 'action' => 'edit']);
    //$routes->connect('/cats/view/*', ['controller' => 'Cats', 'action' => 'view']);

    $routes->resources('Dogs');
    $routes->connect('/dogs/add', ['controller' => 'Dogs', 'action' => 'add']);
    $routes->connect('/dogs/edit/*', ['controller' => 'Dogs', 'action' => 'edit']);

    //$routes->connect('/books/new_form', ['controller' => 'Books', 'action' => 'new_form']);
    //$routes->connect('/books:_method', ['controller' => 'Books', 'action' => 'create', '_method'=>'post']);
    //$routes->connect('/books/:id', ['controller' => 'Books', 'action' => 'show']);
    //$routes->connect('/books/:id/edit', ['controller' => 'Books', 'action' => 'edit']);
    //$routes->connect('/books/:id', ['controller' => 'Books', 'action' => 'destroy', 'method'=>'delete']);

    //$routes->connect('/transactions', ['controller' => 'Transactions', 'action' => 'index']);



});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
//Plugin::routes();
