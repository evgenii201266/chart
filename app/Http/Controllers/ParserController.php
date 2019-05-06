<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParserRequest\ParserRequest;
use App\Http\Helpers\Parser\JsonHelper;
use App\Http\Helpers\Parser\ParseHelper;



class ParserController extends Controller
{
    use JsonHelper;
    use ParseHelper;

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index(ParserRequest $request)
    {
        $response = $this->getCatalogById($request->catalogId);
        $this->getParse($response);

    }
}
