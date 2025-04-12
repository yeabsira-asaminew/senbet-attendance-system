<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- favicon -->
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <title>ዳሽቦርድ | ጽርሐ ጽዮን ሰ/ት/ቤት</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .charts-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        justify-content: center;
        align-items: center;
    }

    canvas {
        max-width: 300px !important;
        max-height: 300px !important;
        margin: auto;
    }
    </style>

</head>

<body>

    <?php include APPPATH . 'views/admin/includes/sidebar.php'; ?>

    <!-- CONTENT -->
    <section id="content">
        <?php include APPPATH . 'views/admin/includes/topbar.php'; ?>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h2>እንኳን ወደ ደብረ ታቦር ቅዱስ እግዚአብሄር አብ ጽርሐ ጽዮን ሰ/ት/ቤት አቴንዳንስ ሲስተም በደህና መጡ</h2>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" href="<?php echo base_url('dashboard'); ?>">ዳሽቦርድ</a>
                        </li>

                    </ul>
                </div>
            </div>
            
            <div>
                <div class="charts-container"
                    style="background: white; border-radius: 10px; padding-top: 25px; margin: 10px auto; ">
                    <canvas id="sexChart"></canvas>
                    <canvas id="sectionChart"></canvas>
                    <canvas id="departmentChart"></canvas>
                    <canvas id="ageChart"></canvas>
                    <canvas id="attendanceChart"></canvas>
                    <canvas id="yearlyChart"></canvas>
                   
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const sexData = <?= json_encode($sex_data) ?>;
        const sectionData = <?= json_encode($section_data) ?>;
        const departmentData = <?= json_encode($department_data) ?>;
        const ageData = <?= json_encode($age_data) ?>;
        const attendanceData = <?= json_encode($attendance_data) ?>;
        const yearlyData = <?= json_encode($students_by_year) ?>;
       

        // Chart 1: Students by Sex (Doughnut)
        new Chart(document.getElementById('sexChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: sexData.map(item => item.sex_amharic),
                datasets: [{
                    data: sexData.map(item => Math.round(item.count)), // Ensure integers
                    backgroundColor: ['rgba(255, 99, 132)', 'rgba(54, 162, 235)']
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'የተማሪዎች ስርጭት - በጾታ',
                        font: { size: 16 }
                    }
                }
            }
        });

        // Chart 2: Students by Section (Bar)
        new Chart(document.getElementById('sectionChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: sectionData.map(item => item.section_name),
                datasets: [{
                    label: 'ተማሪዎች',
                    data: sectionData.map(item => Math.round(item.count)), // Ensure integers
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'የተማሪዎች ስርጭት - በት/ክፍል',
                        font: { size: 16 }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return Number.isInteger(value) ? value : ''; // Only display integers
                            }
                        }
                    }
                }
            }
        });

        // Chart 3: Students by Department (Pie)
        new Chart(document.getElementById('departmentChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: departmentData.map(item => item.department_name),
                datasets: [{
                    data: departmentData.map(item => Math.round(item.count)), // Ensure integers
                    backgroundColor: ['rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(75, 192, 192)']
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'የተማሪዎች ስርጭት - በስራ ክፍል',
                        font: { size: 16 }
                    }
                }
            }
        });


    // Chart 5: Students by Age Group (Line)
    new Chart(document.getElementById('ageChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['ከ7 አመት በታች', 'ከ7 እስከ 18 አመት', 'ከ18 አመት በላይ'],
            datasets: [{
                label: 'Students',
                data: [Math.round(ageData.under_7), Math.round(ageData.between_7_18), Math.round(ageData.above_18)], // Ensure integers
                backgroundColor: ['rgba(54, 162, 235)',  'rgba(255, 99, 132)', 'rgba(75, 192, 192)']
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'የተማሪዎች ስርጭት - በእድሜ',
                    font: { size: 16 }
                }
            }
        }
    });

    // Chart 6: Attendance Analysis (Bar)
   new Chart(document.getElementById('attendanceChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['አጠቃላይ', 'ያለፈው ወር', 'ያለፈው ሳምንት'],
        datasets: [
            {
                label: 'ተገኝቷል',
                data: [
                    Math.round(attendanceData.present),
                    Math.round(attendanceData.present_last_month),
                    Math.round(attendanceData.present_last_week)
                ], // Ensure integers
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
            },
            {
                label: 'አልተገኘም',
                data: [
                    Math.round(attendanceData.absent),
                    Math.round(attendanceData.absent_last_month),
                    Math.round(attendanceData.absent_last_week)
                ], // Ensure integers
                backgroundColor: 'rgba(255, 99, 132, 0.6)'
            }
        ]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'አቴንዳንስ ትንተና',
                font: { size: 16 }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: function(value) {
                        return Number.isInteger(value) ? value : ''; // Only display integers
                    }
                }
            }
        }
    }
});


    // Chart 7: Number of Students Over the Years (Line Chart)
    new Chart(document.getElementById('yearlyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: yearlyData.map(item => item.year ),
            datasets: [{
                label: 'ተማሪዎች',
                data: yearlyData.map(item => Math.round(item.count)), // Ensure integers
                borderColor: 'rgba(54, 162, 235, 0.8)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'የተመዘገቡ ተማሪዎች ቁጥር በየአመቱ',
                    font: { size: 16 }
                }
            },
            scales: {
                y: {
                    ticks: {
                        // Display integer values only
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        }
    });


   


   
});
    </script>

</body>

<script src="<?php echo base_url('assets/js/script.js'); ?>"></script>

</html>