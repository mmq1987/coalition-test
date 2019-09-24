<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $products = file_get_contents(base_path('public/products.json'));
    $products = json_decode($products);
    if(isset($products->data)){
        return view('product',['products'=> $products->data]);
    }else{
        return view('product');
    }
});

//--CREATE a Product--//
Route::post('/addProduct', 'ProductController@add');
 
//--GET Product TO EDIT--//
Route::get('/product/{id?}', function ($id) {
    $products = file_get_contents(base_path('public/products.json'));
    $products = json_decode($products, true);
    $products_data = $products["data"];
    $key = array_search($id, array_column($products_data, 'id'));
    $product = $products_data[$key];
    return Response::json($product);
});

////--UPDATE a Product--//
Route::put('/updateProduct/{id?}', 'ProductController@update');


////--DELETE a Product --//
Route::delete('/deleteProdct/{id?}', 'ProductController@delete');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
