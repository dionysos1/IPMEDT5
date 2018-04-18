<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Product;
use App\ProductStatus;

class productController extends Controller
{
    public function index()
    {
      $products = Product::all();
      return view('welcome')->with('product', $products);
    }

    public function addProduct() {
        // Het verkrijgen van de informatie die moet worden vervangen in de database
        $rfid = Input::get('rfid');
        $name = Input::get('name');
        $endPrice = Input::get('endprice');
        $kg = Input::get('kg');
        // voor de zekerheid de prijs veranderen in een float zodat we geen String mee geven
        $endPrice = (double)$endPrice;
        $rfid = (string)$rfid;

        // database query maken voor het updaten met de informatie
        DB::table('product_status')
                                  ->where('rfid',$rfid)
                                  ->update(['price' => $endPrice,'kg' => $kg,'name' => $name,]);

        // nadat de query klaar is met uitvoeren terug gaan naar de pagina
        return redirect('/');
      }

      public function getInfo() {

        $info = DB::table('gebruiker_dump')->select('kg','rfid')->first();
        $newKg = $info->kg;
        $newRfid = $info->rfid;

        //$msg = "This is a simple message.";
      return response()->json(['kg'=> $newKg,'rfid' => $newRfid]);

      }
}
