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

Router::scope('/', function ($routes) {

    // By default, resources provides the following routes...
    // GET /recipes.format RecipesController::index()
    // GET /recipes/123.format RecipesController::view(123)
    // POST /recipes.format RecipesController::add()
    // PUT /recipes/123.format RecipesController::edit(123)
    // PATCH /recipes/123.format RecipesController::edit(123)
    // DELETE /recipes/123.format RecipesController::delete(123)

    $routes->extensions(['json']);

    $routes->resources('Books', function ($routes) {
        $routes->resources('Accounts', function ($routes) {
            $routes->resources('Distributions');
            $routes->connect('/distributions/add', ['controller' => 'distributions', 'action' => 'add']);
            $routes->connect('/distributions/edit/*', ['controller' => 'distributions', 'action' => 'edit']);
        });
        $routes->connect('/accounts/add', ['controller' => 'accounts', 'action' => 'add']);
        $routes->connect('/accounts/edit/*', ['controller' => 'accounts', 'action' => 'edit']);

        $routes->resources('Transactions', function ($routes) {
            $routes->resources('Distributions');
            $routes->connect('/distributions/add', ['controller' => 'distributions', 'action' => 'add']);
            $routes->connect('/distributions/edit/*', ['controller' => 'distributions', 'action' => 'edit']);   
        });
        $routes->connect('/transactions/add', ['controller' => 'transactions', 'action' => 'add']);
        $routes->connect('/transactions/edit/*', ['controller' => 'transactions', 'action' => 'edit']);
    });
    $routes->connect('/books/add', ['controller' => 'books', 'action' => 'add']);
    $routes->connect('/books/edit/*', ['controller' => 'books', 'action' => 'edit']);
    $routes->connect('/books/graph_bank/*', ['controller' => 'books', 'action' => 'graph_bank']);
    $routes->connect('/books/graph_cash/*', ['controller' => 'books', 'action' => 'graph_cash']);
    $routes->connect('/books/balance/*', ['controller' => 'books', 'action' => 'balance']);
    $routes->connect('/books/income/*', ['controller' => 'books', 'action' => 'income']);

    $routes->resources('Categories');
    $routes->connect('/categories/add', ['controller' => 'categories', 'action' => 'add']);
    $routes->connect('/categories/edit/*', ['controller' => 'categories', 'action' => 'edit']);

    $routes->resources('Currencies');
    $routes->connect('/currencies/add', ['controller' => 'currencies', 'action' => 'add']);
    $routes->connect('/currencies/edit/*', ['controller' => 'currencies', 'action' => 'edit']);

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
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
//Plugin::routes();
