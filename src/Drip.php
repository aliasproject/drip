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
     * @param integer $drip_id - Current user Drip ID for update
     * @param array $custom_fields - Array of custom fields to save to user
     */
    public function addSubscriber(string $email, int $drip_id = null, array $custom_fields = null)
    {
        if ($drip_id) {
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

        $this->makeRequest(self::ENDPOINT . $this->account_id . '/subscribers', $data, true);
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
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
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

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
