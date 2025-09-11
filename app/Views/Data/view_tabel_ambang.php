<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tabel Ambang Batas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .scroll-table-container {
            max-height: 500px;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            border: 1px solid #dee2e6;
        }
        
        table {
            min-width: 1000px;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        thead th {
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, var(--accent-color) 0%, #e67e22 100%);
            color: white;
            text-align: center;
            padding: 12px 15px;
            z-index: 10;
            font-weight: 600;
            border: none;
            cursor: pointer;
            user-select: none;
        }
        
        thead th:hover {
            background: linear-gradient(135deg, #e67e22 0%, var(--accent-color) 100%);
        }
        
        th i.sort-icon {
            margin-left: 5px;
            opacity: 0.6;
            font-size: 0.8em;
        }
        
        tbody tr {
            transition: background-color 0.2s;
        }
        
        tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Zebra striping for rows */
        tbody tr:nth-child(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        tbody tr:nth-child(odd):hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        /* Subtle animation for table rows */
        tbody tr {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0.8; }
            to { opacity: 1; }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(52, 152, 219, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .icon-box i {
            color: var(--secondary-color);
            font-size: 1.5rem;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .table-actions .btn {
            border-radius: 6px;
            padding: 8px 15px;
            font-weight: 500;
        }
        
        .page-item.active .page-link {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .page-link {
            color: var(--secondary-color);
        }
        
        .page-link:hover {
            color: var(--primary-color);
        }
        
        .sub-header {
            background: linear-gradient(135deg, #8e44ad 0%, #6c3483 100%);
            color: white;
            font-weight: 600;
        }
        
        .sub-header:hover {
            background: linear-gradient(135deg, #8e44ad 0%, #6c3483 100%) !important;
        }
    </style>
</head>
<body>
<?= $this->include('layouts/header') ?>
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-table me-3"></i> Tabel Ambang Batas</h1>
                    <p class="lead">Data lengkap mengenai nilai ambang batas pengukuran</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="icon-box">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="container py-4">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-table me-2"></i> Data Ambang Batas</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-actions p-3 border-bottom">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari data..." id="searchInput">
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" id="refreshButton"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
                    </div>
                </div>
                <div class="scroll-table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <?php foreach ($data[0] as $header): ?>
                                    <th><?= esc($header) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php for ($i = 1; $i < count($data); $i++): ?>
                                <?php 
                                // Check if this row is a sub-header (contains "AMBANG BATAS SEEPAGE")
                                $isSubHeader = false;
                                foreach ($data[$i] as $cell) {
                                    if (strpos($cell, 'AMBANG BATAS SEEPAGE') !== false) {
                                        $isSubHeader = true;
                                        break;
                                    }
                                }
                                ?>
                                <tr class="<?= $isSubHeader ? 'sub-header' : '' ?>">
                                    <?php foreach ($data[$i] as $cell): ?>
                                        <td><?= esc($cell) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="row">
                    <div class="col-md-6">
                        Menampilkan <span id="currentItems"><?= count($data) - 1 ?></span> dari <span id="totalItems"><?= count($data) - 1 ?></span> entri data
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0" id="pagination">
                                <li class="page-item disabled" id="prevButton">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#" data-page="1">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#" data-page="2">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#" data-page="3">3</a>
                                </li>
                                <li class="page-item" id="nextButton">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

     <?= $this->include('layouts/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#tableBody tr');
            const refreshButton = document.getElementById('refreshButton');
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');
            const pageLinks = document.querySelectorAll('.page-link[data-page]');
            const currentItemsSpan = document.getElementById('currentItems');
            const totalItemsSpan = document.getElementById('totalItems');
            
            let currentPage = 1;
            const rowsPerPage = 10;
            let filteredRows = Array.from(tableRows);
            
            // Initialize pagination
            updatePagination();
            
            // Search functionality
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                
                filteredRows = Array.from(tableRows).filter(row => {
                    const rowText = row.textContent.toLowerCase();
                    const isVisible = rowText.includes(searchText);
                    row.style.display = isVisible ? '' : 'none';
                    return isVisible;
                });
                
                currentPage = 1;
                updatePagination();
                updateTableDisplay();
            });
            
            // Refresh button
            refreshButton.addEventListener('click', function() {
                searchInput.value = '';
                filteredRows = Array.from(tableRows);
                filteredRows.forEach(row => row.style.display = '');
                currentPage = 1;
                updatePagination();
                updateTableDisplay();
            });
            
            // Previous button
            prevButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                    updateTableDisplay();
                }
            });
            
            // Next button
            nextButton.addEventListener('click', function(e) {
                e.preventDefault();
                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                    updateTableDisplay();
                }
            });
            
            // Page number buttons
            pageLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentPage = parseInt(this.getAttribute('data-page'));
                    updatePagination();
                    updateTableDisplay();
                });
            });
            
            function updatePagination() {
                const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
                
                // Update previous button
                if (currentPage === 1) {
                    prevButton.classList.add('disabled');
                } else {
                    prevButton.classList.remove('disabled');
                }
                
                // Update next button
                if (currentPage === totalPages || totalPages === 0) {
                    nextButton.classList.add('disabled');
                } else {
                    nextButton.classList.remove('disabled');
                }
                
                // Update page number buttons
                pageLinks.forEach(link => {
                    const pageNum = parseInt(link.getAttribute('data-page'));
                    const listItem = link.parentElement;
                    
                    if (pageNum === currentPage) {
                        listItem.classList.add('active');
                    } else {
                        listItem.classList.remove('active');
                    }
                    
                    // Show/hide page buttons based on total pages
                    if (pageNum > totalPages) {
                        listItem.style.display = 'none';
                    } else {
                        listItem.style.display = 'list-item';
                    }
                });
                
                // Update item count display
                const startItem = (currentPage - 1) * rowsPerPage + 1;
                const endItem = Math.min(currentPage * rowsPerPage, filteredRows.length);
                
                currentItemsSpan.textContent = filteredRows.length > 0 ? `${startItem}-${endItem}` : '0';
                totalItemsSpan.textContent = filteredRows.length;
            }
            
            function updateTableDisplay() {
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                
                filteredRows.forEach((row, index) => {
                    if (index >= startIndex && index < endIndex) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>