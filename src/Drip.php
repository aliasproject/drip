<?php namespace AliasProject\Drip;

class Drip
{
    protected $endpoint = 'https://api.getdrip.com/v2';
    private $account_id = null;
    private $token = false;

    /**
     * Create new instance
     *
     * @param string $account_id - API Account ID
     * @param string $api_key - API Key
     * @param string $business_name - Business Name
     * @param string $business_domain - Business Domain
     */
    public function __construct(string $account_id, string $token)
    {
        // Set Authentication
        $this->account_id = $account_id;
        $this->token = $token;
    }

    /**
     * Add or update subscriber
     *
     * @param string $email - Subscriber Email
     * @param string $drip_id - Current user Drip ID for update
     * @param array $custom_fields - Array of custom fields to save to user
     */
    public function addSubscriber(string $email, int $campaign_id = null, string $drip_id = null, array $custom_fields = null)
    {
        if ($drip_id && !$campaign_id) {
    		$subscriber_data = [
				'id' => $drip_id,
				'new_email' => $email
			];
    	} else {
            $subscriber_data = [
				'email' => $email
			];
    	}

        if ($custom_fields) {
            $subscriber_data['custom_fields'] = $custom_fields;
        }

        $data = ['subscribers' => [$subscriber_data]];
        $url = ($campaign_id) ? 'campaigns/' . $campaign_id . '/subscribers' : 'subscribers';

        return $this->makeRequest($url, $data, true);
    }

    public function getSubscriber(string $email, int $campaign_id = null)
    {
        $response = $this->makeRequest('subscribers/' . $email);

    	return (isset($response->errors)) ? false : true;
    }

    /**
     * Make HTTP Request
     *
     * @param  string  $url
     * @return string
     */
    protected function makeRequest(string $url, array $data = [], bool $post = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . '/' . $this->account_id . '/' . $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->token);
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AliasProject/Drip (github.com/aliasproject/drip)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
        ]);

        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }
}
