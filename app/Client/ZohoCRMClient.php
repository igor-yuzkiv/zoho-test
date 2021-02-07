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
     *
     * https://accounts.zoho.com/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=1000.CCIVG6SSHA23ME0SA5A9XI0JXHDFBU&response_type=code&access_type=online&redirect_uri=http://test-laravel.igor-yuzkiv.website/redirect-page
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
        return  $this;
    }

    /**
     * @param array $items
     * @return array
     * @throws GuzzleException
     */
    public function createDeal(array $items)
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
                'data' => $items
            ]
        ]);

        return [$request->getStatusCode(), $request->getBody()->getContents()];
    }

    /**
     * @throws GuzzleException
     *
     * "{"access_token":"1000.68c54210b26eb16934a1190373f973c7.756167d4eff814645a804fcd52622d6e","api_domain":"https://www.zohoapis.com","token_type":"Bearer","expires_in":3600}
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
        }else {
            throw new \Exception('invalid code');
        }
    }

    /**
     * @return string
     */
    public function generateGrandCodeUrl()
    {
        $query = [
            'scope' => 'ZohoCRM.modules.ALL',
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'access_type' => 'online',
            'redirect_uri' => $this->redirectUri,
        ];

        return 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query($query);
    }
}
