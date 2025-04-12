<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- favicon -->
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <title>የተማሪዎች አቴንዳንስ ዝርዝር | ጽርሐ ጽዮን ሰ/ት/ቤት</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icon.css'); ?>">
    <link rel="stylesheet" type="text/css"
        href="<?php echo base_url('assets/icon/simple-line-icons/css/simple-line-icons.css') ?>">

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
                    <h2>የተማሪዎች አቴንዳንስ ዝርዝር ገጽ</h2>

                    <ul class="breadcrumb">
                        <li> <a href="<?php echo base_url('admin/dashboard'); ?>">ዳሽቦርድ</a> </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li> <a class="active" href="<?php echo base_url('attendance/list'); ?>">የተማሪዎች አቴንዳንስ ዝርዝር</a>
                        </li>
                    </ul>
                </div>

                <!-- error and success message-->
                <?php if ($this->session->flashdata('auth_message')): ?>
                    <div id="flash-message"
                        class="message-box <?php echo $this->session->flashdata('auth_message')['type']; ?>">
                        <?php echo $this->session->flashdata('auth_message')['text']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <div class="order">


                    <form class="list-search-form" action="<?= base_url('attendance/list') ?>" method="get">
                        <label for="date">ቀን አስገባ</label>
                        <input type="text" name="date" id="date" value="<?php echo date('d/m/Y', strtotime($selected_date)); ?>">
                        <button type="submit">ፈልግ</button>
                    </form>

                    <table class="table-container-table">
                        <thead>
                            <tr>
                                <th>መታወቂያ ቁ.</th>
                                <th>ሙሉ ስም</th>
                                <th>የትምህርት ክፍል</th>
                                <th>አቴንዳንስ የተመዘገበበት ቀን</th>
                                <th>ሰአት</th>
                                <th>ሁኔታ</th>
                                <th>መተግበሪያ</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($attendances)): ?>
                                <?php foreach ($attendances as $attendance): ?>
                                    <tr>
                                        <td><?= $attendance['id'] ?></td>
                                        <td><?= $attendance['fname']  . ' ' . $attendance['mname'] . ' ' . $attendance['lname'] ?>
                                        </td>
                                        <td><?= $attendance['section_name'] ?></td>
                                        <td><?= $attendance['ethiopian_date'] ?></td>
                                        <td><?= $attendance['ethiopian_time'] ?></td>
                                        <td><?= $attendance['status_text'] ?></td>
                                        <td>

                                            <a href="<?= base_url('attendance/delete_attendance/' . $attendance['id']) ?>"
                                                class="btn btn-danger confirm-delete"
                                                onclick="return confirm('እርግጠኛ ነዎት ይህን አስተዳዳሪ መሰረዝ ይፈልጋሉ?');">
                                                <i class='bx bx-trash'> ሰርዝ</i> <!-- Updated icon for delete -->
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="100%" style="text-align: center; ">
                                        <h2>ምንም አቴንዳንስ አልተገኘም</h2>
                                    </td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatEthiopianDateInput(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value;

                // Remove non-numeric characters
                value = value.replace(/\D/g, '');

                // Add forward slashes after day and month
                if (value.length > 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }
                if (value.length > 5) {
                    value = value.slice(0, 5) + '/' + value.slice(5, 9); // Year is 4 digits
                }

                // Validate day, month, and year
                if (value.length >= 2) {
                    const day = parseInt(value.slice(0, 2), 10);
                    if (day > 30 || day === 0) {
                        // Replace invalid day with '01'
                        value = '01' + value.slice(2);
                    }
                }

                if (value.length >= 5) {
                    const month = parseInt(value.slice(3, 5), 10);
                    if (month > 13 || month === 0) {
                        // Replace invalid month with '01'
                        value = value.slice(0, 3) + '01' + value.slice(5);
                    }
                }

                if (value.length >= 10) {
                    const year = parseInt(value.slice(6, 10), 10);
                    if (year === 0) {
                        // Replace invalid year with '1990'
                        value = value.slice(0, 6) + '1990';
                    }
                }

                // Update the input value
                e.target.value = value;
            });

            input.addEventListener('keydown', function(e) {
                // Allow backspace, delete, and arrow keys
                if (e.key === 'Backspace' || e.key === 'Delete' || e.key.startsWith('Arrow')) {
                    return;
                }

                // Block non-numeric characters
                if (/\D/.test(e.key)) {
                    e.preventDefault();
                }

                // Auto-jump to the next field
                const value = e.target.value;
                if (value.length === 2 || value.length === 5) {
                    e.target.value += '/';
                }
            });
        }

        // Apply the formatting to the date input
        const dateInput = document.getElementById('date');
        if (dateInput) {
            formatEthiopianDateInput(dateInput);
        }
    });
</script>
<script src="<?php echo base_url('assets/js/script.js'); ?>"></script>

</html>