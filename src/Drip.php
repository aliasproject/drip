<?php namespace AliasProject\Drip;

class Drip
{
    const ENDPOINT = 'https://api.getdrip.com/v2/';

    private $account_id;
    private $api_key;
    private $headers;

    /**
     * Create new instance
     *
     * @param string $account_id - API Account ID
     * @param string $api_key - API Key
     * @param string $business_name - Business Name
     * @param string $business_domain - Business Domain
     */
    public function __construct(string $account_id, string $api_key, string $business_name, string $business_domain)
    {
        // Set Authentication
        $this->account_id = $account_id;
        $this->api_key = $api_key;

        // Set Request Headers
        $this->headers = [
            'Content-Type: application/json',
            'User-Agent: ' . $business_name . ' (' . $business_domain . ')'
        ];
    }

    /**
     * Add or update subscriber
     *
     * @param integer $drip_id - Current user Drip ID for update
     * @param array $custom_fields - Array of custom fields to save to user
     */
    public function addSubscriber(int $drip_id = null, array $custom_fields = null)
    {
        if ($drip_id) {
    		$subscriber_data = [
				'id' => $drip_id,
				'new_email' => $_POST['billing_email']
			];
    	} else {
            $subscriber_data = [
				'email' => $_POST['billing_email']
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
    private function makeRequest(string $url, array $data = [], bool $post = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, $pwd);
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
