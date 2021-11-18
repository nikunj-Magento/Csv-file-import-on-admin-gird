<?php

namespace Barcode\Management\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;

class ImportCsvFile extends \Magento\Framework\App\Action\Action {

    private $resultPageFactory;
    private $_dataExample;
    protected $_resource;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\App\ResourceConnection $resource, \Barcode\Management\Model\GridFactory $gridExample, \Barcode\Management\Model\GridDataFactory $gridData
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_gridFactory = $gridExample;
        $this->_gridDataFactory = $gridData;
        $this->_resource = $resource;
    }

    public function execute() {
        $data = $this->getRequest()->getFiles();
        if ($data) {
            try {
                $csv_data = array();
                $error_barcode = array();
                $error_power = array();
                $error_color = array();
                $error_sku = array();
                $arr = array();
                $data_array = array();
                $chk_barcode = '';
                $chk_power = '';
                $chk_color = '';
                $chk_sku = '';
                $saveData = "";
                $rows = array();

                $model = $this->_gridFactory->create();
                $querymodel = $this->_gridDataFactory->create();
                $num_data = count($data);
                $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
                if ($num_data > 0) {
                    if (strlen($data['import_csv']['name']) != "") {
                        if (in_array($data['import_csv']['type'], $csvMimes)) {
                            if (is_uploaded_file($data['import_csv']['tmp_name'])) {
                                $csvFile = fopen($data['import_csv']['tmp_name'], 'r');
                                fgetcsv($csvFile);
                                while (($line = fgetcsv($csvFile)) !== FALSE) {

                                    $rows = array(
                                        'barcode' => $line[0],
                                        'power' => $line[1],
                                        'color' => $line[2],
                                        'sku' => $line[3]
                                    );
                                    $csv_data[] = $rows;
                                    $arr[] = $rows['barcode'];
                                }

                                if (count($arr) == count(array_unique($arr))) {
                                    foreach ($csv_data as $csv) {
                                        if (count(array_filter($csv)) == count($csv)) {

                                            if (!preg_match("/^([a-zA-Z0-9]+)$/", $csv['barcode'])) {
                                                $csv_error_barcode = array('barcode_err' => 'Invalid data in barcode');
                                                $error_barcode[] = $csv_error_barcode;
                                                $message = 'Invalid data in barcode';
                                                $data_array['success'] = 0;
                                                $data_array['message'] = $message;
                                            } else {
                                                $chk_barcode = 1;
                                            }

                                            if (!preg_match("/^[+-]?([0-9]*[.])?[0-9]+$/", $csv['power'])) {
                                                $csv_error_power = array('power_err' => 'Invalid data in power');
                                                $error_power[] = $csv_error_power;
                                                $message = 'Invalid data in power';
                                                $data_array['success'] = 0;
                                                $data_array['message'] = $message;
                                            } else {
                                                $chk_power = 1;
                                            }

                                            if (!preg_match("/^([a-zA-Z]+)$/", $csv['color'])) {
                                                $csv_error_color = array('color_err' => 'Invalid data in color');
                                                $error_color[] = $csv_error_color;
                                                $message = 'Invalid data in color';
                                                $data_array['success'] = 0;
                                                $data_array['message'] = $message;
                                            } else {
                                                $chk_color = 1;
                                            }

                                            if (!preg_match("/^([a-zA-Z0-9_! \"#$%&'()*+,\-.\\:\/;=?@^_]+)$/", $csv['sku'])) {
                                                $csv_error_sku = array('sku_err' => 'Invalid data in sku');
                                                $error_sku[] = $csv_error_sku;
                                                $message = 'Invalid data in sku';
                                                $data_array['success'] = 0;
                                                $data_array['message'] = $message;
                                            } else {
                                                $chk_sku = 1;
                                            }
                                            if ($chk_barcode == 1 && $chk_color == 1 && $chk_power == 1 && $chk_sku == 1) {
                                                $barcode_select = $csv['barcode'];
                                                $total_rows = $querymodel->getBarcodeselect($barcode_select);
                                                if ($total_rows == 1) {
                                                    $barcode = $csv['barcode'];
                                                    $where = ['barcode = ?' => (int) $barcode];
                                                    $saveData = $querymodel->Updatebarcodedata($csv, $where);
                                                } else {
                                                    $model->setData($csv);
                                                    $saveData = $model->save();
                                                }
                                            } else {
                                                $message = 'Sorry, Your Data Can\'t Save';
                                                $data_array['success'] = 0;
                                                $data_array['message'] = $message;
                                            }
                                        } else {
                                            $message = 'Something Wrong Data Can\'t Save';
                                            $data_array['success'] = 0;
                                            $data_array['message'] = $message;
                                        }
                                    }
                                    fclose($csvFile);
                                    if (isset($saveData)) {
                                        $message = 'Barcode Data Uploaded Succesfully !';
                                        $data_array['success'] = 1;
                                        $data_array['message'] = $message;
                                    } else {
                                        $message = 'Something Wrong Data Can\'t Save';
                                        $data_array['success'] = 0;
                                        $data_array['message'] = $message;
                                    }
                                } else {
                                    $message = 'Duplicate data in your csv check and try again later';
                                    $data_array['success'] = 0;
                                    $data_array['message'] = $message;
                                }
                            } else {
                                $message = 'Barcode Data Uploading failed';
                                $data_array['success'] = 0;
                                $data_array['message'] = $message;
                            }
                        } else {
                            $message = 'Only CSV Excel file allowed';
                            $data_array['success'] = 0;
                            $data_array['message'] = $message;
                        }
                    } else {
                        $message = 'Please Select File';
                        $data_array['success'] = 0;
                        $data_array['message'] = $message;
                    }
                } else {
                    $message = 'Pease check your CSV File 0 Data Found';
                    $data_array['success'] = 0;
                    $data_array['message'] = $message;
                }
            } catch (\Exception $e) {
                $data_array['success'] = 0;
                $data_array['message'] = $e->getMessage();
            }
        }
        echo json_encode($data_array);
        exit;
    }

}
