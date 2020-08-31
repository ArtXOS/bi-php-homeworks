<?php


namespace App;

use App\Invoice\Address;
use App\Invoice\BusinessEntity;
use App\Invoice\Item;

class Builder
{
    /** @var Invoice */
    protected $invoice;

    /**
     * @return Invoice
     */
    public function build(): Invoice
    {
        $this->checkExistence();
        return $this->invoice;
    }

    /**
     * @param string $number
     * @return $this
     */
    public function setNumber(string $number): self
    {
        $this->checkExistence();
        $this->invoice->setNumber($number);
        return $this;
    }


    /**
     * @param string      $name
     * @param string      $vatNumber
     * @param string      $street
     * @param string      $number
     * @param string      $city
     * @param string      $zip
     * @param string|null $phone
     * @param string|null $email
     * @return $this
     */
    public function setSupplier(
        string $name,
        string $vatNumber,
        string $street,
        string $number,
        string $city,
        string $zip,
        ?string $phone = null,
        ?string $email = null
    ): self {

        $this->checkExistence();

        $address = new Address();
        $supplier = new BusinessEntity();

        $address
            ->setStreet($street)
            ->setNumber($number)
            ->setCity($city)
            ->setZipCode($zip)
            ->setPhone($phone)
            ->setEmail($email);

        $supplier
            ->setName($name)
            ->setVatNumber($vatNumber)
            ->setAddress($address);

        $this->invoice->setSupplier($supplier);

        return $this;

    }

    /**
     * @param string      $name
     * @param string      $vatNumber
     * @param string      $street
     * @param string      $number
     * @param string      $city
     * @param string      $zip
     * @param string|null $phone
     * @param string|null $email
     * @return $this
     */
    public function setCustomer(
        string $name,
        string $vatNumber,
        string $street,
        string $number,
        string $city,
        string $zip,
        ?string $phone = null,
        ?string $email = null
    ): self {

        $this->checkExistence();

        $address = new Address();
        $customer = new BusinessEntity();

        $address
            ->setStreet($street)
            ->setNumber($number)
            ->setCity($city)
            ->setZipCode($zip)
            ->setPhone($phone)
            ->setEmail($email);

        $customer
            ->setName($name)
            ->setVatNumber($vatNumber)
            ->setAddress($address);

        $this->invoice->setCustomer($customer);

        return $this;

    }

    /**
     * @param string     $description
     * @param float|null $quantity
     * @param float|null $price
     * @return $this
     */
    public function addItem(string $description, ?float $quantity, ?float $price): self
    {
        $this->checkExistence();

        $item = new Item();
        $item
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setUnitPrice($price);

        $this->invoice->addItem($item);

        return $this;

    }

    private function checkExistence() {
        if($this->invoice === null) {
            $this->invoice = new Invoice();
        }
    }
}
