<?php

        
        session_start();
        require_once '../../class.user.php';
        $user = new USER();
        
        if($user->is_logged_in()!=true)
        {
            $user->redirect('login.php');
        }
        
        $userid=$_SESSION['userSession'];
        $CompID=$_SESSION['userCompID'];
    
    
    
    /** Set default timezone (will throw a notice otherwise) */
    date_default_timezone_set('Asia/Kolkata');
    
    
    
    include '../PHPExcel/IOFactory.php';
    
    
    
    
    class DebugSoapClient extends SoapClient {
        public $sendRequest = true;
        public $printRequest = true;
        public $formatXML = true;
        
        public function __doRequest($request, $location, $action, $version, $one_way=0) {
            if ( $this->printRequest ) {
                if ( !$this->formatXML ) {
                    $out = $request;
                }
                else {
                    $doc = new DOMDocument;
                    $doc->preserveWhiteSpace = false;
                    $doc->loadxml($request);
                    $doc->formatOutput = true;
                    $out = $doc->savexml();
                }
                echo $out;
            }
            
            if ( $this->sendRequest ) {
                return parent::__doRequest($request, $location, $action, $version, $one_way);
            }
            else {
                return '';
            }
        }
    }
    
    
    
    
    
    if(isset($_FILES['file']['name'])){
        
        
        $file_name = $_FILES['file']['name'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
        
        //Checking the file extension
        if($ext == "xlsx"){
            
            
            $file_name = $_FILES['file']['tmp_name'];
            $inputFileName = $file_name;
            
            ////// Courier Vendor Details/////
            
            $couriervendor=$_POST["couriervendor"];
            $service=$_POST["services"];
            $account_number=$_POST["accountnumber"];
            $purpose=$_POST["purpose"];
            $shipmentcontent=$_POST["shipmentcontent"];
            
            
            $shipment_date=$_POST["date"];
            $nowtime = time();
            $fedex_shipment_date=$shipment_date."T".$nowtime;
            $convert_date = new DateTime($shipment_date);
            $shipment_date1 = date_format($convert_date, 'd-M-Y');
            

            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                    . '": ' . $e->getMessage());
            }
            
            
            //Table used to display the contents of the file
            //echo '<center><table style="width:50%;" border=1>';
            
            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
           
            
            
            //echo $highestRow."----->".$highestColumn."</br>";
            
            $rad=0;
            
            
            //  Loop through each row of the worksheet in turn
            for ($Excelrow =2; $Excelrow <= $highestRow; $Excelrow++) {
                //  Read a row of data into an array
                $rowData= $sheet->rangeToArray('A' . $Excelrow . ':' . $highestColumn . $Excelrow,
                                                NULL, TRUE, FALSE);
                
                
                
                $from_pin=$rowData[0][6];
                $to_pin=$rowData[0][14];
                
                
                //////////////////////////////////////
                /////// Sender Details///////////////
                /////////////////////////////////////
                $sender_vendorid=$rowData[0][0];
                $sender_name=$rowData[0][1];
                $sender_company=$rowData[0][2];
                $sender_address=$rowData[0][3];
                $sender_city=$rowData[0][4];
                $sender_state=$rowData[0][5];
                $sender_phone=$rowData[0][7];
                $sender_info=array($from_pin,$sender_name,$sender_company,$sender_address,$sender_city,$sender_state,$sender_phone);
                
                //////////////////////////////////////
                /////// Receiver Details///////////////
                /////////////////////////////////////
                $receiver_vendorid=$rowData[0][8];
                $receiver_name=$rowData[0][9];
                $receiver_company=$rowData[0][10];
                $receiver_city=$rowData[0][12];
                $receiver_state=$rowData[0][13];
                $receiver_address=$rowData[0][11];
                $receiver_phone=$rowData[0][15];
                $receiver_info=array($to_pin,$receiver_name,$receiver_company,$receiver_address,$receiver_city,$receiver_state,$receiver_phone);
                
                
                
                $length=$rowData[0][20];
                $breath=$rowData[0][21];
                $height=$rowData[0][22];
                $TotalWeight=$rowData[0][23];
                //$purpose=$rowData[0][20];
                
                $CollectableAmount=$rowData[0][17];
                
                $COD=$rowData[0][16];
                $packagecount=1;
                //$shipmentcontent=$rowData[0][25];
                
                $uid=$rowData[0][18];
                $randUID=randomString();  // Only for BlueDart////
                
                $InvoiceValue=$rowData[0][19];
                
                
                $package_details=array($TotalWeight,$length,$breath,$height,$purpose,$CollectableAmount);
                
                
                $Commodity=$rowData[0][24];
                $Commodity_desc=$rowData[0][25];
                $CommodityQuantity=$rowData[0][26];
                $CommodityWeight=$rowData[0][27];
                $CommodityValue=$rowData[0][28];
                
            
                if($couriervendor=="FedEx"){
                    
                    include( '../../AirwayBill/FedEx/ShipWebServiceClientExcel.php');
                   
                }
                else if($couriervendor=="BlueDart"){
                   
                    include('../../AirwayBill/BlueDart/CallAwbServiceExcel.php');
                }
                else{
                    
                    echo "Do Nothing";
                }
                
                //////////////////////////////////
                ////// Creating Output Excel//////
                //////////////////////////////////
                
                $ExcelInputData=array();
                
                foreach($rowData[0] as $k=>$v){
                    
                    array_push($ExcelInputData,$v);
                }
                
                array_push($ExcelInputData,$status);
                array_push($ExcelInputData,$filename);
                array_push($ExcelInputData,$filepath);
                
                $ExcelOutputData[$rad]= $ExcelInputData;
               
                ++$rad;
                
                
            }
            
            
            $OutputExcelName=CreateOutputExcel($ExcelOutputData,$highestRow);
            
           $redirectflag= DownloadZip($ExcelOutputData,$highestRow,$OutputExcelName);
            
           
          
        }
        
        else{
            echo '<p style="color:red;">Please upload file with xlsx extension only</p>'; 
        }
        
    }
    else{
        
        
    }
    
    function CreateOutputExcel($Data,$maxrows){
        
    
        // create new PHPExcel object
        $objPHPExcel = new PHPExcel;
        
        // set default font
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        
        // set default font size
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
        
        // create the writer
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        
        
        
        /**
         
         * Define currency and number format.
         
         */
        
        // currency format, € with < 0 being in red color
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        
        // number format, with thousands separator and two decimal points.
        $numberFormat = '#,#0.##;[Red]-#,#0.##';
        
        
        
        // writer already created the first sheet for us, let's get it
        $objSheet = $objPHPExcel->getActiveSheet();
        
        // rename the sheet
        $objSheet->setTitle('Generate AirwayBill');
        
        
        
        // let's bold and size the header font and write the header
        // as you can see, we can specify a range of cells, like here: cells from A1 to A4
        $objSheet->getStyle('A1:AE1')->getFont()->setBold(true)->setSize(12);
        
        
        
        // write header
        
        $objSheet->getCell('A1')->setValue('Sender_VendorID');
        $objSheet->getCell('B1')->setValue('Sender_Name');
        $objSheet->getCell('C1')->setValue('Sender_CompmanyName');
        $objSheet->getCell('D1')->setValue('Sender_Address');
        $objSheet->getCell('E1')->setValue('Sender_City');
        $objSheet->getCell('F1')->setValue('Sender_State');
        $objSheet->getCell('G1')->setValue('Sender_Pincode');
        $objSheet->getCell('H1')->setValue('Sender_PhoneNumber');
        $objSheet->getCell('I1')->setValue('Receiver_VendorID');
        $objSheet->getCell('J1')->setValue('Receiver_Name');
        $objSheet->getCell('K1')->setValue('Receiver_CompmanyName');
        $objSheet->getCell('L1')->setValue('Receiver_Address');
        $objSheet->getCell('M1')->setValue('Receiver_City');
        $objSheet->getCell('N1')->setValue('Receiver_State');
        $objSheet->getCell('O1')->setValue('Receiver_Pincode');
        $objSheet->getCell('P1')->setValue('Receiver_PhoneNumber');
        $objSheet->getCell('Q1')->setValue('COD');
        $objSheet->getCell('R1')->setValue('Amount');
        $objSheet->getCell('S1')->setValue('UniqueID');
        $objSheet->getCell('T1')->setValue('InvoiceValue');
        $objSheet->getCell('U1')->setValue('Length');
        $objSheet->getCell('V1')->setValue('Breath');
        $objSheet->getCell('W1')->setValue('Height');
        $objSheet->getCell('X1')->setValue('Weight');
        $objSheet->getCell('Y1')->setValue('Commodity');
        $objSheet->getCell('Z1')->setValue('CommodityDescription');
        $objSheet->getCell('AA1')->setValue('Quantity');
        $objSheet->getCell('AB1')->setValue('UnitWeight');
        $objSheet->getCell('AC1')->setValue('UnitValue');
        $objSheet->getCell('AD1')->setValue('Status');
        $objSheet->getCell('AE1')->setValue('FileName');
        
        // Passing value in rows///
                                            
        for($i=2; $i<=$maxrows; ++$i){
            
            $j=$i-2;
            
            $objSheet->getCell('A'.$i)->setValue($Data[$j][0]);
            $objSheet->getCell('B'.$i)->setValue($Data[$j][1]);
            $objSheet->getCell('C'.$i)->setValue($Data[$j][2]);
            $objSheet->getCell('D'.$i)->setValue($Data[$j][3]);
            $objSheet->getCell('E'.$i)->setValue($Data[$j][4]);
            $objSheet->getCell('F'.$i)->setValue($Data[$j][5]);
            $objSheet->getCell('G'.$i)->setValue($Data[$j][6]);
            $objSheet->getCell('H'.$i)->setValue($Data[$j][7]);
            $objSheet->getCell('I'.$i)->setValue($Data[$j][8]);
            $objSheet->getCell('J'.$i)->setValue($Data[$j][9]);
            $objSheet->getCell('K'.$i)->setValue($Data[$j][10]);
            $objSheet->getCell('L'.$i)->setValue($Data[$j][11]);
            $objSheet->getCell('M'.$i)->setValue($Data[$j][12]);
            $objSheet->getCell('N'.$i)->setValue($Data[$j][13]);
            $objSheet->getCell('O'.$i)->setValue($Data[$j][14]);
            $objSheet->getCell('P'.$i)->setValue($Data[$j][15]);
            $objSheet->getCell('Q'.$i)->setValue($Data[$j][16]);
            $objSheet->getCell('R'.$i)->setValue($Data[$j][17]);
            $objSheet->getCell('S'.$i)->setValue($Data[$j][18]);
            $objSheet->getCell('T'.$i)->setValue($Data[$j][19]);
            $objSheet->getCell('U'.$i)->setValue($Data[$j][20]);
            $objSheet->getCell('V'.$i)->setValue($Data[$j][21]);
            $objSheet->getCell('W'.$i)->setValue($Data[$j][22]);
            $objSheet->getCell('X'.$i)->setValue($Data[$j][23]);
            $objSheet->getCell('Y'.$i)->setValue($Data[$j][24]);
            $objSheet->getCell('Z'.$i)->setValue($Data[$j][25]);
            $objSheet->getCell('AA'.$i)->setValue($Data[$j][26]);
            $objSheet->getCell('AB'.$i)->setValue($Data[$j][27]);
            $objSheet->getCell('AC'.$i)->setValue($Data[$j][28]);
            $objSheet->getCell('AD'.$i)->setValue($Data[$j][29]);
            $objSheet->getCell('AE'.$i)->setValue($Data[$j][30]);
            
            
        }
        
        
        // autosize the columns
        $objSheet->getColumnDimension('A')->setAutoSize(true);
        $objSheet->getColumnDimension('B')->setAutoSize(true);
        $objSheet->getColumnDimension('C')->setAutoSize(true);
        $objSheet->getColumnDimension('D')->setAutoSize(true);
        $objSheet->getColumnDimension('E')->setAutoSize(true);
        $objSheet->getColumnDimension('F')->setAutoSize(true);
        $objSheet->getColumnDimension('G')->setAutoSize(true);
        $objSheet->getColumnDimension('H')->setAutoSize(true);
        $objSheet->getColumnDimension('I')->setAutoSize(true);
        $objSheet->getColumnDimension('J')->setAutoSize(true);
        $objSheet->getColumnDimension('K')->setAutoSize(true);
        $objSheet->getColumnDimension('L')->setAutoSize(true);
        $objSheet->getColumnDimension('M')->setAutoSize(true);
        $objSheet->getColumnDimension('N')->setAutoSize(true);
        $objSheet->getColumnDimension('O')->setAutoSize(true);
        $objSheet->getColumnDimension('P')->setAutoSize(true);
        $objSheet->getColumnDimension('Q')->setAutoSize(true);
        $objSheet->getColumnDimension('S')->setAutoSize(true);
        $objSheet->getColumnDimension('T')->setAutoSize(true);
        $objSheet->getColumnDimension('U')->setAutoSize(true);
        $objSheet->getColumnDimension('V')->setAutoSize(true);
        $objSheet->getColumnDimension('W')->setAutoSize(true);
        $objSheet->getColumnDimension('X')->setAutoSize(true);
        $objSheet->getColumnDimension('Y')->setAutoSize(true);
        $objSheet->getColumnDimension('Z')->setAutoSize(true);
        $objSheet->getColumnDimension('AA')->setAutoSize(true);
        $objSheet->getColumnDimension('AB')->setAutoSize(true);
        $objSheet->getColumnDimension('AC')->setAutoSize(true);
        $objSheet->getColumnDimension('AD')->setAutoSize(true);
        $objSheet->getColumnDimension('AE')->setAutoSize(true);
        
        $string=randomString();
        
        $ExcelFileName=$string.".xlsx";
       
        $objWriter->save('../../AirwayBill/ExcelUpload/'.$ExcelFileName);
         
        return $ExcelFileName;
    }
    
    
    function randomString(){
        
        //// Generating Randon file name and Saving in Airway Bill Folder////
        
        $characters = 'abcdefghijklmnopqrstuvwxyz123456789';
        
        $string = '';
        $max = strlen($characters) - 1;
        $random_string_length=10;
        
        for ($i = 0; $i < $random_string_length; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        
        return $string;

    }
    
    
    function DownloadZip($Data,$maxrows,$OutputExcel){
        
        $flag=0;
        $zipname = 'file.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        
        
        
        for($i=0; $i<($maxrows-1); ++$i){
            
            
            if($Data[$i][29]=="Success"){
                
                $zip->addFile('../../'.$Data[$i][31],$Data[$i][30]);
            }
            
            
        }
        
        /// Adding Output Excel into Zip file/////////
        
        $zip->addFile('../../'.'AirwayBill/ExcelUpload/'.$OutputExcel,$OutputExcel);
        
        $zip->close();
        
        setcookie("DownloadFlag", 1,time()+60,"/");
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        header("Pragma: no-cache");
        header("Expires: 0");
        
        flush();
        readfile($zipname);
        
        // delete file
        unlink($zipname);
        
        $flag=1;
        
        return $flag;
        

    }
    
    ?>



