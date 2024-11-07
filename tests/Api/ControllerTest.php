<?php

namespace App\Tests\Api;

use App\Entity\Customer;
use App\Entity\Purchases;
use App\Repository\CustomerRepository;
use App\Repository\PurchasesRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ControllerTest extends WebTestCase
{
    public function testGetCustomerPurchases()
    {
        error_log('- - - - - - - - - - - - - - -');
        error_log('🚀 Starting test: testGetCustomerPurchases');
        error_log('- - - - - - - - - - - - - - -');

        $client = static::createClient();

        error_log('✅ Client created');

        $purchasesRepository = $this->createMock(PurchasesRepository::class);
        $purchasesRepository->method('findBy')->willReturn([
            (new Purchases())->setCustomerId(1)->setPurchaseIdentifier('12345')->setProductId('1')->setQuantity(2)->setPrice(20)->setCurrency('USD')->setDate(new DateTime('2024-01-01')),
            (new Purchases())->setCustomerId(1)->setPurchaseIdentifier('67890')->setProductId('2')->setQuantity(1)->setPrice(30)->setCurrency('USD')->setDate(new DateTime('2024-01-02')),
        ]);

        error_log('🛠️ PurchasesRepository mock created and configured');

        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository->method('find')->willReturn(
            (new Customer())->setId(1)->setLastname('Norris')
        );

        error_log('🛠️ CustomerRepository mock created and configured');

        $container = $client->getContainer();
        $container->set(CustomerRepository::class, $customerRepository);
        $container->set(PurchasesRepository::class, $purchasesRepository);

        error_log('🔧 Repositories set in container');

        $client->request('GET', '/api/customers/1/purchases');

        error_log('📡 Request made to /api/customers/1/purchases');

        $response = $client->getResponse();
        $content = $response->getContent();

        error_log('📊 Status Code: ' . $response->getStatusCode());
        error_log('📄 Response Content: ' . $content);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        error_log('✅ Status code assertion passed');

        $this->assertJson($content);

        error_log('✅ Response is valid JSON');

        $this->assertJsonStringEqualsJsonString(json_encode([
            'lastname' => 'Norris',
            'purchases' => [
                [
                    'currency' => 'USD',
                    'date' => "2024-01-01 00:00:00",
                    'price' => 20,
                    'product_id' => '1',
                    'purchase_identifier' => '12345',
                    'quantity' => 2
                ],
                [
                    'currency' => 'USD',
                    'date' => "2024-01-02 00:00:00",
                    'price' => 30,
                    'product_id' => '2',
                    'purchase_identifier' => '67890',
                    'quantity' => 1
                ]
            ]
        ]), $content);

        error_log('✅ JSON content assertion passed');
    }

    public function testGetCustomers()
    {
        error_log('- - - - - - - - - - - - - - -');
        error_log('🚀 Starting test: testGetCustomers');
        error_log('- - - - - - - - - - - - - - -');

        $client = static::createClient();

        error_log('✅ Client created');

        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository->method('findAll')->willReturn([
            (new Customer())->setId(1)->setLastname('Norris')->setTitle('Mr')->setFirstname('Chuck')->setPostalCode('12345')->setCity('Dallas')->setEmail('chuck.norris@example.com'),
            (new Customer())->setId(2)->setLastname('Smith')->setTitle('Ms')->setFirstname('Anna')->setPostalCode('67890')->setCity('New York')->setEmail('anna.smith@example.com')
        ]);

        error_log('🛠️ CustomerRepository mock created and configured');

        $container = $client->getContainer();
        $container->set(CustomerRepository::class, $customerRepository);

        error_log('🔧 CustomerRepository set in container');

        $client->request('GET', '/api/customers');

        error_log('📡 Request made to /api/customers');

        $response = $client->getResponse();
        $content = $response->getContent();

        error_log('📊 Status Code: ' . $response->getStatusCode());
        error_log('📄 Response Content: ' . $content);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        error_log('✅ Status code assertion passed');

        $this->assertJson($content);

        error_log('✅ Response is valid JSON');

        $this->assertJsonStringEqualsJsonString(json_encode([
            [
                'id' => 1,
                'title' => 'Mr',
                'lastname' => 'Norris',
                'firstname' => 'Chuck',
                'postal_code' => '12345',
                'city' => 'Dallas',
                'email' => 'chuck.norris@example.com'
            ],
            [
                'id' => 2,
                'title' => 'Ms',
                'lastname' => 'Smith',
                'firstname' => 'Anna',
                'postal_code' => '67890',
                'city' => 'New York',
                'email' => 'anna.smith@example.com'
            ]
        ]), $content);

        error_log('✅ JSON content assertion passed');
    }
}