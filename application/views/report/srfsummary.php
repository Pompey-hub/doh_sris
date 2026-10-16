<?php
$this->load->view('page/headlink');

// Calculate the total across every service category.
$calculateTotal = function ($row) {
    $categories = [
        'cnt_HW',
        'cnt_SW',
        'cnt_HIS',
        'cnt_IIS',
        'cnt_VC',
        'cnt_OC',
        'cnt_WIFI',
        'cnt_GOVMAIL',
        'cnt_unk'
    ];

    $total = 0;

    foreach ($categories as $category) {
        $total += (
            isset($row[$category]) &&
            is_numeric($row[$category])
        ) ? (int) $row[$category] : 0;
    }

    return $total;
};
?>

<style type="text/css">
    .table thead th,
    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }

    .table thead th {
        white-space: normal;
    }
</style>

<body>
    <?php $this->load->view('page/header'); ?>

    <div id="wrapper" style="padding-left: 5px; padding-right: 5px;">
        <div class="bg-white px-2">

            <div class="row pt-2 pb-4">
                <div class="col-6 text-left" style="font-size: 1.2em;">
                    Generate Report: Consolidated Service Request Summary
                    Report per Actions Taken
                </div>

                <div class="col-6 text-right">
                    <?php
                    $mm = [
                        'January',
                        'February',
                        'March',
                        'April',
                        'May',
                        'June',
                        'July',
                        'August',
                        'September',
                        'October',
                        'November',
                        'December'
                    ];
                    ?>

                    <form class="form-inline" style="float: right;">

                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="mm">
                                <option value="" disabled>
                                    Select month..
                                </option>

                                <?php foreach ($mm as $month): ?>
                                    <option
                                        value="<?php echo $month; ?>"
                                        <?php echo ($mm_g === $month) ? 'selected' : ''; ?>>
                                        <?php echo $month; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="yy">
                                <option value="" disabled>
                                    Select year..
                                </option>

                                <?php
                                $currentYear = (int) date('Y');

                                for ($i = $currentYear; $i > 2018; $i--):
                                ?>
                                    <option
                                        value="<?php echo $i; ?>"
                                        <?php echo ((string) $yy_g === (string) $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="off">
                                <option value="" disabled>
                                    Select officer..
                                </option>

                                <option
                                    value="<?php echo $this->cryptography->encrypt(-1); ?>"
                                    <?php echo ($off_g === '-1') ? 'selected' : ''; ?>>
                                    All
                                </option>

                                <?php foreach ($actoff as $officer): ?>
                                    <option
                                        value="<?php echo $this->cryptography->encrypt($officer['lid']); ?>"
                                        <?php echo ($off_g === $officer['lid']) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($officer['actoff']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-success mx-1 mb-2">
                            Generate
                        </button>

                        <a
                            href="javascript:void(0);"
                            class="btn btn-primary mx-1 mb-2"
                            id="myprint">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </form>
                </div>
            </div>

            <div class="container-fluid">
                <div class="table-responsive">

                    <table
                        class="table table-striped table-bordered"
                        style="text-align: center;">
                        <thead>
                            <tr>
                                <th rowspan="2">Actions Taken</th>

                                <th colspan="2">
                                    Number of Requests for ITE Support/Others
                                </th>
                                <th colspan="2">
                                    Number of Requests for Technical Assistance
                                </th>
                                <th rowspan="2">
                                    Number of Requests for Video Conference
                                </th>
                                <th rowspan="2">WIFI REGISTRATION</th>
                                <th rowspan="2">GOV MAIL</th>
                                <th rowspan="2">Other Concerns</th>
                                <th rowspan="2">TOTAL</th>
                            </tr>

                            <tr>
                                <th>Hardware</th>
                                <th>Software</th>
                                <th>DOH HIS</th>
                                <th>DOH EV-CHD IN-HOUSE INFORMATION SYSTEM</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $rows = [
                                'Received' => $rcvd[0] ?? [],
                                'For Action' => $actn[0] ?? [],
                                'Accomplished' => $accomp[0] ?? []
                            ];

                            foreach ($rows as $label => $row):
                            ?>
                                <tr>
                                    <td><?php echo $label; ?></td>

                                    <td>
                                        <?php echo (int) ($row['cnt_HW'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_SW'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_HIS'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_IIS'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_VC'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_WIFI'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_GOVMAIL'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['cnt_OC'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php echo $calculateTotal($row); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    <?php $this->load->view('page/footer'); ?>
</body>

<script
    type="text/javascript"
    src="<?php echo base_url('assets/js/srfsummary.js'); ?>"></script>