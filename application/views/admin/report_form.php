<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- favicon -->
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <title>ሪፖርት እና ዳታቤዝ | ጽርሐ ጽዮን ሰ/ት/ቤት</title>

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/schedule.css'); ?>">
    <style>
    .title {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #004080;
        font-weight: bold;
        text-align: left;
    }

    .form-label {
        font-size: 1rem;
        margin: 0.5rem 0;
        color: #333;
    }

    .report-form {
        display: flex;
        flex-direction: column;
        align-items: left;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .schedule-select {
        width: 250px;
        padding: 5px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #fff;
    }

    .checkbox-group {
        margin: 10px 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .checkbox-group label {
        margin: 3px 0;
        font-size: 0.9rem;
        color: #444;
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
                    <h2>ሪፖርት እና ዳታቤዝ መፍጠሪያና </h2>

                    <ul class="breadcrumb">
                        <li><a href="<?php echo base_url('dashboard'); ?>">ዳሽቦርድ</a></li>
                        <li><i class="bx bx-chevron-right"></i></li>
                        <li><a class="active" href="<?php echo base_url('report'); ?>">ሪፖርት እና ዳታቤዝ</a></li>
                    </ul>
                </div>

                <?php if ($this->session->flashdata('report_message')): ?>
                <div id="flash-message"
                    class="message-box <?php echo $this->session->flashdata('report_message')['type']; ?>">
                    <?php echo $this->session->flashdata('report_message')['text']; ?>
                </div>
                <?php endif; ?>


            </div>

            <div class="unique-form-container">
                <div class="schedule-container">

                    <div class="table-container">
                        <h3 class="title">ሪፖርት መፍጠሪያ</h3>
                        <form action="<?= base_url('report/generate_report') ?>" method="post" class="report-form">

                            <label class="form-label">በአመቱ የተመዘገቡ ተማሪዎች ቁጥር (ከአንድ በላይ መምረጥ ይቻላል)</label>
                            <select name="years[]" multiple size="10" class="schedule-select">
                                <option value="">-- ምረጥ --</option>

                                <!-- Loop through the years -->
                                <?php for ($year = 2010; $year <= 2100; $year++) { ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                                <?php } ?>
                            </select>

                            <label class="form-label">በአመቱ ውስጥ በሁሉም ወራት የተመዘገቡ ተማሪዎች ቁጥር </label>
                            <select name="monthly_year" class="schedule-select">
                                <?php for ($year = 2010; $year <= 2100; $year++) { ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                                <?php } ?>
                            </select>

                            <div class="checkbox-group">
                                <label><input type="checkbox" name="data[]" value="sections"> የተማሪዎች ቁጥር በት/ክፍል</label>
                                <label><input type="checkbox" name="data[]" value="departments"> የተማሪዎች ቁጥር በስራ
                                    ክፍል</label>
                                <label><input type="checkbox" name="data[]" value="age_groups"> የተማሪዎች ቁጥር በእድሜ
                                    ክፍል</label>
                                <label><input type="checkbox" name="data[]" value="students_by_sex"> የተማሪዎች ቁጥር
                                    በጾታ</label>
                                <label><input type="checkbox" name="data[]" value="students_by_year"> የተማሪዎች ቁጥር
                                    በየአመቱ ግራፍ</label>
                                <br>
                                <label><input type="checkbox" id="select_all"> ሁሉንም ምረጥ</label>
                            </div>

                            <button type="submit" class="schedule-button">📄 ሪፖርት ፍጠር</button>
                        </form>

                    </div>


                    <div class="schedule-container">

                        <h3 class="title">ዳታቤዝ ምትክ አስቀምጥ </h3>
                        <a href="<?= site_url('report/backup_db') ?>" class="schedule-button">ዳታቤዝ አውርድ</a>
                    
                    </div>





                </div>
            </div>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="<?= base_url('assets/js/script.js'); ?>"></script>
    <script>
    document.getElementById('select_all').addEventListener('click', function() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name="data[]"]');
        var selectAllChecked = this.checked;

        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllChecked;
        });
    });
    </script>

</body>

</html>