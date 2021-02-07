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
                    'Deal_Name' => 'Test Deal',
                    'Stage' => 'Qualification',
                ]
            ], 'Task for Test Deal'
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
            ->setGrandCode('1000.ce51702714f8a03aadd9f724b1e1e454.46a83d30452195145ecd125510ddde3f');

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
