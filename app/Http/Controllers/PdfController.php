<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller; 

class PdfController extends Controller {

public function github (){
 return \PDF::loadFile('http://www.github.com')->stream('github.pdf'); 
 }

}
