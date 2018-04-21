<?php
require($_SERVER['DOCUMENT_ROOT'].'/include/fpdf/fpdf.php');
$pdf=new FPDF();
$pdf-> AddFont('TimesNRCyrMT','','7454.php');
$pdf-> SetFont('TimesNRCyrMT','',12);
//set document properties
$pdf->SetAuthor('русский изык');
$pdf->SetTitle('русский изык');
//set font for the entire document
$pdf->SetFont('Helvetica','B',20);
$pdf->SetTextColor(50,60,100);
//set up a page
$pdf->AddPage('P');
$pdf->SetDisplayMode(real,'default');
//insert an image and make it a link
$pdf->Image('http://chart.apis.google.com/chart?cht=qr&chs=300x300&chl=zdz-online.ru?agreementId='.'AAAAAB',10,20,33,0,'PNG');
//display the title with a border around it
$pdf->SetXY(50,20);
$pdf->SetDrawColor(50,60,100);
$pdf->Cell(100,10,'FPDF Tutorial',1,0,'C',0);
//Set x and y position for the main text, reduce font size and write content
$pdf->SetXY (10,50);
$pdf->SetFontSize(10);
$pdf->Write(5,'Русские хакеры. Congratulations! You have generated a PDF.');
//Output the document
$pdf->Output('example1.pdf','I');
?>