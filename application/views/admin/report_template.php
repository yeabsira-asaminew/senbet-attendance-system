<!DOCTYPE html>
<html>

<head>
    <title>Student Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: 'Nyala', 'Abyssinica SIL', sans-serif;
        margin: 20px;
    }

    h2,
    h3 {
        color: #333;
    }

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 8px;
        text-align: center;
        font-size: 14px;
    }

    th {
        background-color: #f2f2f2;
    }

    table {
        margin-bottom: 20px;
        width: 50%;
    }

    .chart-container {
        width: 80%;
        margin: 20px auto;
    }

    /* General body styling */
    .report-body {
        font-family: 'Nyala', 'Abyssinica SIL', sans-serif;
        margin: 20px;
    }

    /* Table styling */
    .report-table {
        border-collapse: collapse;
        width: 70%;
        margin-bottom: 20px;
        border: 1px solid black;
    }

    .report-table th,
    .report-table td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    .report-table th {
        background-color: #f2f2f2;
        /* Light gray background for headers */
        font-weight: bold;
    }

    /* Total row styling */
    .report-total-row {
        background-color: #ADD8E6;
        /* Light blue background */
        font-weight: bold;
    }

    /* Header styling */
    .report-header {
        color: #333;
        /* Dark gray color */
    }

    .report-section-header {
        margin-top: 20px;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>



    <?php if (is_array($selected_data) && in_array('sections', $selected_data)): ?>
    <h2 class="report-section-header">የተማሪዎች ቁጥር በት/ክፍል</h2>
    <table class="report-table">
        <tr>
            <th>የትምህርት ክፍል </th>
            <th>ወንድ</th>
            <th>ሴት</th>
            <th>ጠቅላላ</th>
        </tr>
        <?php
            // Initialize totals
            $total_male = 0;
            $total_female = 0;
            $overall_total = 0;

            foreach ($sections as $section):
                // Add to totals
                $total_male += $section['male'];
                $total_female += $section['female'];
                $overall_total += $section['total'];
            ?>
        <tr>
            <td><?= $section['section_name'] ?></td>
            <td><?= $section['male'] ?></td>
            <td><?= $section['female'] ?></td>
            <td><?= $section['total'] ?></td>
        </tr>
        <?php endforeach; ?>

        <!-- Total Row -->
        <tr class="report-total-row">
            <td>ጠቅላላ</td>
            <td><?= $total_male ?></td>
            <td><?= $total_female ?></td>
            <td><?= $overall_total ?></td>
        </tr>
    </table>
    <?php endif; ?>




    <?php if (is_array($selected_data) && in_array('departments', $selected_data)): ?>
    <h2 class="report-section-header">የተማሪዎች ቁጥር በስራ ክፍል</h2>
    <table class="report-table">
        <tr>
            <th>የስራ ክፍል</th>
            <th>ወንድ</th>
            <th>ሴት</th>
            <th>ጠቅላላ</th>
        </tr>

        <?php
            // Initialize totals
            $total_male = 0;
            $total_female = 0;
            $overall_total = 0;

            foreach ($departments as $department): 
                // Add to totals
                $total_male += $department['male'];
                $total_female += $department['female'];
                $overall_total += $department['total'];
            ?>
        <tr>
            <td><?= $department['department_name'] ?></td>
            <td><?= $department['male'] ?></td>
            <td><?= $department['female'] ?></td>
            <td><?= $department['total'] ?></td>
        </tr>
        <?php endforeach; ?>

        <!-- Total Row -->
        <tr class="report-total-row">
            <td>ጠቅላላ</td>
            <td><?= $total_male ?></td>
            <td><?= $total_female ?></td>
            <td><?= $overall_total ?></td>
        </tr>

    </table>
    <?php endif; ?>



    <?php if (is_array($selected_data) && in_array('age_groups', $selected_data)): ?>
    <h2 class="report-section-header">የተማሪዎች ቁጥር በእድሜ
        ክፍል</h2>
    <table class="report-table">
        <tr>
            <th>ከ7 አመት በታች</th>
            <th>ከ7 እስከ 18 አመት</th>
            <th>ከ18 አመት በላይ</th>
        </tr>
        <tr>
            <td><?= $age_groups['under_7'] ?></td>
            <td><?= $age_groups['between_7_18'] ?></td>
            <td><?= $age_groups['above_18'] ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <?php if (is_array($selected_data) && in_array('students_by_sex', $selected_data)): ?>
    <h2 class="report-section-header"> የተማሪዎች ቁጥር
        በጾታ</h2>
    <table class="report-table">
        <tr>
            <th>ጾታ</th>
            <th>ጠቅላላ</th>
        </tr>
        <?php foreach ($students_by_sex as $student): ?>
        <tr>
            <td><?= $student['sex'] ?></td>
            <td><?= $student['total'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>








    <!-- Yearly Report -->
    <?php foreach ($reportData as $year => $sections) { ?>
    <h2 class="report-section-header">በአመቱ የተመዘገቡ ተማሪዎች ቁጥር - <?= $year ?> ዓ.ም</h2>
    <h3>Year: <?= $year ?> ዓ.ም</h3>
    <table>
        <tr>
            <th>የትምህርት ክፍል </th>
            <th>ወንድ</th>
            <th>ሴት</th>
            <th>ጠቅላላ</th>
        </tr>
        <?php 
                $totalMale = 0;
                $totalFemale = 0;
                $totalStudents = 0;

                foreach ($sections as $section) { 
                    $totalMale += $section['male_count'];
                    $totalFemale += $section['female_count'];
                    $totalStudents += $section['total_count'];
            ?>
        <tr>
            <td><?= $section['section'] ?></td>
            <td><?= $section['male_count'] ?></td>
            <td><?= $section['female_count'] ?></td>
            <td><?= $section['total_count'] ?></td>
        </tr>
        <?php } ?>
        <tr>
            <th>ጠቅላላ</th>
            <th><?= $totalMale ?></th>
            <th><?= $totalFemale ?></th>
            <th><?= $totalStudents ?></th>
        </tr>
    </table>
    <?php } ?>

    <!-- Active and Inactive Students Report -->
    <h2 class="report-section-header">በመማር ላይ ያሉ እና የሌሉ(ንቁ - active | ንቁ ያልሆኑ - inactive)</h2>
    <table>
        <tr>
            <th>ሁኔታ</th>
            <th>ወንድ</th>
            <th>ሴት</th>
            <th>ጠቅላላ</th>
        </tr>
        <?php
            $activeMale = $activeFemale = $inactiveMale = $inactiveFemale = 0;

            foreach ($active_inactive_data as $row) {
                if ($row['status'] == 1) {
                    $activeMale += $row['male_count'];
                    $activeFemale += $row['female_count'];
                } else {
                    $inactiveMale += $row['male_count'];
                    $inactiveFemale += $row['female_count'];
                }
            }

            $activeTotal = $activeMale + $activeFemale;
            $inactiveTotal = $inactiveMale + $inactiveFemale;
            $overallTotal = $activeTotal + $inactiveTotal;
        ?>
        <tr>
            <td>ንቁ</td>
            <td><?= $activeMale ?></td>
            <td><?= $activeFemale ?></td>
            <td><?= $activeTotal ?></td>
        </tr>
        <tr>
            <td>ያልነቁ</td>
            <td><?= $inactiveMale ?></td>
            <td><?= $inactiveFemale ?></td>
            <td><?= $inactiveTotal ?></td>
        </tr>
        <tr>
            <th>ጠቅላላ</th>
            <th><?= $activeMale + $inactiveMale ?></th>
            <th><?= $activeFemale + $inactiveFemale ?></th>
            <th><?= $overallTotal ?></th>
        </tr>
    </table>


    <!-- Monthly Report -->
    <?php if (isset($monthly_year) && isset($report_monthly_data)) { ?>
        <h2 class="report-section-header">በ<?= $monthly_year ?> ዓ.ም ውስጥ በሁሉም ወራት የተመዘገቡ ተማሪዎች ቁጥር</h2>
    <table>
        <thead>
            <tr>
                <th>ወር</th>
                <th>የተመዘገቡ ተማሪዎች ቁጥር</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                    $monthlyTotal = 0;
                    foreach ($report_monthly_data as $row) {
                        $monthlyTotal += $row['total'];
                ?>
            <tr>
                <td><?= $row['month'] ?></td>
                <td><?= $row['total'] ?></td>
            </tr>
            <?php } ?>
            <tr>
                <th>ጠቅላላ</th>
                <th><?= $monthlyTotal ?></th>
            </tr>
        </tbody>
    </table>
    <?php } ?>


</body>

</html>