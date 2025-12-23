<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Upload & Selective Preview</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .dropzone { border: 2px dashed #0087F7; border-radius: 5px; background: #fdfdfd; }
        .table-wrapper { max-height: 500px; overflow-y: auto; margin-top: 20px; border: 1px solid #dee2e6; }
        #preview-section { display: none; margin-top: 30px; }
        .sticky-thead th { position: sticky; top: 0; background-color: #343a40; color: white; z-index: 1; }
    </style>
</head>
<body>

<div class="container">
    <div class="text-center mb-4">
        <h2>Pedimentos Import Tool</h2>
        <p class="text-muted">Upload your CSV/Excel file, preview the rows, and select which ones to save to the database.</p>
    </div>

    <form action="{{ route('upload') }}" class="dropzone" id="pedimentoDropzone">
        @csrf
        <div class="dz-message">
            <h4>Drop file here or click to upload</h4>
            <span>(Accepted formats: .csv, .xlsx, .xls)</span>
        </div>
    </form>

    <div id="preview-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Data Preview</h4>
            <div>
                <button id="confirm-save" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle"></i> Save Selected Rows
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table table-hover table-sm" id="preview-table">
                <thead class="sticky-thead">
                    <tr>
                        <th width="40"><input type="checkbox" id="select-all" checked></th>
                        <th>Proveedor</th>
                        <th>Planta</th>
                        <th>No. Pedimento</th>
                        <th>Division</th>
                        <th>Periodo</th>
                        <th>Avance</th>
                        <th>Estatus</th>
                        <th>Responsable</th>
                        <th>Inicio Proceso</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script>
    // Global variable to hold the full data from the file
    let fileRows = [];

    // 1. Initialize Dropzone
    Dropzone.options.pedimentoDropzone = {
        paramName: "file",
        maxFiles: 1,
        acceptedFiles: ".csv,.xlsx,.xls",
        dictDefaultMessage: "Upload Excel/CSV",
        init: function() {
            this.on("success", function(file, response) {
                // response.path comes from your Controller upload method
                loadPreview(response.path);
            });
            this.on("error", function(file, message) {
                alert("Upload failed: " + (message.error || message));
                this.removeFile(file);
            });
        }
    };

    // 2. Fetch data and show the table
    function loadPreview(path) {
        const previewSection = document.getElementById('preview-section');
        const tbody = document.querySelector('#preview-table tbody');
        
        // Show loading state
        tbody.innerHTML = '<tr><td colspan="11" class="text-center p-4">Loading preview...</td></tr>';
        previewSection.style.display = 'block';

        fetch(`/preview?path=${path}`)
            .then(response => response.json())
            .then(data => {
                fileRows = data; // Save data for saving later
                renderTable(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading preview.');
            });
    }

    // 3. Render rows into the table
    function renderTable(data) {
        const tbody = document.querySelector('#preview-table tbody');
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="text-center">No data found in file.</td></tr>';
            return;
        }

        tbody.innerHTML = data.map((row, index) => `
            <tr>
                <td><input type="checkbox" class="row-checkbox" value="${index}" checked></td>
                <td>${row.id_proveedor || ''}</td>
                <td>${row.id_planta || ''}</td>
                <td>${row.numero_pedimento || row.Numero_Pedimento || ''}</td>
                <td>${row.division || row.Division || ''}</td>
                <td>${row.periodo || row.Periodo || ''}</td>
                <td><span class="badge bg-primary">${row.avance || row.Avance || 0}%</span></td>
                <td>${row.estatus || row.Estatus || ''}</td>
                <td>${row.responsable || row.Responsable || ''}</td>
                <td>${row.inicio_proceso || row.Inicio_Proceso || ''}</td>
                <td>${row.tipo || ''}</td>
            </tr>
        `).join('');
    }

    // 4. Handle "Select All" toggle
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // 5. Submit Selected Rows to Database
    document.getElementById('confirm-save').addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert("Please select at least one row to save.");
            return;
        }

        const selectedData = Array.from(selectedCheckboxes).map(cb => {
            const index = cb.value;
            return fileRows[index];
        });

        // Disable button to prevent double clicks
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        fetch('/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ items: selectedData })
        })
        .then(res => res.json())
        .then(res => {
            if (res.error) {
                alert("Error: " + res.error);
                btn.disabled = false;
                btn.innerHTML = 'Confirm and Save';
            } else {
                alert(res.message);
                window.location.reload(); // Refresh to start over
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while saving.");
            btn.disabled = false;
        });
    });
</script>

</body>
</html>