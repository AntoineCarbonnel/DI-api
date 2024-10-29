<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\PurchasesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{

    private function setUpSerializer(): SerializerInterface
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ])];

        return new Serializer($normalizers, $encoders);
    }

    #[Route('/api/customers', name: 'api_customers', methods: ['GET'])]
    public function getCustomers(CustomerRepository $customerRepository): JsonResponse
    {
        $serializer = $this->setUpSerializer();
        $customers = $customerRepository->findAll();
        $customerData = array_map(function ($customer) {
            return [
                'id' => $customer->getId(),
                'title' => $customer->getTitle(),
                'lastname' => $customer->getLastname(),
                'firstname' => $customer->getFirstname(),
                'postal_code' => $customer->getPostalCode(),
                'city' => $customer->getCity(),
                'email' => $customer->getEmail(),
            ];
        }, $customers);
        $jsonContent = $serializer->serialize($customerData, 'json');
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/api/customers/{id}/purchases', name: 'api_customer_purchases', methods: ['GET'])]
    public function getCustomerPurchases(Customer $customer, PurchasesRepository $purchasesRepository): JsonResponse
    {
        $serializer = $this->setUpSerializer();
        $purchases = $purchasesRepository->findBy(['customer_id' => $customer->getId()]);

        $purchaseData = array_map(function ($purchase) {
            return [
                'purchase_identifier' => $purchase->getPurchaseIdentifier(),
                'product_id' => $purchase->getProductId(),
                'quantity' => $purchase->getQuantity(),
                'price' => $purchase->getPrice(),
                'currency' => $purchase->getCurrency(),
                'date' => $purchase->getDate()->format('Y-m-d H:i:s'),
            ];
        }, $purchases);

        $customer = [
            'lastname' => $customer->getLastname(),
            'purchases' => $purchaseData,
        ];

        $jsonContent = $serializer->serialize($customer, 'json');
        return new JsonResponse($jsonContent, 200, [], true);
    }
}