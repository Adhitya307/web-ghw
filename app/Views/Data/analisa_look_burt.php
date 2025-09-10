<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisa Look Burt - PT Indonesia Power</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/home.css') ?>">

    <style>
        .data-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .table-title {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
        }
        
        .scroll-table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        table {
            min-width: 100%;
            margin: 0;
        }

        thead th {
            position: sticky;
            top: 0;
            background: #5dade2;
            color: white;
            z-index: 10;
            text-align: center;
            vertical-align: middle;
            padding: 14px 10px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
        }
        
        tbody td {
            text-align: center;
            vertical-align: middle;
            padding: 12px 10px;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            font-size: 0.9rem;
        }
        
        tbody tr:hover {
            background-color: #eaf4fb !important;
        }
        
        .table-striped>tbody>tr:nth-child(odd) {
            background-color: #f8fbff;
        }
        
        .table-striped>tbody>tr:nth-child(even) {
            background-color: #ffffff;
        }

        /* Sticky first column */
        tbody td:first-child {
            position: sticky;
            left: 0;
            background: inherit;
            z-index: 1;
        }
        
        thead th:first-child {
            position: sticky;
            left: 0;
            z-index: 11;
        }
    </style>
</head>
<body>

    <?= $this->include('layouts/header') ?>

    <main class="container py-4">
        <div class="data-container">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-chart-line me-2"></i>Analisa Look Burt 2007
                </h2>
            </div>
            
            <div class="scroll-table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>TMA Waduk</th>
                            <th>Rembesan Bendungan (Ltr/mnt)</th>
                            <th>Panjang Bendungan (M)</th>
                            <th>Rembesan per M</th>
                            <th>Ambang OK</th>
                            <th>Ambang Not OK</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <?php foreach ($analisa as $row): ?>
                            <tr>
                                <td><?= $row['tanggal'] ?? '-'; ?></td>
                                <td><?= isset($row['tma_waduk']) ? number_format($row['tma_waduk'], 2) : '-'; ?></td>
                                <td><?= isset($row['rembesan_bendungan']) ? number_format($row['rembesan_bendungan'], 2) : '-'; ?></td>
                                <td><?= isset($row['panjang_bendungan']) ? number_format($row['panjang_bendungan'], 2) : '-'; ?></td>
                                <td><?= isset($row['rembesan_per_m']) ? number_format($row['rembesan_per_m'], 4) : '-'; ?></td>
                                <td><?= isset($row['nilai_ambang_ok']) ? number_format($row['nilai_ambang_ok'], 2) : '-'; ?></td>
                                <td><?= isset($row['nilai_ambang_notok']) ? number_format($row['nilai_ambang_notok'], 2) : '-'; ?></td>
                                <td><?= $row['keterangan'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?= $this->include('layouts/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
