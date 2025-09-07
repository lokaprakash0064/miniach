<?php

/**
 * Class file to export data in csv or excel format
 *
 * @author Lokaprakash Behera <lokaprakash.behera@gmail.com>
 * @version Build 1.0
 * @package Doculoss
 * @outputBuffering disabled
 */
// common include file required MIND THE PATH (__DIR__ INSTEAD OF __FILE__)

//require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'PhpSpreadsheet' . DIRECTORY_SEPARATOR . 'index.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include.php';

// Check if Excel class exists or not and define if not
if (!class_exists('Excel')) {
    class Excel
    {
        
        /**
         * Private static variable to hold singleton class object
         *
         * @access    private
         * @staticvar
         * @var       object  The current class object
         */
        private static $_classObject;
        
        // }}}
        // {{{ getObject()

        /**
         * Method to return singleton class object.
         * returns current class object if already present
         * else creates one
         *
         * @return object  The current class object
         * @access public
         * @static
         */
        public static function getObject()
        {
            // check if class not instantiated
            if (self::$_classObject === null) {
                // then create a new instance
                self::$_classObject = new self();
            }
            // return the class object to be used
            return self::$_classObject;
        }
        
        // }}}
        // {{{ exportAsCsv()

        /**
         * Method to create a csv file of user data
         * and download the same
         *
         * @access public
         * @static
         */
        public function exportAsCsv()
        {
            $sql = 'select rms_rm_num, rms_name, rms_length, rms_width, rms_height from rooms '
                    . 'where rms_uid = ?';
            $rData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            //var_dump($rData);exit;
            $sql = 'select su_name, su_compName, su_phn, su_uname, st_zip, st_city, st_state from signup '
                    . ' left join state_data on su_st_id = st_id where su_id = ?';
            $uData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            $sql = 'select rm_item_name, rm_sl_no, rm_date, rm_item_price from roombuild WHERE rm_uid = ? and rm_room_num = ?';
            $rmItmRes = DbOperations::getObject()->prepareQuery($sql);
            /*$helper = new Sample();
            if ($helper->isCli()) {
                $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
                return;
            }*/
            $functions = spl_autoload_functions();
            foreach($functions as $function) {
                spl_autoload_unregister($function);
            }
            require_once 'phpspreadsheet/autoload.php';
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            // Set document properties
            $spreadsheet->getProperties()->setCreator($uData[0]['su_name'])
                    ->setLastModifiedBy($uData[0]['su_name'])
                    ->setTitle('Doculoss - Room Builder')
                    ->setSubject('Doculoss - Room Builder')
                    ->setDescription('Doculoss - Room Builder')
                    ->setKeywords('Doculoss - Room Builder');
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('C1', 'PERSONAL PROPERTY INVENTORY FORM');
            $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('C3', $uData[0]['su_name']);
                $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('C4', $uData[0]['st_city'] . ', ' . $uData[0]['st_state']);
                $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValueExplicit('C5', strval($uData[0]['su_phn']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('C6', $uData[0]['su_uname']);
                $spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
                
                foreach ($rData as $rmDat) {
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('C' . $highestRow + 2, ucfirst($rmDat['rms_name']));
                    $spreadsheet->getActiveSheet()->getStyle('C' . $highestRow + 2)->getFont()->setBold(true);
                    $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('A' . $highestRow + 4, 'Item Number');
                    $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('B' . $highestRow + 4, 'Item Description');
                    $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('C' . $highestRow + 4, 'Serial Number');
                    $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('D' . $highestRow + 4, 'Date Bought');
                    $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('E' . $highestRow + 4, 'Cost Pre Tax');
                    $spreadsheet->getActiveSheet()->getStyle('A' . $highestRow + 4 . ':E' . $highestRow + 4)->getFont()->setBold(true);
                    $rmItmData = DbOperations::getObject()->fetchData('', [$_SESSION['UID'], $rmDat['rms_rm_num']], true, $rmItmRes);
                    $col = $highestRow + 4 + 1;
                    $cnt = 1;
                    foreach ($rmItmData as $itm) {
                        $row = 'A';
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue($row++ . $col, $cnt);
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue($row++ . $col, $itm['rm_item_name']);
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValueExplicit($row++ . $col, $itm['rm_sl_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValueExplicit($row++ . $col, date('d/m/Y', strtotime($itm['rm_date'])), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue($row++ . $col, $itm['rm_item_price']);
                        ++$col;
                        ++$cnt;
                    }
                }
            /*$sheetCount = 0;
            foreach ($rData as $rmDat) {
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($sheetCount)
                    ->setCellValue('C1', 'PERSONAL PROPERTY INVENTORY FORM');
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('C3', $uData[0]['su_name']);
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('C4', $uData[0]['st_city'] . ', ' . $uData[0]['st_state']);
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValueExplicit('C5', strval($uData[0]['su_phn']), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('C6', $uData[0]['su_uname']);
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('C8', ucfirst($rmDat['rms_name']));
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('A10', 'Item Number');
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('B10', 'Item Description');
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('C10', 'Serial Number');
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('D10', 'Date Bought');
                $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue('E10', 'Cost Pre Tax');
                $spreadsheet->getActiveSheet()->setTitle(ucfirst($rmDat['rms_name']), false, false);
                $spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('C8')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('A10:E10')->getFont()->setBold(true);
                $rmItmData = DbOperations::getObject()->fetchData('', [$_SESSION['UID'], $rmDat['rms_rm_num']], true, $rmItmRes);
                $col = 11;
                $cnt = 1;
                foreach ($rmItmData as $itm) {
                    $row = 'A';
                    $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue($row++ . $col, $cnt);
                    $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue($row++ . $col, $itm['rm_item_name']);
                    $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValueExplicit($row++ . $col, $itm['rm_sl_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValueExplicit($row++ . $col, date('d/m/Y', strtotime($itm['rm_date'])), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $spreadsheet->setActiveSheetIndex($sheetCount)
                        ->setCellValue($row++ . $col, $itm['rm_item_price']);
                    ++$col;
                    ++$cnt;
                }
                ++$sheetCount;
            }*/
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $spreadsheet->getActiveSheet()->getStyle('A1:' . $highestColumn . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . 'Doculoss-Room Builder' . date('H-i-s') . '".xlsx');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Last-Modified: ' . CURTIME); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        }
        
        // }}}
        // {{{ exportAsPdf()

        /**
         * Method to create a pdf file of user data
         * and download the same
         *
         * @access public
         * @static
         */
        public function exportAsPdf() 
        {
            $sql = 'select rms_rm_num, rms_name, rms_length, rms_width, rms_height, '
                    . 'rms_es_video, rms_wn_video from rooms where rms_uid = ?';
            $rData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            if (count($rData) > 0) {
                $sql = 'select su_name, su_compName, su_phn, su_uname, st_zip, st_city, st_state from signup '
                        . ' left join state_data on su_st_id = st_id where su_id = ?';
                $uData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
                $sql = 'select rm_item_name, rm_sl_no, rm_date, rm_item_price, rm_img_rcpt, '
                        . 'rm_img_pre_pic from roombuild WHERE rm_uid = ? and rm_room_num = ?';
                $rmItmRes = DbOperations::getObject()->prepareQuery($sql);

                $pdfData = '<div style="width: 100%"><table style="width: 100%;margin: auto;border: 1px solid;border-collapse:collapse">'
                        . '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse" colspan="7">'
                        . '<h2 style="text-align: center">PERSONAL PROPERTY INVENTORY FORM</h2></td></tr>'
                        . '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse" colspan="7">'
                        . '<h5 style="margin-left: 10px">' . $uData[0]['su_name'] . '</h5>'
                        . '<h5 style="margin-left: 10px">' . $uData[0]['st_city'] . ', ' . $uData[0]['st_state'] . '</h5>'
                        . '<h5 style="margin-left: 10px">' . $uData[0]['su_phn'] . '</h5>'
                        . '<h5 style="margin-left: 10px">' . $uData[0]['su_uname'] . '</h5></td></tr>';
                foreach ($rData as $rmDat) {
                    if (!empty($rmDat['rms_es_video'])) {
                        $esVid = '<a href="' . ACCESS_URL . 'helpers/images/uploads/' . $rmDat['rms_es_video'] . '">' . ACCESS_URL . 'helpers/images/uploads/' . $rmDat['rms_es_video'] . '</a>';
                    } else {
                        $esVid = 'Video not added!';
                    }
                    if (!empty($rmDat['rms_wn_video'])) {
                        $wnVid = '<a href="' . ACCESS_URL . 'helpers/images/uploads/' . $rmDat['rms_wn_video'] . '">' . ACCESS_URL . 'helpers/images/uploads/' . $rmDat['rms_wn_video'] . '</a>';
                    } else {
                        $wnVid = 'Video not added!';
                    }
                    $pdfData .= '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse" colspan="7">'
                            . '<h3 style="text-align: center">' . ucfirst($rmDat['rms_name']) . '</h3></td></tr>'
                            . '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse" colspan="7">'
                            . '<h3 style="text-align: center">Room Dimensions(In feet):</h3> '
                            . $rmDat['rms_length'] . '(L) X ' . $rmDat['rms_width'] . '(W) X ' 
                            . $rmDat['rms_height'] . '(H)</td></tr>'
                            . '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse" colspan="7">'
                            . '<h3 style="text-align: center">Room Videos: </h3>'
                            . '<p>East to South: ' . $esVid . '</p>'
                            . '<p>West to North: ' . $wnVid . '</p></td></tr>'
                            . '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Item Number</h4></td>'
                            . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Item Description</h4></td>'
                            . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Serial Number</h4></td>'
                            . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Date Bought</h4></td>'
                            . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Cost Pre Tax</h4></td>'
                            . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Receipt Pic</h4></td>'
                            . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                            . '<h4 style="text-align: center">Before Dis. Pic</h4></td></tr>';
                    $rmItmData = DbOperations::getObject()->fetchData('', [$_SESSION['UID'], $rmDat['rms_rm_num']], true, $rmItmRes);
                    $cnt = 1;
                    foreach ($rmItmData as $itm) {
                        $pdfData .= '<tr><td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<h6 style="text-align: center">' . $cnt . '</h6></td>'
                                . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<h6 style="text-align: center">' . $itm['rm_item_name'] . '</h6></td>'
                                . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<h6 style="text-align: center">' . $itm['rm_sl_no'] . '</h6></td>'
                                . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<h6 style="text-align: center">' . date('d/m/Y', strtotime($itm['rm_date'])) . '</h6></td>'
                                . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<h6 style="text-align: center">' . $itm['rm_item_price'] . '</h6></td>'
                                . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<img src="' . ACCESS_URL . 'helpers/images/uploads/' . $itm['rm_img_rcpt'] . '" class="img-fluid" width="150" height="100" alt=""></td>'
                                . '<td style="margin: auto;border: 1px solid;border-collapse:collapse">'
                                . '<img src="' . ACCESS_URL . 'helpers/images/uploads/' . $itm['rm_img_pre_pic'] . '" class="img-fluid" width="150" height="100" alt=""></td></tr>';
                        ++$cnt;
                    }
                }
                $pdfData .= '</table></div>';
                //print_r($pdfData);exit;
                require_once DIRPATH . DS . 'helpers' . DS . 'classes' . DS . 'mpdf' . DS . 'vendor' . DS . 'autoload.php';
                $mpdf = new \Mpdf\Mpdf();
                $mpdf->WriteHTML($pdfData);
                //$mpdf->Output(PGS_DIR . DS . $uData[0]['su_name'] . '.pdf', 'F');
                $mpdf->Output($uData[0]['su_name'] . '.pdf', 'D');
            } else {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Add a room to download data';
                header('Location:' . ACCESS_URL . 'user-home/');
                exit;
            }
        }
    }
}
