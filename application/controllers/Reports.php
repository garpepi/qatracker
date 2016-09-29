<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Reports extends MY_Controller {
		public function __construct(){
			parent::__construct();	
			$this->load->model('projects_model');	
			$this->load->model('daily_reports_model');	
			
			//content used
			$this->load->model('environment_model');
			$this->load->model('teamleads_model');
			$this->load->model('progres_model');
			$this->load->model('phase_model');			
			$this->load->model('tester_on_projects_model');	
		}
		
		private function front_stuff(){
			$this->data = array(
							'title' => 'Daily Reports',
							'box_title_1' => 'Add Daily Report',
							'sub_box_title_1' => 'Adding new report',
							'box_title_2' => 'Projects List',
							'sub_box_title_2' => 'List of projects',
							'box_title_3' => 'Finished Projects List',
							'sub_box_title_3' => 'List of Finished projects',
							'box_title_4' => 'Droped Projects List',
							'sub_box_title_4' => 'List of Droped projects'
						);
			$this->page_css  = array(
							'vendors/iCheck/skins/flat/green.css',
							'vendors/datatables.net-bs/css/dataTables.bootstrap.min.css',
							'vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css',
							'vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css',
							'vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css',
							'vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css',
							'vendors/select2/dist/css/select2.min.css'

						);
			$this->page_js  = array(
							'vendors/iCheck/icheck.min.js',
							'vendors/datatables.net/js/jquery.dataTables.min.js',
							'vendors/datatables.net-bs/js/dataTables.bootstrap.min.js',
							'vendors/datatables.net-buttons/js/dataTables.buttons.min.js',
							'vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js',
							'vendors/datatables.net-buttons/js/buttons.flash.min.js',
							'vendors/datatables.net-buttons/js/buttons.html5.min.js',
							'vendors/datatables.net-buttons/js/buttons.print.min.js',
							'vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js',
							'vendors/datatables.net-keytable/js/dataTables.keyTable.min.js',
							'vendors/datatables.net-responsive/js/dataTables.responsive.min.js',
							'vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js',
							'vendors/datatables.net-scroller/js/dataTables.scroller.min.js',
							'vendors/jszip/dist/jszip.min.js',
							'vendors/pdfmake/build/pdfmake.min.js',
							'vendors/pdfmake/build/vfs_fonts.js',
							'vendors/select2/dist/js/select2.full.min.js',
							'vendors/moment/moment.min.js',
							'vendors/datepicker/daterangepicker.js',
							'vendors/jquery/jquery.cookie.js',
							'page/dailyreports/formreport.js'
						);
		}
		
		public function index(){
			$this->page_js  = array(
							'vendors/iCheck/icheck.min.js',
							'vendors/datatables.net/js/jquery.dataTables.min.js',
							'vendors/datatables.net-bs/js/dataTables.bootstrap.min.js',
							'vendors/datatables.net-buttons/js/dataTables.buttons.min.js',
							'vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js',
							'vendors/datatables.net-buttons/js/buttons.flash.min.js',
							'vendors/datatables.net-buttons/js/buttons.html5.min.js',
							'vendors/datatables.net-buttons/js/buttons.print.min.js',
							'vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js',
							'vendors/datatables.net-keytable/js/dataTables.keyTable.min.js',
							'vendors/datatables.net-responsive/js/dataTables.responsive.min.js',
							'vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js',
							'vendors/datatables.net-scroller/js/dataTables.scroller.min.js',
							'vendors/jszip/dist/jszip.min.js',
							'vendors/pdfmake/build/pdfmake.min.js',
							'vendors/pdfmake/build/vfs_fonts.js',
							'vendors/select2/dist/js/select2.full.min.js',
							'vendors/moment/moment.min.js',
							'vendors/datepicker/daterangepicker.js',
							'vendors/jquery/jquery.cookie.js',
							'page/dailyreports/reportlists.js'
						);
			$this->front_stuff();
			$this->contents = 'dailyreports/list/index'; // its your view name, change for as per requirement.
			
			// Table
			$this->data['contents'] = array(
							'daily_reports' => $this->daily_reports_model->get_reports(array('daily_reports.user_id' => $this->session->userdata('logged_in_data')['id']))
			);
			//$this->fancy_print($this->data['contents']);
			$this->layout();
		}
		
		public function generate(){
			$fetch = $this->daily_reports_model->get_reports(array(),'tester_name asc, daily_reports.created_date asc');
			$this->fancy_print($fetch);
			$data = array();
			$data[] = array(
						'Timestamp',
						'Project',
						'Tester Name',
						'Test Lead Name',
						'TRF Number',
						'Type of Changes',
						'Application',
						'Summary',
						'SIT / UAT Status',
						'Phase',
						'Remark',
						'Total Test Case Aplikasi',
						'Total Assigned TC per tester',
						'Test Case Executed',
						'Outstanding Test Case',
						'Plan Start Date',
						'Plan Completion Date',
						'Actual Start Date',
						'Actual Completion Date',
						'Downtime',
						'Plan Start Date - Doc',
						'Plan Completion Date - Doc',
						'Actual Start Date - Doc',
						'Actual Completion Date - Doc'
					);
			foreach($fetch as $key=> $value)
			{
				// set data
				$temp = array(); // initialize
				$application_list = '';// initialize
				foreach($value['project']['application_impact'] as $keys => $app_data){
					if(empty($application_list) || isset($app_data['project']['application_impact'][$keys+1]) ){
						$application_list .= $app_data['name'].',';
					}else{
						$application_list .= $app_data['name'];
					}
				}
				
				// Time Convert
				//
				$d = floor ($value['downtimes'] / 1440);
				$h = floor (($value['downtimes'] - $d * 1440) / 60);
				$m = $value['downtimes'] - ($d * 1440) - ($h * 60);
				
				$temp = array(
						$value['created_date'],
						$value['environment'],
						$value['tester_name'],
						$value['team_lead'],
						$value['project']['TRF'],
						$value['project']['TOC'],
						$application_list,
						$value['project']['sum_TRF'],
						$value['progress'],
						$value['phase'],
						$value['remarks'],
						$value['total_test_case'],
						$value['test_case_per_user'],
						$value['test_case_executed'],
						$value['test_case_outstanding'],
						$value['project']['plan_start_date'],
						$value['project']['plan_end_date'],
						$value['project']['actual_start_date'],
						$value['project']['actual_end_date'],
						$d.' Days, '.$h.' Hours, '.$m.' Minutes' ,
						$value['project']['plan_start_doc_date'],
						$value['project']['plan_end_doc_date'],
						$value['project']['actual_start_doc_date'],
						$value['project']['actual_end_doc_date']						
						);
				
				array_push($data,$temp);
			}
		//	$this->fancy_print($data);
			// setdata end
			// start Generate Excel
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Adidata QA Daily Report'); // naming sheet
			
			
			$filename='Adidata QA Daily Report.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			$this->excel->getActiveSheet()->fromArray( 
					$data,  // The data to set
					NULL,        // Array values with this value will not be set
					'A1'         // Top left coordinate of the worksheet range where
								 //    we want to set these values (default is A1)
				);
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1:X1')->getFont()->setBold(true);
			//Autosize
			for($col = 'A'; $col !== 'Y'; $col++) {
				$this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			}
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
		}
		
		public function _generate(){
			$this->load->library('excel');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('test worksheet');
			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', 'This is just some text value');
			//change the font size
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			//make the font become bold
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			//merge cell A1 until D1
			$this->excel->getActiveSheet()->mergeCells('A1:D1');
			//set aligment to center for that merged cell (A1 to D1)
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$filename='just_some_random_name.xls'; //save our workbook as this file name
			header('Content-Type: application/vnd.ms-excel'); //mime type
			header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
						
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
			//force user to download the Excel file without writing it to server's HD
			$objWriter->save('php://output');
		}

    }