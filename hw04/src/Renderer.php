<?php


namespace App;

define('COLUMN_WIDTH', 86);

use Fpdf\Fpdf;

class Renderer extends Fpdf
{
    private $invoice;

    public function render(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->createPDF();
        return $this->Output();
    }

    private function createPDF() {
        $this->SetMargins(20,20,20);
        $this->AddPage('P', 'A4');
        $this->createSupplierCell();
        $this->createCustomerCell();
        $this->createContentCells();
    }

    function Header()
    {
        $this->SetFont('Arial','', 12);
        $this->Cell(50,10,"FAKTURA - DOKLAD c. ".$this->invoice->getNumber(),0,0,'L');
    }

    private function createCustomerCell() {

        $this->SetXY(COLUMN_WIDTH+20,35);
        $this->Cell(COLUMN_WIDTH,55,'',1,0,'L');

        $this->SetXY(COLUMN_WIDTH+22,35);
        $this->SetFont('Arial','B', 12);
        $this->Cell(COLUMN_WIDTH,8,'Odberatel',0,0,'L');

        $customer = $this->invoice->getCustomer();
        $this->fillEntityCell($customer, COLUMN_WIDTH);
    }

    private function createSupplierCell() {

        $this->SetXY(20,35);
        $this->Cell(COLUMN_WIDTH,55,'',1,0,'L');

        $this->SetXY(22,35);
        $this->SetFont('Arial','B', 12);
        $this->Cell(COLUMN_WIDTH,8,'Dodavatel',0,0,'L');

        $supplier = $this->invoice->getSupplier();
        $this->fillEntityCell($supplier, 0);
    }

    private function fillEntityCell($entity, $offset) {

        $entityAddress = $entity->getAddress();

        $this->SetFont('Arial','', 10);

        $this->SetXY(22+$offset,45);
        $this->Cell(COLUMN_WIDTH,8,$entity->getName(),0,0,'L');

        $this->SetXY(22+$offset,50);
        $this->Cell(COLUMN_WIDTH,8,$entityAddress->getStreet()." ".$entityAddress->getNumber(),0,0,'L');

        $this->SetXY(22+$offset,55);
        $this->Cell(COLUMN_WIDTH,8,$entityAddress->getZipCode()." ".$entityAddress->getCity(),0,0,'L');

        $this->SetXY(22+$offset,65);
        $this->Cell(COLUMN_WIDTH,8,$entity->getVatNumber(),0,0,'L');

        if($entityAddress->getPhone() === null && $entityAddress->getEmail() === null) return;

        if($entityAddress->getPhone() !== null && $entityAddress->getEmail() !== null) {
            $this->SetXY(22+$offset,75);
            $this->Cell(COLUMN_WIDTH,8,$entityAddress->getPhone(),0,0,'L');
            $this->SetXY(22+$offset,80);
            $this->Cell(COLUMN_WIDTH,8,$entityAddress->getEmail(),0,0,'L');
        } else if($entityAddress->getPhone() !== null && $entityAddress->getEmail() === null){
            $this->SetXY(22+$offset,75);
            $this->Cell(COLUMN_WIDTH,8,$entityAddress->getPhone(),0,0,'L');
        } else {
            $this->SetXY(22+$offset,75);
            $this->Cell(COLUMN_WIDTH,8,$entityAddress->getEmail(),0,0,'L');
        }
    }

    private function createContentCells() {
        $this->SetXY(20,95);
        $this->SetFont('Arial','B', 11);
        $this->Cell(COLUMN_WIDTH*2/4,7," Polozka",1,0,'L');
        $this->Cell(COLUMN_WIDTH*2/4,7," Pocet m.j",1,0,'L');
        $this->Cell(COLUMN_WIDTH*2/4,7," Cena za m.j",1,0,'L');
        $this->Cell(COLUMN_WIDTH*2/4,7," Celkem",1,0,'L');

        $items = $this->invoice->getItems();

        $offset = 7;
        $yPos = 95;

        $this->SetFont('Arial','', 11);

        foreach ($items as $item) {
            $yPos += $offset;
            $this->SetXY(20,$yPos);

            $this->Cell(COLUMN_WIDTH*2/4,7,$item->getDescription(),1,0,'L');
            $this->Cell(COLUMN_WIDTH*2/4,7,$item->getQuantity(),1,0,'R');

            $price = $item->getUnitPrice();
            $total = $item->getTotalPrice();

            $this->Cell(COLUMN_WIDTH*2/4,7,number_format($price, 2, ',', ' '),1,0,'R');
            $this->Cell(COLUMN_WIDTH*2/4,7,number_format($total, 2, ',', ' '),1,0,'R');
        }

        $this->SetXY(20,$yPos + $offset);
        $this->SetFont('Arial','B', 12);
        $this->Cell(COLUMN_WIDTH*2/4*3,7,'Celkem',1,0,'L');
        $this->Cell(COLUMN_WIDTH*2/4,7,number_format($this->invoice->getTotalPrice(),2,',',' '),1,0,'R');
    }

}
