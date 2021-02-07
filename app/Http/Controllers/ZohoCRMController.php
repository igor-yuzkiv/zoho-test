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
    public function create_deal(Request $request)
    {
        Validator::make($request->all(), [
            'code' => 'required'
        ])->validate();

        $this->zohoCRMClient->setGrandCode($request->code);

        dd($this->zohoCRMClient->createDeal(
            [
                [
                    'Deal_Name' => 'Test value453',
                    'Stage' => 'Qualification',
                ]
            ]
        ));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create_deal_self_client()
    {
        $this->zohoCRMClient
            ->setClientId('1000.435G177Z1Y88YNGXICNN4O93DGNKJL')
            ->setClientSecret('9c26ad89509e73eaacd2d7d7fa30f877e674a9a4c1')
            ->setRedirectUri('http://test-laravel.igor-yuzkiv.website/create-deal-self-client')
            ->setGrandCode('1000.1e279d90226c9971510e30c18a16542b.3b97f3ea00809293e19cb7129892a7d0');

        dd($this->zohoCRMClient->createDeal(
            [
                [
                    'Deal_Name' => 'Test value2 self',
                    'Stage' => 'Qualification',
                ]
            ]
        ));
    }

}
