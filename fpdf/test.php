<?php 

require('fpdf/fpdf.php');

$pdf = new FPDF();

$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);

$pdf->SetFillColor(36, 96, 84);
$pdf->SetTextColor(225);
$pdf->SetLineWidth(1);

$pdf->SetTopMargin(40);
$pdf->SetLeftMargin(40);
$pdf->SetRightMargin(40);

$pdf->SetAutoPageBreak(true, 35);


$pdf->SetAuthor('Andrew Burgess');
$pdf->SetTitle('Generating PDFs with PHP');
$pdf->SetSubject('PDFs');
$pdf->SetKeywords('php pdf generating fpdf-library');
$pdf->SetCreator('test.php');


$pdf->Cell(100, 16, "Hello World!", 1, 0, 'C', true);

$pdf->Output();
