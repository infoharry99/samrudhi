<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MonthlyRegister;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DeploymentController extends Controller
{
    /**
     * Verify deployment authorization token
     */
    private function verifyToken(Request $request): bool
    {
        $validToken = env('DEPLOY_TOKEN', 'samrudhi2026');
        $providedToken = $request->query('token') ?? $request->input('token');

        // Also allow if admin is logged in
        if (auth()->check() && auth()->user()->isAdmin()) {
            return true;
        }

        return !empty($providedToken) && hash_equals($validToken, $providedToken);
    }

    /**
     * Deployment Control Center Dashboard
     */
    public function index(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->view('deploy.unauthorized', [
                'token' => $request->query('token'),
            ], 403);
        }

        $token = $request->query('token') ?? env('DEPLOY_TOKEN', 'samrudhi2026');

        // Check DB Status
        $dbConnected = false;
        $dbError = null;
        $membersCount = 0;
        $usersCount = 0;
        $registersCount = 0;

        try {
            DB::connection()->getPdo();
            $dbConnected = true;
            $membersCount = DB::table('members')->count();
            $usersCount = DB::table('users')->count();
            $registersCount = DB::table('monthly_registers')->count();
        } catch (\Exception $e) {
            $dbError = $e->getMessage();
        }

        return view('deploy.index', compact(
            'dbConnected',
            'dbError',
            'membersCount',
            'usersCount',
            'registersCount',
            'token'
        ));
    }

    /**
     * Execute Migration & Seeding
     */
    public function migrateAndSeed(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Invalid deployment token.'], 403);
        }

        $logs = [];
        $startTime = microtime(true);

        try {
            // 1. Run Migrations
            Artisan::call('migrate', ['--force' => true]);
            $logs['migrate'] = Artisan::output();

            // 2. Run Database Seeder
            Artisan::call('db:seed', ['--force' => true]);
            $logs['seed'] = Artisan::output();

            // 3. Clear & Optimize Caches
            Artisan::call('optimize:clear');
            $logs['optimize_clear'] = Artisan::output();

            Artisan::call('storage:link');
            $logs['storage_link'] = Artisan::output();

            $duration = round(microtime(true) - $startTime, 2);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Database successfully migrated and seeded!',
                    'duration' => "{$duration}s",
                    'logs' => $logs,
                ]);
            }

            return back()->with('deploy_success', [
                'action' => 'Migrate & Seed',
                'duration' => "{$duration}s",
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 500);
            }

            return back()->with('deploy_error', $e->getMessage());
        }
    }

    /**
     * Run Migrations Only
     */
    public function migrate(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'output' => $output]);
            }

            return back()->with('deploy_success', [
                'action' => 'Migrate Only',
                'duration' => 'done',
                'logs' => ['migrate' => $output],
            ]);
        } catch (\Exception $e) {
            return back()->with('deploy_error', $e->getMessage());
        }
    }

    /**
     * Run Seeder Only
     */
    public function seed(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('db:seed', ['--force' => true]);
            $output = Artisan::output();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'output' => $output]);
            }

            return back()->with('deploy_success', [
                'action' => 'Seed Only',
                'duration' => 'done',
                'logs' => ['seed' => $output],
            ]);
        } catch (\Exception $e) {
            return back()->with('deploy_error', $e->getMessage());
        }
    }

    /**
     * Clear & Optimize Application
     */
    public function optimize(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('optimize:clear');
            $clearOut = Artisan::output();

            Artisan::call('config:cache');
            $cfgOut = Artisan::output();

            Artisan::call('route:cache');
            $rtOut = Artisan::output();

            Artisan::call('view:cache');
            $vwOut = Artisan::output();

            return back()->with('deploy_success', [
                'action' => 'Optimize & Cache',
                'duration' => 'done',
                'logs' => [
                    'clear' => $clearOut,
                    'config' => $cfgOut,
                    'routes' => $rtOut,
                    'views' => $vwOut,
                ],
            ]);
        } catch (\Exception $e) {
            return back()->with('deploy_error', $e->getMessage());
        }
    }
}
