# Cubits PHP Client Library

An easy way to buy, send, and accept [bitcoin](http://en.wikipedia.org/wiki/Bitcoin) through the [Cubits API](https://cubits.com/help).

This library supports  [API key authentication method](https://cubits.com/help)

## Installation

```
composer require cubits/cubits-php
```

## Usage

Start by [enabling an API Key on your account](https://cubits.com/merchant#integration_tools).


Next, configure the Cubits library via `Cubits::configure` method and create an instance of the client using the `Cubits::withApiKey` method:

```php
 Cubits::configure("https://pay.cubits.com/api/v1/",true);
 $cubits = Cubits::withApiKey($_ENV['Cubits_API_KEY'], $_ENV['Cubits_API_SECRET'])
```

## Examples


### Create an Invoice

```php
$response = $cubits->createInvoice("EUR", "42.95", "Your Order #1234", array(
            "description" => "1 widget at EUR 42.95",
            "reference" =>  "my custom tracking code for this order"
        ));
```

### Get an Invoice

```php
$response = $cubits->getInvoice("ef73a6ed61a8c97427eaae2073b9127b");
```

### Send Money

```php
$response = $cubits->sendMoney("3Pj4mJfK62n9mjMRcHYs96nd15UQLHHhPS","0.25120521");
```

### List Accounts

```php
$response = $cubits->listAccounts();
```

### Request Quote

```php
$response = $cubits->requestQuote("buy","EUR","10","BTC");
```
### Buy

```php
$response = $cubits->buy("EUR","10");
```

### Sell

```php
$response = $cubits->sell("0.150","EUR");
```

### createChannel
```php
$response =  $cubits->createChannel("EUR");
```

### getChannel
```php
  $cubits->getChannel("7ff31a5843887cbaffb9adb3fcb2aebd");
```

### updateChannel
```php
$response = $cubits->updateChannel("7ff31a5843887cbaffb9adb3fcb2aebd", "EUR", "Alpaca underwear");
```

## Security notes

If someone gains access to your API Key they will have complete control of your Cubits account.  This includes the abillity to send all of your bitcoins elsewhere.

For this reason, API access is disabled on all Cubits accounts by default.  If you decide to enable API key access you should take precautions to store your API key securely in your application.  How to do this is application specific, but it's something you should [research](http://programmers.stackexchange.com/questions/65601/is-it-smart-to-store-application-keys-ids-etc-directly-inside-an-application) if you have never done this before.
