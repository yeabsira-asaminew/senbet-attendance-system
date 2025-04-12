<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';
use Mpdf\Mpdf;

use Andegna\DateTime as EthiopianDateTime;
use Andegna\DateTimeFactory;
use Andegna\Constants;

class Report extends CI_Controller {
    public function __construct() {
        parent::__construct();

        if (!$this->session->userdata('uid')) {
            redirect('login');
        } elseif ($this->session->userdata('role') !== 'superadmin') {
            redirect('login');
        }

        
        $this->load->model('Report_model');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->dbutil();
    }

    public function index() {
        $this->load->view('admin/report_form');
    }

    public function generate_report() {
    
             // Get selected data from the form (ensure it's always an array)
             $selected_data = $this->input->post('data') ?? [];
        
             // Fetch data from the database based on selection
             $data = [
                 'selected_data' => $selected_data, // Pass selected data to the view
                 'sections' => $this->Report_model->get_sections_data(),
                 'departments' => $this->Report_model->get_departments_data(),
                 'age_groups' => $this->Report_model->get_age_groups_data(),
                 'students_by_sex' => $this->Report_model->get_students_by_sex(),
             ];
        
             
            $selected_years = $this->input->post('years');
            $reportData = [];
    
            foreach ($selected_years as $ethiopianYear) {
                // Convert Ethiopian year to Gregorian year range
                $startDate = DateTimeFactory::of((int)$ethiopianYear, 1, 1)->toGregorian();
                $endDate = DateTimeFactory::of((int)$ethiopianYear, 12, 30)->toGregorian();
                $startGregorianYear = $startDate->format('Y');
                $endGregorianYear = $endDate->format('Y');
    
                // Fetch report data by section
                $reportData[$ethiopianYear] = $this->Report_model->get_student_count_by_section($startGregorianYear, $endGregorianYear);
            }
    
            // Monthly report
            $month_selected_year = $this->input->post('monthly_year');
            $startDate = DateTimeFactory::of((int)$month_selected_year, 1, 1)->toGregorian();
            $endDate = DateTimeFactory::of((int)$month_selected_year, 12, 30)->toGregorian();
            $startGregorianYear = $startDate->format('Y');
            $endGregorianYear = $endDate->format('Y');
    
            // Fetch monthly report data
            $reportMonthlyData = $this->Report_model->get_monthly_count_by_year($startGregorianYear, $endGregorianYear);
    
            // Active and inactive report data
            $activeInactiveData = $this->Report_model->get_active_inactive_student_data();
    
            // Total students registration per year for chart
            $yearlyRegistration = $this->Report_model->get_yearly_registration_data();
    
            // Prepare data for the view
            $data['reportData'] = $reportData;
            $data['report_monthly_data'] = $reportMonthlyData;
            $data['monthly_year'] = $month_selected_year;
            $data['active_inactive_data'] = $activeInactiveData;
            $data['yearly_registration'] = $yearlyRegistration;
    
    
             // Get the current Ethiopian date and time
             $now = new DateTime('now', new DateTimeZone('Africa/Addis_Ababa'));
             $ethiopian_date = new Andegna\DateTime($now); //DateTimeFactory::of($now->format('Y'), $now->format('m'), $now->format('d'))->toEthiopian();
             $ethiopian_date_str = $ethiopian_date->format('F j, Y, g:i A');
    
            // Load the view
            $html = $this->load->view('admin/report_template', $data, true);
    
            try {
               $mpdf = new Mpdf();
         
                 // Set header and footer
                 $mpdf->SetHeader('ደብረ ታቦር ቅዱስ እግዚአብሄር አብ ጽርሐ ጽዮን ሰ/ት/ቤት');
                 $mpdf->SetFooter('ሪፖርቱ የተፈጠረው በሰንበት አቴንዳንስ ሲስተም(SAS) በ' . $ethiopian_date_str);
         
                 // Write the HTML content to the PDF
                 $mpdf->WriteHTML($html);
         
                 // Output the PDF
                 $mpdf->Output('student_report.pdf', 'D');
            } catch (Exception $e) {
                echo "PDF generation failed: " . $e->getMessage();
            }
        }
    

    public function backup_db() {
        $prefs = array(
            'format' => 'zip',             
            'filename' => 'senbet_db.sql'   
        );
        $backup = $this->dbutil->backup($prefs);

        $filename = 'senbet_db_backup-' . date('Y-m-d_H-i-s') . '.zip';

        force_download($filename, $backup);
    }
  
    
}
