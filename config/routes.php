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

    // In addition to the standard REST routes, we also need a method to obtain an entry form
    // for a new record and an edited record.
    // GET /recipies/newform.format
    // GET /recipies/123/editform.format

    // I have remove the PATCH edit route cuz I don't see the necessity.

    //$routes->extensions(['json']);

    // Option A. Use Cake to do this

    // By default, resources wants to set edit/update to accept PUT and PATCH.  But I only
    // want PUT.  I need to invoke deep magic to change that.
    $deepMagic=['map'=>['update'=>['action'=>'edit','method'=>'PUT','path'=>':id']]];
    // change it to only accept PUT.
    $routes->resources('Books',$deepMagic,function ($routes) {
        $routes->resources('Accounts',
            ['map'=>['update'=>['action'=>'edit','method'=>'PUT','path'=>':id']]],
            function ($routes) {
                $routes->resources('Distributions',['map'=>[
                    'update'=>['action'=>'edit','method'=>'PUT','path'=>':id'], // only PUT, not PATCH
                    //'editform'=>['action'=>'editform','method'=>'GET'],
                    'newform'=>['action'=>'newform','method'=>'GET']
                ]
                ]);
                //$routes->connect('/distributions/add', ['controller' => 'distributions', 'action' => 'add',''=>'']);
                //$routes->connect('/distributions/edit/*', ['controller' => 'distributions', 'action' => 'edit']);
            });
        //$routes->connect('/accounts/add', ['controller' => 'accounts', 'action' => 'add']);
        //$routes->connect('/accounts/edit/*', ['controller' => 'accounts', 'action' => 'edit']);
        $routes->connect('/accounts/newform',['controller'=>'accounts','action'=>'newform']);
        $routes->connect('/accounts/:id/editform',['controller'=>'accounts','action'=>'editform']);

        $routes->resources('Transactions',
            ['map'=>[
                'update'=>['action'=>'edit','method'=>'PUT','path'=>':id'], // only PUT, not PATCH
                //'editform'=>['action'=>'editform','method'=>'GET'],
                'newform'=>['action'=>'newform','method'=>'GET'],
                ]
            ],
            function ($routes) {
                $routes->resources('Distributions',
                    ['map'=>[
                    'update'=>['action'=>'edit','method'=>'PUT','path'=>':id'], // only PUT, not PATCH
                    //'editform'=>['action'=>'editform','method'=>'GET'],
                    'newform'=>['action'=>'newform','method'=>'GET']
                ]
            ]);
            //$routes->connect('/distributions/add', ['controller' => 'distributions', 'action' => 'add']);
            //$routes->connect('/distributions/edit/*', ['controller' => 'distributions', 'action' => 'edit']);
            $routes->connect('/distributions/:id/editform',['controller'=>'distributions','action'=>'editform']);
            //$routes->connect('/transactions/newform', ['controller'=>'transactions','action'=>'newform']);
            //$routes->connect('/newform', ['action'=>'newform']);
            // either way works, but can't pass :id into the controller method as an argument
            //$routes->connect('/transactions/editform/:id', ['controller'=>'transactions','action'=>'editform']);
            //$routes->connect('/transactions/:id/editform', ['controller'=>'transactions','action'=>'editform']);
            }
        );
        //$routes->connect('/transactions/add', ['controller' => 'transactions', 'action' => 'add']);
        //$routes->connect('/transactions/edit/*', ['controller' => 'transactions', 'action' => 'edit']);
        $routes->connect('/transactions/:id/editform',['controller'=>'transactions','action'=>'editform']);
    });
    $routes->connect('/books/newform', ['controller'=>'books','action'=>'newform']);

    // either way works, but can't pass :id into the controller method as an argument
    //$routes->connect('/books/editform/:id', ['controller'=>'books','action'=>'editform']);
    $routes->connect('/books/:id/editform', ['controller'=>'books','action'=>'editform']);
    $routes->connect('/books/:id/balance', ['controller'=>'books','action'=>'balance']);
    $routes->connect('/books/:id/income', ['controller'=>'books','action'=>'income']);

    //$routes->connect('/books/graph_bank/*', ['controller' => 'books', 'action' => 'graph_bank']);
    //$routes->connect('/books/graph_cash/*', ['controller' => 'books', 'action' => 'graph_cash']);
    //$routes->connect('/books/balance/*', ['controller' => 'books', 'action' => 'balance']);
    //$routes->connect('/books/income/*', ['controller' => 'books', 'action' => 'income']);

    
    $routes->resources('Categories',$deepMagic,function ($routes) {});
    $routes->connect('/categories/newform', ['controller'=>'categories','action'=>'newform']);
    // either way works, but can't pass :id into the controller method as an argument
    //$routes->connect('/categories/editform/:id', ['controller'=>'categories','action'=>'editform']);
    $routes->connect('/categories/:id/editform', ['controller'=>'categories','action'=>'editform']);

    $routes->resources('Currencies',$deepMagic,function ($routes) {});
    $routes->connect('/currencies/newform', ['controller'=>'currencies','action'=>'newform']);
    // either way works, but can't pass :id into the controller method as an argument
    //$routes->connect('/currencies/editform/:id', ['controller'=>'currencies','action'=>'editform']);
    $routes->connect('/currencies/:id/editform', ['controller'=>'currencies','action'=>'editform']);

    // Option B. Try to make restful and nested routes myself
    //$routes->scope('/books', function ($routes) {
    //$routes->connect('/', ['controller'=>'books','action'=>'index']);
    //$routes->connect('/add', ['controller'=>'books','action'=>'add']);
    //$routes->connect('/edit/*', ['controller'=>'books','action'=>'edit']);
    //$routes->connect('/view', ['controller'=>'books','action'=>'view']);
    //$routes->connect('/add', ['controller'=>'books','action'=>'add', '_method'=>'POST']);

    //$routes->scope('/:book_id/accounts', function ($routes) {
    //$routes->connect('/add', ['controller' => 'accounts', 'action' => 'add']);
    //});

    //});

    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    //$routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

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
