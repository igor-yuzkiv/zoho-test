<?php

namespace App\Http\Controllers;

use App\Client\ZohoCRMClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class ZohoCRMController
 * @package App\Http\Controllers
 */
class ZohoCRMController extends Controller
{
    /**
     * @var ZohoCRMClient
     */
    private $zohoCRMClient;


    /**
     * ZohoCRMController constructor.
     */
    public function __construct()
    {
        $this->zohoCRMClient = new ZohoCRMClient();
    }

    /**
     * @param ZohoCRMClient $zohoCRMClient
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(ZohoCRMClient $zohoCRMClient)
    {
        return redirect($this->zohoCRMClient->generateGrandCodeUrl());

    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function redirect_page(Request $request)
    {
        Validator::make($request->all(), [
            'code' => 'required'
        ])->validate();

        $this->zohoCRMClient->setGrandCode($request->code);

        dd($this->zohoCRMClient->createDeal());
    }
}
