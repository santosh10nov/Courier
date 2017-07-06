<?php
include 'PDFMerger.php';

$pdf = new PDFMerger;

$pdf->addPDF('/Applications/XAMPP/xamppfiles/htdocs/Courier/Merger/794603357197.pdf', '1, 3, 4')
	->addPDF('/Applications/XAMPP/xamppfiles/htdocs/Courier/Merger/ProForma.pdf', 'all')
	->merge('file', '/Applications/XAMPP/xamppfiles/htdocs/Courier/Merger/TEST.pdf');
	
	//REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
	//You do not need to give a file path for browser, string, or download - just the name.
