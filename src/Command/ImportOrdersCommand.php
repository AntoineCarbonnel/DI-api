<?php

namespace App\Command;

use App\Entity\Customer;
use App\Entity\Purchases;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ImportOrdersCommand extends Command
{
    protected static $defaultName = 'ugo:orders:import';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import orders from CSV files');
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $question = new Question('Veuillez fournir le chemin du CSV de customers: ');
        $customersFile = $helper->ask($input, $output, $question);

        $question = new Question('Veuillez fournir le chemin du CSV de purchases: ');
        $purchasesFile = $helper->ask($input, $output, $question);

        $this->importCustomers($customersFile, $output);
        $this->importPurchases($purchasesFile, $output);
        
        $this->entityManager->flush();

        $output->writeln('Import completed successfully.');

        return Command::SUCCESS;
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     */
    private function importCustomers(string $filePath, OutputInterface $output): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $errors = [];

        foreach ($csv as $key => $record) {
            $customer = new Customer();

            $title = $record['title'];
            $title = match ($title) {
                '1' => 'M',
                '2' => 'MME',
                default => null,
            };

            $customer->setTitle($title);
            $customer->setLastname($record['lastname']);
            $customer->setFirstname($record['firstname']);
            $customer->setPostalCode($record['postal_code']);
            $customer->setCity($record['city']);
            if ($record['email']) {
                $customer->setEmail($record['email']);
                $this->entityManager->persist($customer);
            } else {
                $errors[] = "Line " . ($key + 1) . ": email missing.";
            }
            $this->entityManager->persist($customer);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $output->writeln($error);
            }
        }
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    private function importPurchases(string $filePath, OutputInterface $output): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $errors = [];

        foreach ($csv as $key => $record) {
            if (!$record['customer_id']) {
                $errors[] = "Line " . ($key + 1) . ": customer_id missing.";
                continue;
            }
            
            $purchase = new Purchases();
            $purchase->setPurchaseIdentifier($record['purchase_identifier']);
            $purchase->setCustomerId($record['customer_id']);
            $purchase->setProductId($record['product_id']);
            $purchase->setQuantity($record['quantity']);
            $purchase->setPrice($record['price']);
            $purchase->setCurrency($record['currency']);
            $purchase->setDate(new \DateTime($record['date']));
            $this->entityManager->persist($purchase);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $output->writeln($error);
            }
        }
    }
}