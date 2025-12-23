<!DOCTYPE html>
<html>
<head>
    <title>Excel Preview & Upload</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="p-5">

<div class="container">
    <h3>Upload & Preview Pedimentos</h3>
    <form action="{{ route('upload') }}" class="dropzone" id="myDropzone"></form>
    
    <div id="preview-section" class="mt-4" style="display:none;">
        <h4>Data Preview</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="preview-table">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <button id="confirm-save" class="btn btn-success">Confirm and Save to Database</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script>
    Dropzone.options.myDropzone = {
        paramName: "file",
        maxFiles: 1,
        acceptedFiles: ".csv,.xlsx,.xls",
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        success: function(file, response) {
            const tempPath = response.path;
            loadPreview(tempPath);
        }
    };

    let currentPath = "";

    function loadPreview(path) {
        currentPath = path;
        fetch(`/preview?path=${path}`)
            .then(res => res.json())
            .then(data => {
                const tableHead = document.querySelector('#preview-table thead');
                const tableBody = document.querySelector('#preview-table tbody');
                
                // Set Headers
                tableHead.innerHTML = `<tr>${Object.keys(data[0]).map(k => `<th>${k}</th>`).join('')}</tr>`;
                
                // Set Rows (limit to 10 for preview performance)
                tableBody.innerHTML = data.slice(0, 10).map(row => `
                    <tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>
                `).join('');

                document.getElementById('preview-section').style.display = 'block';
            });
    }

    document.getElementById('confirm-save').addEventListener('click', function() {
        fetch('/store', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
            },
            body: JSON.stringify({ path: currentPath })
        })
        .then(res => res.json())
        .then(res => {
            alert(res.message);
            location.reload();
        });
    });
</script>
</body>
</html>