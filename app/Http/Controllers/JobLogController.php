<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobLog;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class JobLogController extends Controller
{
    public function index()
    {
        $counts = [
            'pending' => JobLog::where('status', 'pending')->count(),
            'running' => JobLog::where('status', 'running')->count(),
            'success' => JobLog::where('status', 'success')->count(),
            'failure' => JobLog::where('status', 'failure')->count(),
            'cancelled' => JobLog::where('status', 'cancelled')->count(),
        ];

        return view('job_logs.index', compact('counts'));
    }

    public function getLogs(Request $request)
    {
        $query = JobLog::query();

        // Pagination and sorting
        $logs = $query->orderBy($request->input('sort', 'created_at'), $request->input('order', 'desc'))
            ->paginate($request->input('size', 10));

        return response()->json($logs);
    }

    public function cancelJob($id)
    {
        $jobLog = JobLog::where('process_id', $id)->first();

        if (!$jobLog || $jobLog->status !== 'running') {
            return response()->json(['success' => false, 'message' => 'Job is not running or not found.']);
        }

        $processId = $jobLog->process_id;

        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("taskkill /F /PID $processId", $output, $result);
            } else {
                exec("kill -9 $processId", $output, $result);
            }

            if ($result === 0) {
                $jobLog->status = 'cancelled';
                $jobLog->save();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => "Failed to kill process $processId"]);
            }
        } catch (ProcessFailedException $exception) {
            return response()->json(['success' => false, 'message' => 'Failed to cancel job.']);
        }
    }
}