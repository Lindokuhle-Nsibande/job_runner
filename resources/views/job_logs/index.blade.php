<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Logs Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://unpkg.com/tabulator-tables@4.9.3/dist/css/tabulator.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <h1>Job Logs Dashboard</h1>

        <div class="row mb-3">
            
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Running Jobs</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $counts['running'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Cancelled Jobs</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $counts['cancelled'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Successful Jobs</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $counts['success'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Failed Jobs</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $counts['failure'] }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div id="job-logs-table"></div>
    </div>

    <script src="https://unpkg.com/tabulator-tables@4.9.3/dist/js/tabulator.min.js"></script>
    <script>
        var table = new Tabulator("#job-logs-table", {
            ajaxURL: "{{ route('api.job_logs') }}",
            layout: "fitColumns",
            pagination: "remote",
            paginationSize: 10,
            columns: [
                {title: "ID", field: "id", sorter: "number"},
                {title: "ProcessID", field: "process_id", sorter: "number"},
                {title: "Class Name", field: "class_name", sorter: "string"},
                {title: "Method Name", field: "method_name", sorter: "string"},
                {title: "Parameters", field: "parameters", sorter: "string"},
                {title: "Status", field: "status", sorter: "string"},
                {title: "Retry Count", field: "retry_count", sorter: "number"},
                {title: "Priority", field: "priority", sorter: "number"},
                {title: "Error Message", field: "error_message", sorter: "string"},
                {title: "Created At", field: "created_at", sorter: "date"},
                {title: "Updated At", field: "updated_at", sorter: "date"},
                {
                    title: "Action",
                    field: "action",
                    formatter: function(cell, formatterParams, onRendered) {
                        var status = cell.getRow().getData().status;
                        if (status === "running") {
                            return "<button class='btn btn-danger cancel-job' data-id='" + cell.getRow().getData().process_id + "'>Cancel</button>";
                        }
                        return "";
                    },
                    cellClick: function(e, cell) {
                        // When Cancel button is clicked, cancel the job
                        var jobId = cell.getRow().getData().process_id;
                        console.log(jobId);
                        cancelJob(jobId);
                    }
                },
            ],
        });

        function cancelJob(jobId) {
            if (confirm('Are you sure you want to cancel this job?')) {
                fetch(`/api/job-logs/${jobId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        alert("Job cancelled successfully.");
                        table.redraw();
                    } else {
                        alert("Failed to cancel the job.");
                    }
                })
                .catch(error => {
                    console.error("Error cancelling job:", error);
                    alert("An error occurred.");
                });
            }
        }
    </script>
</body>
</html>
