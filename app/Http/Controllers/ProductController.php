<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function add(Request $request)
    {
        $product = array();
        $product['name'] = $request->input('name');
        $product['quantity'] = $request->input('quantity');
        $product['price'] = $request->input('price');
        $product['date'] = time();
        
        $file = file_get_contents(base_path('public/products.json'));
        $data = json_decode($file, true);
        $product['id'] = (int)count($data["data"])+1;
        $data["data"] = array_values($data["data"]);
        
        array_push($data["data"], $product);
        file_put_contents(base_path('public/products.json'), json_encode($data));
        
        $product['date'] = date('m-d-yy',$product['date']);
        $product['total'] = '$'.money_format($product['quantity']*$product['price'],2);
        $product['price'] = '$'.money_format($product['price'],2);
        return response()->json($product);
    }
    
    public function update(Request $request,$id)
    {
        $product = array();
        $product['name'] = (null !== $request->input('name') )? $request->input('name') : "";
        $product['quantity'] = (null !== $request->input('quantity') ) ? $request->input('quantity') : "";
        $product['price'] = (null !== $request->input('price') )? $request->input('price') : "";
        $product['date'] = time();
        $product['id'] = (int)$id;
        
        $getfile = file_get_contents(base_path('public/products.json'));
        $all = json_decode($getfile, true);
        $jsonfileProduce = $all["data"];
        $key = array_search($id, array_column($jsonfileProduce, 'id'));
        $jsonfileProduce = $jsonfileProduce[$key];
        
        if ($jsonfileProduce) {
            unset($all["data"][$key]);
            $all["data"][$key] = $product;
            $all["data"] = array_values($all["data"]);
            file_put_contents(base_path('public/products.json'), json_encode($all));
        }
        $product['date'] = date('m-d-yy',$product['date']);
        $product['total'] = '$'.money_format($product['quantity']*$product['price'],2);
        $product['price'] = '$'.money_format($product['price'],2);
        return response()->json($product);
    }
    public function delete($id)
    {
        $all = file_get_contents(base_path('public/products.json'));
        $all = json_decode($all, true);
        $jsonfile = $all["data"];
        $key = array_search($id, array_column($jsonfile, 'id'));
        $jsonfile = $jsonfile[$key];

        if ($jsonfile) {
            unset($all["data"][$key]);
            $all["data"] = array_values($all["data"]);
            file_put_contents(base_path('public/products.json'), json_encode($all));
        }
        return response()->json($all);
    }
}
