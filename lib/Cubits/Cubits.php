<?php

namespace Cubits;

class Cubits
{

    private $rpc;
    private $authenticator;
    private $apiBase = 'https://api.cubits.com';
    private $sslVerify = true;

    public function configure($apiBase, $sslVerify = true)
    {
        $this->apiBase = $apiBase;
        $this->sslVerify = $sslVerify;
    }

    /**
     * @param $key
     * @param $secret
     * @return Cubits
     * @throws ApiException
     * @throws \Exception
     */
    public static function withApiKey($key, $secret)
    {
        if(!function_exists('curl_init')) {
            throw new \Exception('The Cubits client library requires the CURL PHP extension.');
        }

        return new Cubits(new ApiKeyAuthenticator($key, $secret));
    }

    /**
     * *DEPRECATED* Cubits constructor. Use Cubits::withApiKey instead.
     * @param $authenticator
     * @param $tokens       null
     * @param $apiKeySecret null
     * @throws ApiException
     */
    public function __construct($authenticator, $tokens = null, $apiKeySecret = null)
    {
        // First off, check for a legit authentication class type
        if ($authenticator instanceof Authenticator) {
            $this->authenticator = $authenticator;
        } else {
            // Here, $authenticator was not a valid authentication object, so
            // analyze the constructor parameters and return the correct object.
            // This should be considered deprecated, but it's here for backward compatibility.
            // In older versions of this library, the first parameter of this constructor
            // can be either an API key string or an OAuth object.
            if ($authenticator !== null && is_string($authenticator)) {
                $apiKey = $authenticator;

                $this->authenticator = new ApiKeyAuthenticator($apiKey, $apiKeySecret);

            } else {
                throw new ApiException('Could not determine API authentication scheme');
            }
        }

        $this->rpc = new Rpc($this, new RequestExecutor(), $this->authenticator);
    }

    /**
     * @param $path
     * @param $params array
     * @return mixed
     * @throws ApiException
     * @throws ConnectionException
     */
    public function get($path, $params = array())
    {
        return $this->rpc->request("GET", $path, $params);
    }

    /**
     * @param $path
     * @param $params array
     * @return mixed
     * @throws ApiException
     * @throws ConnectionException
     */
    public function post($path, $params = array())
    {
        return $this->rpc->request("POST", $path, $params);
    }

    /**
     * @param $path
     * @param $params array
     * @return mixed
     * @throws ApiException
     * @throws ConnectionException
     */
    public function delete($path, $params = array())
    {
        return $this->rpc->request("DELETE", $path, $params);
    }

    /**
     * @param $path
     * @param $params array
     * @return mixed
     * @throws ApiException
     * @throws ConnectionException
     */
    public function put($path, $params = array())
    {
        return $this->rpc->request("PUT", $path, $params);
    }

    /**
     * This request is intended to be used to test if your application is configured properly and can access the Cubits
     * API using POST requests.
     * @param $params
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function postTest($params)
    {
        $response = json_decode($this->post("test", $params));
        $returnValue = new \stdClass();
        $returnValue->status = $response->status;

        return $returnValue;
    }

    /**
     * This request is intended to be used to test if your application is configured properly and can access the Cubits
     * API using GET requests.
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function getTest()
    {
        $response = json_decode($this->get("test"));
        $returnValue = new \stdClass();
        $returnValue->status = $response->status;

        return $returnValue;

    }

    /**
     * Creates a new invoice.
     * @param $currency   string(3)     ISO 4217 code of the currency that the merchant wants to receive (e.g. "EUR")
     * @param $price      string(16)    Price of the invoice that the merchant wants to receive, as a decimal floating
     *                                  point number, converted to string (e.g. "123.05")
     *
     * @param $name       string(256)   (optional) Name of the item displayed to the customer
     * @param $options    array         (<br>
     * <b>share_to_keep_in_btc</b>      <i>number</i><br>
     * (optional) Percentage of the invoice amount to be kept in BTC, as an integer number from 0 to 100.
     * If not specified, a default value is used from the Cubits Pay / Payouts / Percentage Kept in BTC <br>
     *
     * <b>description</b>               <i>string(512)</i><br>
     * (optional) Description of the item displayed to the customer <br>
     *
     * <b>reference</b>                 <i>string(512)</i><br>
     * (optional) Individual free-text field stored in the invoice as-is <br>
     *
     * <b>callback_url</b>              <i>string(512)</i><br>
     * (optional) URL that is called on invoice status updates <br>
     *
     * <b>success_url</b>               <i>string(512)</i><br>
     * (optional) URL to redirect the customer to after a successful <br>
     * )
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function createInvoice($currency, $price, $name = '', $options = array())
    {
        $params = array(
            "name" => $name,
            "price" => number_format($price, 8, '.', ''),
            "currency" => $currency
        );

        foreach ($options as $option => $value) {
            $params[$option] = $value;
        }

        return $this->createInvoiceWithOptions($params);
    }

    /**
     * @param $options array
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function createInvoiceWithOptions($options = array())
    {

        $response = json_decode($this->post("invoices", $options));

        $returnValue = new \stdClass();
        $returnValue->embedHtml = '<div class="cubits-button" data-code="55555" style="background: yellow; padding: 10px 25px; float:left">Pay with bitcoin</div>';
        $returnValue->id = $response->id;
        $returnValue->invoice_url = $response->invoice_url;
        $returnValue->address = $response->address;
        $returnValue->valid_until_time = $response->valid_until_time;

        return $returnValue;

    }

    /**
     * Get information about an existing invoice.
     * @param $invoiceId    string  Unique identifier of the invoice
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function getInvoice($invoiceId)
    {
        $invoiceUrl = "invoices/" . $invoiceId;

        $response = json_decode($this->get($invoiceUrl));

        $returnValue = new \stdClass();
        $returnValue->id = $response->id;
        $returnValue->status = $response->status;

        $returnValue->address = $response->address;

        $returnValue->merchant_currency = $response->merchant_currency;
        $returnValue->merchant_amount = $response->merchant_amount;

        $returnValue->invoice_currency = $response->invoice_currency;
        $returnValue->invoice_amount = $response->invoice_amount;
        $returnValue->invoice_url = $response->invoice_url;

        $returnValue->paid_currency = $response->paid_currency;
        $returnValue->paid_amount = $response->paid_amount;

        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;

        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->cancel_url = $response->cancel_url;

        $returnValue->notify_email = $response->notify_email;

        return $returnValue;

    }

    /**
     * Creates a transaction to send bitcoins from your Cubits wallet to an external bitcoin address.
     * @param $address      string(64)  Bitcoin address the amount is to be sent to
     * @param $amount       string(32)  Amount in BTC to be sent, decimal number as a string (e.g. "0.12500000")
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function sendMoney($address, $amount)
    {
        $params = array(
            "amount" => number_format($amount, 8, '.', ''),
            "address" => $address
        );
        $response = json_decode($this->post("send_money", $params));

        $returnValue = new \stdClass();
        $returnValue->tx_ref_code = $response->tx_ref_code;

        return $returnValue;
    }

    /**
     * Retrieves a list of your Cubits wallet accounts. Each wallet can have accounts in different currencies. With this
     * call you can get a complete overview of all your balances on Cubits.
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function listAccounts()
    {
        $response = json_decode($this->get("accounts"));
        $returnValue = new \stdClass();
        $returnValue->accounts = $response->accounts;

        return $returnValue;
    }

    /**
     * Requests a quote for a buy or sell operation.
     * @param $operation            string(256)     Type of the transaction: ￼buy or sell
     * @param $sender_currency      string(3)       ISO 4217 code of the currency that you want to spend (e.g. "EUR")
     * @param $sender_amount        string(16)      Price of the invoice that the merchant wants to receive, as a
     *                                              decimal floating point number, converted to string (e.g. "123.05")
     * @param $receiver_currency    string(3)       ISO 4217 code of the currency that you want to spend (e.g. "EUR")
     * @param $receiver_amount      string(16)      Price of the invoice that the merchant wants to receive, as a
     *                                              decimal floating point number, converted to string (e.g. "123.05")
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function requestQuote($operation, $sender_currency, $sender_amount, $receiver_currency, $receiver_amount)
    {
        $sender = array(
            'currency' => $sender_currency,
            'amount' => $sender_amount
        );
        $receiver = array(
            'currency' => $receiver_currency,
            'amount' => $receiver_amount
        );
        $params = array(
            "operation" => $operation,
            "sender" => $sender,
            "receiver" => $receiver
        );

        return $this->requestQuoteWithParams($params);
    }

    /**
     * @param $params
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function requestQuoteWithParams($params)
    {
        $response = json_decode($this->post("quotes", $params));

        $returnValue = new \stdClass();
        $returnValue->operation = $response->operation;
        $returnValue->sender = array(
            'currency' => $response->sender->currency,
            'amount' => $response->sender->amount
        );

        $returnValue->receiver = array(
            'currency' => $response->receiver->currency,
            'amount' => $response->receiver->amount
        );

        return $returnValue;
    }

    /**
     * Creates a transaction to buy bitcoins using funds from your Cubits account. Bought bitcoins will be credited to
     * your Cubits wallet.
     * The exact exchange rate will be calculated at the transaction execution time.
     * @param $senderCurrency   string(3)       ISO 4217 code of the currency that you want to spend (e.g. "EUR")
     * @param $senderAmount     string(32)      Amount in specified currency to be spent, decimal number as a string
     *                                          (e.g. "12.50")
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function buy($senderCurrency, $senderAmount)
    {
        $sender = array(
            "currency" => $senderCurrency,
            "amount" => number_format($senderAmount, 8, '.', '')
        );
        $params = array(
            "sender" => $sender
        );
        $response = json_decode($this->post("buy", $params));

        $returnValue = new \stdClass();
        $returnValue->tx_ref_code = $response->tx_ref_code;

        return $returnValue;
    }

    /**
     * Creates a transaction to sell bitcoins from your Cubits wallet and receive amount in specified fiat currency.
     * Fiat funds will be credited to your Cubits account.
     * The exact exchange rate will be calculated at the transaction execution time.
     * @param $sender_amount           string(32)   Amount in specified currency to be spent,
     *                                              decimal number as a string (e.g. "12.50")
     * @param $receiver_currency       string(3)    ISO 4217 code of the currency that you want to spend (e.g. "EUR")
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function sell($sender_amount, $receiver_currency)
    {
        $sender = array(
            "amount" => number_format($sender_amount, 8, '.', '')
        );
        $receiver = array(
            "currency" => $receiver_currency
        );
        $params = array(
            "sender" => $sender,
            "receiver" => $receiver
        );
        $response = json_decode($this->post("sell", $params));

        $returnValue = new \stdClass();
        $returnValue->tx_ref_code = $response->tx_ref_code;

        return $returnValue;
    }

    /**
     * Get information about an existing channel.
     * @param $channelId    string  Unique identifier of the channel
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function getChannel($channelId)
    {
        $url = "channels/" . $channelId;

        $response = json_decode($this->get($url));

        $returnValue = new \stdClass();
        $returnValue->id = $response->id;
        $returnValue->address = $response->address;
        $returnValue->receiver_currency = $response->receiver_currency;
        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;
        $returnValue->channel_url = $response->channel_url;
        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->created_at = $response->created_at;
        $returnValue->updated_at = $response->updated_at;
        $returnValue->transactions = isset($response->transactions) ? $response->transactions : array();
        $returnValue->txs_callback_url = $response->txs_callback_url;

        return $returnValue;
    }

    /**
     * Creates a new channel.
     * @param $receiver_currency    string(3)          ISO 4217 code of the currency that you want to spend (e.g. "EUR")
     * @param $name                 null|string(256)   (optional) Name of the channel, displayed to the customer
     *                                                  on the payment screen
     * @param $description          null|string(512)   (optional) Description of the item displayed to the customer
     *                                                  on the payment screen
     * @param $reference            null|string(512)   (optional) Individual free-text field stored in the channel as-is
     * @param $callback_url         null|string(512)   (optional) URL that is called on channel status updates
     * @param $success_url          null|string(512)   (optional) URL to redirect the user to after a successful payment
     * @param $txs_callback_url
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function createChannel(
        $receiver_currency,
        $name = null,
        $description = null,
        $reference = null,
        $callback_url = null,
        $success_url = null,
        $txs_callback_url = null
    )
    {
        $params = array(
            "receiver_currency" => $receiver_currency,
            "name" => $name,
            "description" => $description,
            "reference" => $reference,
            "callback_url" => $callback_url,
            "success_url" => $success_url,
            "txs_callback_url" => $txs_callback_url
        );

        $response = json_decode($this->post("channels", $params));

        $returnValue = new \stdClass();

        $returnValue->id = $response->id;
        $returnValue->address = $response->address;
        $returnValue->receiver_currency = $response->receiver_currency;
        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;
        $returnValue->channel_url = $response->channel_url;
        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->created_at = $response->created_at;
        $returnValue->updated_at = $response->updated_at;
        $returnValue->transactions = isset($response->transactions) ? $response->transactions : array();
        $returnValue->txs_callback_url = $response->txs_callback_url;

        return $returnValue;
    }

    /**
     * @param $channelId            string             Unique identifier of the channel
     * @param $receiver_currency    string(3)          ISO 4217 code of the currency that you want to spend (e.g. "EUR")
     * @param $name                 null|string(256)   (optional) Name of the channel, displayed to the customer
     *                                                  on the payment screen
     * @param $description          null|string(512)   (optional) Description of the item displayed to the customer
     *                                                  on the payment screen
     * @param $reference            null|string(512)   (optional) Individual free-text field stored in the channel as-is
     * @param $callback_url         null|string(512)   (optional) URL that is called on channel status updates
     * @param $success_url          null|string(512)   (optional) URL to redirect the user to after a successful payment
     * @param $tx_callback_url      null|string(512)   (optional) URL that is called on channel transaction status
     *                                                  updates
     * @return \stdClass
     * @throws ApiException
     * @throws ConnectionException
     */
    public function updateChannel(
        $channelId,
        $receiver_currency,
        $name = null,
        $description = null,
        $reference = null,
        $callback_url = null,
        $success_url = null,
        $tx_callback_url = null
    )
    {
        $url = "channels/" . $channelId;
        $params = array(
            "receiver_currency" => $receiver_currency,
            "name" => $name,
            "description" => $description,
            "reference" => $reference,
            "callback_url" => $callback_url,
            "success_url" => $success_url,
            "tx_callback_url" => $tx_callback_url
        );

        $response = json_decode($this->post($url, $params));

        $returnValue = new \stdClass();

        $returnValue->id = $response->id;
        $returnValue->address = $response->address;
        $returnValue->receiver_currency = $response->receiver_currency;
        $returnValue->name = $response->name;
        $returnValue->description = $response->description;
        $returnValue->reference = $response->reference;
        $returnValue->channel_url = $response->channel_url;
        $returnValue->callback_url = $response->callback_url;
        $returnValue->success_url = $response->success_url;
        $returnValue->created_at = $response->created_at;
        $returnValue->updated_at = $response->updated_at;
        $returnValue->transactions = isset($response->transactions) ? $response->transactions : array();
        $returnValue->txs_callback_url = $response->txs_callback_url;

        return $returnValue;
    }

    /**
     * @return string
     */
    public function getApiBase()
    {
        return $this->apiBase;
    }

    /**
     * @return bool
     */
    public function getSslVerify()
    {
        return $this->sslVerify;
    }

}
