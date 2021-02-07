<?php


namespace App\Client;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;


/**
 * Class ZohoCRMClient
 * @package App\Client
 */
class ZohoCRMClient
{
    /**
     * @var string
     */
    private $clientId = '1000.CCIVG6SSHA23ME0SA5A9XI0JXHDFBU';

    /**
     * @var string
     */
    private $clientSecret = 'c9dc9f68213f1e741309c58fa231afcbeb35c5c1e6';

    /**
     * @var string
     */
    private $redirectUri = 'http://test-laravel.igor-yuzkiv.website/create-deal';

    /**
     * @var string
     */
    private $grandCode = null;

    /**
     * @var null
     */
    private $token = null;

    /**
     * @param string $grandCode
     * @return $this
     */
    public function setGrandCode(string $grandCode): self
    {
        $this->grandCode = $grandCode;
        return $this;
    }

    /**
     * @param string $clientId
     * @return $this
     */
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param string $clientSecret
     * @return $this
     */
    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @param string $redirectUri
     * @return $this
     */
    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    /**
     * @throws GuzzleException
     */
    private function setToken()
    {
        $client = new Client();
        $request = $client->post('https://accounts.zoho.com/oauth/v2/token', [
            RequestOptions::FORM_PARAMS => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $this->grandCode,
            ]
        ]);

        $response = json_decode($request->getBody()->getContents(), true);
        if (isset($response['access_token'])) {
            $this->token = $response['access_token'];
        } else {
            throw new \Exception('invalid code');
        }
    }

    /**
     * @param array $item
     * @param string|null $taskSubject
     * @return string
     * @throws GuzzleException
     */
    public function createDeal(array $item, string $taskSubject = null)
    {
        $this->setToken();

        $client = new Client([
            'defaults' => [
                'headers' => ['Authorization' => 'Zoho-oauthtoken ' . $this->token]
            ]
        ]);

        $request = $client->post('https://www.zohoapis.com/crm/v2/Deals', [
            RequestOptions::HEADERS => ['Authorization' => 'Zoho-oauthtoken ' . $this->token, 'Content-Type' => 'application/json',],
            RequestOptions::JSON => [
                'data' => $item
            ]
        ]);

        if ($taskSubject != null && $request->getStatusCode() === 201) {
            $dealData = json_decode($request->getBody()->getContents(), true);
            $this->createTaskForDeal($taskSubject, $dealData);
        }

        return ($request->getStatusCode() == 201) ? 'Success' : 'Error';
    }

    /**
     * @param string $subject
     * @param array $dealData
     * @throws GuzzleException
     */
    public function createTaskForDeal(string $subject, array $dealData)
    {
        $client = new Client([
            'defaults' => [
                'headers' => ['Authorization' => 'Zoho-oauthtoken ' . $this->token]
            ]
        ]);

        $request = $client->post('https://www.zohoapis.com/crm/v2/Tasks', [
            RequestOptions::HEADERS => ['Authorization' => 'Zoho-oauthtoken ' . $this->token, 'Content-Type' => 'application/json',],
            RequestOptions::JSON => [
                'data' => [
                    [
                        'Subject' => $subject,
                        'What_Id' => $dealData['data'][0]['details']['id'],
                        '$se_module' => 'Deals'
                    ]
                ]
            ]
        ]);

        return $request->getStatusCode();
    }

    /**
     * @param string $scope
     * @return string
     */
    public function generateGrandCodeUrl($scope = 'ZohoCRM.modules.ALL')
    {
        $query = [
            'scope' => $scope,
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'access_type' => 'online',
            'redirect_uri' => $this->redirectUri,
        ];

        return 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query($query);
    }
}
