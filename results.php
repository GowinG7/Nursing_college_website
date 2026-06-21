<?php
require_once __DIR__ . '/includes/db.php';
$page_title = "Examination Results";
include __DIR__ . '/includes/header.php';

$student = null;
$batch = null;
$marks = [];
$subs = [];
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $symbol = trim($_POST['symbol'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    if (!$symbol || !$dob) {
        $err = 'Enter symbol number and date of birth.';
    } else {
        $st = $conn->prepare("SELECT * FROM result_students WHERE symbol_no=? AND dob=? LIMIT 1");
        $st->bind_param('ss', $symbol, $dob);
        $st->execute();
        $student = $st->get_result()->fetch_assoc();
        if (!$student) {
            $err = 'No result found. Check symbol number and DOB.';
        } else {
            $batch = $conn->query("SELECT * FROM result_batches WHERE id=" . (int) $student['batch_id'])->fetch_assoc();
            $sq = $conn->query("SELECT * FROM result_subjects WHERE batch_id=" . (int) $student['batch_id'] . " ORDER BY sort_order");
            while ($r = $sq->fetch_assoc())
                $subs[$r['id']] = $r;
            $mq = $conn->query("SELECT * FROM result_marks WHERE student_id=" . (int) $student['id']);
            while ($r = $mq->fetch_assoc())
                $marks[$r['subject_id']] = $r;
        }
    }
}
?>
<style>
    .result-hero {
        background: linear-gradient(135deg, #e9f7ef, #fff);
        padding: 3rem 0
    }

    .search-card {
        max-width: 560px;
        margin: auto;
        background: #fff;
        border-radius: 14px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .06)
    }

    .marksheet {
        max-width: 850px;
        margin: 2rem auto;
        background: #fff;
        border: 2px solid #198754;
        padding: 2rem;
        border-radius: 6px
    }

    .marksheet h2 {
        color: #0f5132;
        margin: 0
    }

    .ms-head {
        text-align: center;
        border-bottom: 2px solid #198754;
        padding-bottom: 1rem;
        margin-bottom: 1.25rem
    }

    .ms-info td {
        padding: .3rem .5rem
    }

    .ms-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem
    }

    .ms-table th,
    .ms-table td {
        border: 1px solid #444;
        padding: .5rem;
        text-align: center;
        font-size: .92rem
    }

    .ms-table th {
        background: #e9f7ef
    }

    .ms-foot {
        display: flex;
        justify-content: space-between;
        margin-top: 3rem
    }

    .sign {
        text-align: center;
        border-top: 1px solid #333;
        padding-top: .25rem;
        min-width: 160px
    }

    .badge-pass {
        background: #198754;
        color: #fff;
        padding: .35rem .8rem;
        border-radius: 20px
    }

    .badge-fail {
        background: #dc3545;
        color: #fff;
        padding: .35rem .8rem;
        border-radius: 20px
    }

    @media print {
        .no-print {
            display: none !important
        }

        body {
            background: #fff
        }

        .marksheet {
            box-shadow: none;
            border: 2px solid #000;
            margin: 0
        }
    }
</style>

<section class="result-hero no-print">
    <div class="container text-center">
        <h1 style="color:#0f5132">Examination Results</h1>
        <p class="text-muted">Enter your symbol number and date of birth to view your marksheet.</p>
    </div>
    <div class="container">
        <div class="search-card">
            <?php if ($err): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3"><label class="form-label fw-bold">Symbol Number</label>
                    <input name="symbol" class="form-control form-control-lg" placeholder="e.g. BPK2025001" required
                        value="<?= htmlspecialchars($_POST['symbol'] ?? '') ?>">
                </div>
                <div class="mb-3"><label class="form-label fw-bold">Date of Birth</label>
                    <input type="date" name="dob" class="form-control form-control-lg" required
                        value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                </div>
                <button class="btn btn-success btn-lg w-100"><i class="bi bi-search"></i> View Result</button>
            </form>
        </div>
    </div>
</section>

<?php if ($student): ?>
    <div class="marksheet">
        <div class="ms-head">
            <img src="assets/images/logo.png" style="height:60px" alt="logo">
            <h2>BPKMCH Nursing College</h2>
            <div>Cancer Gate, Bharatpur, Chitwan, Nepal</div>
            <h4 class="mt-2 mb-0">Statement of Marks —
                <?= htmlspecialchars($batch['exam_name'] . ' Examination ' . $batch['exam_year']) ?></h4>
        </div>

        <table class="ms-info" style="width:100%">
            <tr>
                <td><strong>Student Name:</strong> <?= htmlspecialchars($student['student_name']) ?></td>
                <td><strong>Symbol No:</strong> <?= htmlspecialchars($student['symbol_no']) ?></td>
            </tr>
            <tr>
                <td><strong>Roll No:</strong> <?= htmlspecialchars($student['roll_no']) ?></td>
                <td><strong>DOB:</strong> <?= htmlspecialchars($student['dob']) ?></td>
            </tr>
            <tr>
                <td><strong>Program:</strong> <?= htmlspecialchars($batch['program']) ?></td>
                <td><strong>Level:</strong> <?= htmlspecialchars($batch['semester']) ?></td>
            </tr>
            <tr>
                <td><strong>Father's Name:</strong> <?= htmlspecialchars($student['father_name']) ?></td>
                <td><strong>Mother's Name:</strong> <?= htmlspecialchars($student['mother_name']) ?></td>
            </tr>
        </table>

        <table class="ms-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Credit</th>
                    <th>Full</th>
                    <th>Pass</th>
                    <th>Theory</th>
                    <th>Practical</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>GPA</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($subs as $sid => $s):
                    $m = $marks[$sid] ?? null; ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td style="text-align:left">
                            <?= htmlspecialchars(($s['subject_code'] ? $s['subject_code'] . ' — ' : '') . $s['subject_name']) ?></td>
                        <td><?= $s['credit'] ?></td>
                        <td><?= $s['full_marks'] ?></td>
                        <td><?= $s['pass_marks'] ?></td>
                        <td><?= $m ? $m['theory'] : '-' ?></td>
                        <td><?= $m ? $m['practical'] : '-' ?></td>
                        <td><strong><?= $m ? $m['total'] : '-' ?></strong></td>
                        <td><?= $m ? $m['grade'] : '-' ?></td>
                        <td><?= $m ? $m['gpa'] : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#f6f9f7;font-weight:700">
                    <td colspan="3">TOTAL</td>
                    <td colspan="4"><?= $student['total_full'] ?> / Obtained: <?= $student['total_obtained'] ?></td>
                    <td><?= number_format($student['percentage'], 2) ?>%</td>
                    <td><?= $student['grade'] ?></td>
                    <td><?= $student['gpa'] ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-3">
            <strong>Result:</strong>
            <span class="<?= $student['status'] === 'Pass' ? 'badge-pass' : 'badge-fail' ?>"><?= $student['status'] ?></span>
        </div>

        <div class="ms-foot">
            <div class="sign">Prepared By</div>
            <div class="sign">Controller of Examinations</div>
            <div class="sign">Principal</div>
        </div>

        <div class="text-center mt-4 no-print">
            <button class="btn btn-success" onclick="window.print()"><i class="bi bi-printer"></i> Print Marksheet</button>
            <a href="results.php" class="btn btn-outline-secondary">Search Another</a>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>