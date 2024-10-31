# Lalamove PHP Client - (Third Party)

## Overview

The **Lalamove PHP Client** enables seamless interaction with the Lalamove API, allowing you to create quotations, manage orders, retrieve driver information, and handle city and market data with ease. The Client supports both **sandbox** and **production** environments for testing and deployment.

This guide walks you through the setup, core functionalities, and usage of the payload builders provided by the Client.

### Key Features

- **Quotation Management**: Create and retrieve quotations for delivery services.
- **Order Management**: Create, retrieve, update, and cancel delivery orders.
- **Driver Management**: Manage and retrieve details about drivers assigned to orders.
- **Market and City Information**: Access available markets and cities for deliveries.
- **Webhook Handling**: Set up webhooks to receive real-time updates on order statuses.
- **Payload Builders**: Build payloads for quotations, orders, and patch orders easily with validation.

---

## 1. Installation

To install the Client, you can use Composer:

```bash
composer require jmusthakeem/lalamove
```

### Requirements

- PHP 7.0+
- cURL extension enabled in PHP

---

## 2. Initialization

Before making any requests to the Lalamove API, initialize the `LalamoveClient` with your credentials and market information.

### Example:

```php
require 'vendor/autoload.php';

use JM\Lalamove\LalamoveClient;

// Initialize LalamoveClient
$client = new LalamoveClient(
    'yourApiKey',   // Required: API Key
    'yourApiSecret',// Required: API Secret
    'market',       // Required: Your market (e.g., 'SG')
    'sandbox',      // Required: Choose environment: 'sandbox' or 'production'
    'isJson',       // Optional: Response type: default 'true' for JSON 'false' for Object
    'requestId',    // Optional: Unique request ID (optional, can use UUID)
);
```

##### Response: `(isJson = default | true)`

```json
{
  "errors": [
    {
      "id": "ERR_DRIVER_NOT_FOUND",
      "message": "Driver not found."
    }
  ]
}
```

##### Response: `(isJson = false)`

```php
Object
(
    [errors] => Array
        (
            [0] => stdClass Object
                (
                    [id] => ERR_CANCELLATION
                    [message] => Cannot cancel order.
                )
        )
)
```

---

## 3. Quotation Management

The `QuotationPayloadBuilder` helps you build payloads for creating a quotation. You can specify service types, stops, special requests, and more.

### 3.1 Creating a Quotation

Use the builder to set the service type, stops, and other optional details before creating a quotation.

#### Code Example:

```php
$coordinates0 = ['lat' => '1.3475088', 'lng' => '103.8649333'];
$coordinates1 = ['lat' => '1.3480000', 'lng' => '103.8650000'];

// Pickup stop
$stop0 = [
    'coordinates' => $coordinates0,
    'address' => '01-01 Jln Girang, Singapore 358985'
    ];

// Drop-off stop
$stop1 = [
    'coordinates' => $coordinates1,
    'address' => '10-10 Jln Girang, Singapore 358986',
];

// Build the quotation payload for a delivery service
$quotationPayload = $client->quotationPayloadBuilder()
    ->setServiceType('motorcycle')
    ->setStops([$stop0, $stop1])
    ->setLanguage('en')
    ->setScheduleAt('2023-10-18T10:00:00Z')
    ->setIsRouteOptimized(true)
    ->setSpecialRequests(['fragile'])
    ->build();    // Finalizes and builds the payload for the quotation
```

##### Request

```php
$quotation = $client->getQuotation()->create($quotationPayload);
print_r($quotation);
```

##### Expected Output:

```json
{
  "data": {
    "quotationId": "3077495749739347740",
    "scheduleAt": "2024-07-25T10:00:00.00Z",
    "expiresAt": "2024-07-18T03:34:00.00Z",
    "serviceType": "MOTORCYCLE",
    "language": "EN_SG",
    "stops": [
      {
        "stopId": "3077495749739347782",
        "coordinates": {
          "lat": "1.3475088",
          "lng": "103.8649333"
        },
        "address": "01-01 Jln Girang, Singapore 358985"
      },
      {
        "stopId": "3077495749739347783",
        "coordinates": {
          "lat": "1.3480000",
          "lng": "103.8650000"
        },
        "address": "10-10 Jln Girang, Singapore 358986"
      }
    ],
    "isRouteOptimized": false,
    "priceBreakdown": {
      "base": "7.2",
      "totalBeforeOptimization": "7.2",
      "totalExcludePriorityFee": "7.2",
      "total": "7.2",
      "currency": "SGD"
    },
    "distance": {
      "value": "39",
      "unit": "m"
    }
  }
}
```

### 3.2 Retrieving a Quotation

To retrieve an existing quotation, use the `retrieve` method.

#### Code Example:

##### Request

```php
$quotationId = 'yourQuotationId'; // quotation->quotationId => 3077495749739347740

$quotationDetail = $client->getQuotation()->retrieve($quotationId);
print_r($quotationDetail);
```

##### Expected Output:

```json
{
  "data": {
    "quotationId": "3077495749739347740",
    "stops": [],
    "isRouteOptimized": false,
    "priceBreakdown": {
      "base": "7.2",
      "totalBeforeOptimization": "7.2",
      "totalExcludePriorityFee": "7.2",
      "total": "7.2",
      "currency": "SGD"
    },
    "distance": {}
  }
}
```

---

## 4. Order Management

The `OrderPayloadBuilder` helps you construct an order payload using a quotation, specifying the sender, recipients, and other optional details.

### 4.1 Creating an Order

Once you have a valid quotation, you can create an order from it.

#### Code Example:

```php
// Create Sender as an associative array
$senderData = [
    'stopId' => "3077495749739347783", //  'stopId' => quotation->stops[0]->stopId
    'name' => "John",
    'phone' => "+6599887766"
];

// Create Recipients as associative arrays
$recipientData1 = [
    'stopId' => "3077495749739347784", //  'stopId' => quotation->stops[1]->stopId
    'name' => "Deo",
    'phone' => "+6599887755",
    'remarks' => "Offer letter"
];

// Build the order payload
$orderPayload = $client->orderPayloadBuilder()
    ->setQuotationId('yourQuotationId')
    ->setSender($senderData)
    ->addRecipients([$recipientData1])
    ->setIsPODEnabled(true)
    ->setMetadata(['referenceId' => 'yourReferenceId'])
    ->build();    // Finalizes and builds the payload for the order
```

##### Request

```php
$order = $client->getOrder()->create($orderPayload);
print_r($order);
```

##### Expected Output:

```json
{
  "data": {
    "orderId": "149595595901",
    "quotationId": "3077495749739347740",
    "priceBreakdown": {
      "base": "7.2",
      "totalExcludePriorityFee": "7.2",
      "total": "7.2",
      "currency": "SGD"
    },
    "driverId": "",
    "shareLink": "https://share.sandbox.lalamove.com?&lang=en_SG&sign=890&source=891api_wrapper",
    "status": "ASSIGNING_DRIVER",
    "distance": {
      "value": "39",
      "unit": "m"
    },
    "stops": [
      {
        "coordinates": {
          "lat": "1.3475088",
          "lng": "103.8649333"
        },
        "address": "01-01 Jln Girang, Singapore 358985",
        "name": "John",
        "phone": "6599887766",
        "delivery_code": {
          "value": "",
          "status": "Not Applicable"
        }
      },
      {
        "coordinates": {
          "lat": "1.3480000",
          "lng": "103.8650000"
        },
        "address": "10-10 Jln Girang, Singapore 358986",
        "name": "Deo",
        "phone": "6599887755",
        "POD": {
          "status": "PENDING"
        },
        "delivery_code": {
          "value": "",
          "status": "Not Applicable"
        }
      }
    ],
    "metadata": {
      "referenceId": "yourReferenceId"
    },
    "partner": "Lalamove Partner ***"
  }
}
```

### 4.2 Retrieving an Order

You can retrieve details about an order using its order ID.

#### Code Example:

##### Request

```php
$orderId = 'yourOrderId'; // order->orderId => 149595595901

$response = $client->getOrder()->retrieve($orderId);
print_r($response);
```

##### Expected Output:

```json
{
  "data": {
    "orderId": "145151447903",
    "quotationId": "3077495749739347740",
    "priceBreakdown": {},
    "driverId": "80029",
    "shareLink": "https://share.sandbox.lalamove.com?&lang=en_SG&sign=890&source=891api_wrapper",
    "status": "ON_GOING",
    "distance": {},
    "stops": [],
    "metadata": {},
    "partner": "Lalamove Partner 10"
  }
}
```

### 4.3 Edit an Order

The `PatchOrderPayloadBuilder` is used to modify the stops of an existing order.

#### Code Example:

```php
// Create patch stops
$stop0 =[
    'coordinates' => ['lat' => '1.3475088', 'lng' => '103.8649333'],
    'address' => '01-01 Jln Girang, Singapore 358985',
    'name' => "John",
    'phone' => "+6599887766"
];
$stop1 =[
    'coordinates' => ['lat' => '1.3480000', 'lng' => '103.8650000'],
    'address' => '10-10 Jln Girang, Singapore 358986',
    'name' => "Deo",
    'phone' => "+6599887755",
    ];
$stop2 =[
    'coordinates' => ['lat' => '1.28564069', 'lng' => '103.85391781'],
    'address' => '7 Fullerton Rd, #01-01 One Fullerton, 049214',
    'name' => "Sandra",
    'phone' => "+6599887744",
    ];

// Build the patch payload
$patchPayload = $client->patchOrderPayloadBuilder()
    ->withStops([$stop0,$stop1,$stop2])
    ->build();    // Finalizes and builds the payload for the changes in `order`
```

##### Request

```php
$orderId = 'yourOrderId'; // order->orderId => 149595595901
$response = $client->getOrder()->edit($orderId, $patchPayload);

print_r($response);
```

##### Expected Output:

```json
{
  "data": {
    "orderId": "145151447903",
    "quotationId": "3077495749739347740",
    "priceBreakdown": {
      "base": "7.2",
      "extraMileage": "2.5",
      "priorityFee": "4.0",
      "multiStopSurcharge": "2.4",
      "totalExcludePriorityFee": "12.1",
      "total": "16.1",
      "currency": "SGD"
    },
    "driverId": "192859",
    "shareLink": "https://share.sandbox.lalamove.com?&lang=en_SG&sign=890&source=891api_wrapper",
    "status": "ON_GOING",
    "distance": {},
    "stops": [
      {},
      {},
      {
        "coordinates": {
          "lat": "1.2856406",
          "lng": "103.8539179"
        },
        "address": "9 Raffles Place, #05-02 D Tower 4, 049217",
        "name": "Sandra",
        "phone": "6599887744",
        "POD": {},
        "delivery_code": {}
      }
    ],
    "metadata": {},
    "partner": "Lalamove Partner ***"
  }
}
```

### 4.4 Add Priority Fee

Add `Priority Fee` to to the order using order ID.

#### Code Example:

##### Request

```php
$orderId = 'yourOrderId'; // Required: order->orderId => 149595595901
$fee = '4';               // Required: string

$response = $client->getOrder()->addPriorityFee($orderId, $fee);
print_r($response);
```

##### Expected Output:

```json
{
  "data": {
    "orderId": "149595595901",
    "quotationId": "3077495749739347740",
    "priceBreakdown": {
      "base": "7.2",
      "priorityFee": "4.0",
      "totalExcludePriorityFee": "7.2",
      "total": "11.2",
      "currency": "SGD"
    },
    "driverId": "",
    "shareLink": "https://share.sandbox.lalamove.com?&lang=en_SG&sign=890&source=891api_wrapper",
    "status": "ASSIGNING_DRIVER",
    "distance": {},
    "stops": [],
    "metadata": {},
    "partner": "Lalamove Partner ***"
  }
}
```

### 4.4 Cancelling an Order

To cancel an order, simply provide the order ID.

#### Code Example:

##### Request

```php
$orderId = 'yourOrderId'; // order->orderId => 149595595901
$response = $client->getOrder()->cancel($orderId);

if ($response === true) {
    echo "Order canceled successfully.";
} else {
    echo "Error: " . $response['error'];
}
```

This returns `true` if cancelled successfully

##### Expected Output:

```console
Order canceled successfully.
```

---

## 5. Driver Management

The `Driver` class allows you to manage drivers assigned to orders.

### 5.1 Retrieving Driver Details

You can retrieve information about the driver assigned to a particular order.

#### Code Example:

##### Request

```php
$orderId = 'yourOrderId'; // order->orderId => 149595595901
$driverId = 'yourDriverId'; // order->driverId => 80029

$response = $client->getDriver()->retrieve($orderId, $driverId);
print_r($response);
```

##### Expected Output:

```json
{
  "data": {
    "driverId": "80029",
    "name": "TestDriver 11222",
    "phone": "+6522211222",
    "plateNumber": "**438890*",
    "photo": ""
  }
}
```

### 5.2 Changing a Driver Assignment

To change a driver assignment for an order:

#### Code Example:

##### Request

```php
$orderId = 'yourOrderId'; // order->orderId => 149595595901
$driverId = 'yourDriverId'; // order->driverId => 80029

$response = $client->getDriver()->cancel($orderId, $driverId);

if ($response === true) {
    echo "Driver canceled successfully.";
} else {
    echo "Error: " . $response['error'];
}
```

This returns `true` if cancelled successfully.

##### Expected Output:

```console
Driver canceled successfully
```

---

## 6. Market and City Management

The Client provides access to available markets and cities.

### 6.1 Retrieving Markets

To get a list of available markets:

#### Code Example:

##### Request

```php
// Returns the service list based on the market set for the client.
$response = $client->getMarkets()->retrieve();
print_r($response);

// Returns the service list based on the market passed to the method.
$market = 'market'; // Optional eg TH, HK, MY, etc.
$response = $client->getMarkets()->retrieve($market);
print_r($response);
```

##### Expected Output:

<details>
  <summary>View Response</summary>

```json
{
  "data": [
    {
      "locode": "SG SIN",
      "name": "Singapore",
      "services": [
        {
          "key": "CAR",
          "description": "Car delivery of medium size items",
          "dimensions": {},
          "load": {},
          "specialRequests": [
            {
              "name": "PURCHASE_SERVICE_FOOD",
              "description": "What type of items do you need help buying? \u00b7 Food and beverage only",
              "parent_type": "What type of items do you need help buying?",
              "max_selection": 1,
              "effective_time": "",
              "offline_time": ""
            }
          ],
          "deliveryItemSpecification": {}
        }
      ]
    }
  ]
}
```

</details>

### 6.2 Retrieving a City by ID

To retrieve details of a city by its ID:

#### Code Example:

##### Request

```php
// Returns the service list based on the city Id for the market set in the client.
$cityId = 'YourCityId'; // Eg: TH BKK, SG SIN, etc.
$response = $client->getCity()->retrieve($cityId);
print_r($response);

$market = 'market'; // Optional: eg TH, HK, MY, etc.
// Returns the service list based on the city Id and the market passed to the method.
$client->getCity()->retrieve($cityId, $market)
print_r($response);
```

##### Expected Output:

<details>
  <summary>View Response</summary>

```json
{
  "data": {
    "id": "TH BKK",
    "name": "Bangkok",
    "services": [
      {
        "key": "CAR",
        "description": "Ideal for small to medium goods requiring extra care",
        "dimensions": {},
        "load": {},
        "specialRequests": [
          {
            "name": "PURCHASE_SERVICE_4",
            "description": "Tell your driver how much to pay \u00b7 Pay THB 4,500 \u2013 6,000",
            "parent_type": "Tell your driver how much to pay",
            "max_selection": 1,
            "effective_time": "",
            "offline_time": ""
          }
        ],
        "deliveryItemSpecification": {
          "quantity": "^[1-9][0-9]*$",
          "categories": [
            "FOOD_AND_BEVERAGE",
            "HOUSEHOLD_ITEMS_OR_FURNITURE",
            "OFFICE_ITEMS_OR_STATIONERIES",
            "ELECTRONICS",
            "APPAREL_AND_ACCESSORIES",
            "FRAGILE_ITEMS"
          ],
          "handlingInstructions": [
            "FRAGILE_OR_HANDLE_WITH_CARE",
            "DO_NOT_STACK",
            "KEEP_DRY",
            "KEEP_UPRIGHT"
          ]
        }
      }
    ]
  }
}
```

</details>

### 6.3 Retrieving service type by the Item weight

To retrieve ServiceType based on the item weight available for the market:

#### Code Example:

##### Request

```php
// Retrieve `service type` based on the item weight available .
$weight = 100;          // Required, weigh in kg (float)
$cityId = 'YourCityId'; // Required, Eg: TH BKK, SG SIN, etc.
$response = $client->getCity()->getServiceKeyByLoad($weight);


// Retrieve `service type` based on the item weight for the given city & market.
$weight = 100;          // Required, weigh in kg (float)
$cityId = 'YourCityId'; // Required, Eg: TH BKK, SG SIN, etc.
$market = 'market';     // Optional, eg: TH, HK, MY, etc.
$client->getCity()->getServiceKeyByLoad($weight, $cityId, $market) :
print_r($response);
```

##### Expected Output:

```json
{
  "data": {
    "serviceType": "MINIVAN"
  }
}
```

---

## 7. Exception Handling

Ensure you handle exceptions in your code to catch errors returned by the API or Client.

### Code Example:

##### Request

```php
try {
    $response = $client->getOrder()->create($orderPayload);
    if (isset($response['error'])) {
        echo "Error: " . $response['error'];
    } else {
        print_r($response);
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
```

---

## Conclusion

This guide provides the essential steps for building payloads, creating quotations and orders, and managing drivers and city information using the Lalamove PHP Client. For more advanced features or further details, refer the official Lalamove API documentation.

---

## License

This project is licensed under the MIT License.
