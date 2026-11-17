<?php

namespace App\Http\Controllers;

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

        // Allow if authenticated admin (safely wrapped in try-catch in case session is absent)
        try {
            if (auth()->check() && auth()->user()->isAdmin()) {
                return true;
            }
        } catch (\Throwable $e) {
            // Session not initialized yet
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

        $token = $request->query('token') ?? $request->input('token') ?? env('DEPLOY_TOKEN', 'samrudhi2026');

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
        } catch (\Throwable $e) {
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
            return response()->view('deploy.unauthorized', [
                'token' => $request->query('token'),
            ], 403);
        }

        $token = $request->query('token') ?? $request->input('token') ?? env('DEPLOY_TOKEN', 'samrudhi2026');
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

            try {
                Artisan::call('storage:link');
                $logs['storage_link'] = Artisan::output();
            } catch (\Throwable $e) {
                $logs['storage_link'] = 'Storage link: ' . $e->getMessage();
            }

            $duration = round(microtime(true) - $startTime, 2) . 's';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Database successfully migrated and seeded!',
                    'duration' => $duration,
                    'logs' => $logs,
                ]);
            }

            return view('deploy.result', [
                'success' => true,
                'action' => 'Database Migration & Seeding Complete',
                'duration' => $duration,
                'logs' => $logs,
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2) . 's';
            $logs['error'] = $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            return view('deploy.result', [
                'success' => false,
                'action' => 'Migration / Seeding Failed',
                'duration' => $duration,
                'logs' => $logs,
                'token' => $token,
            ]);
        }
    }

    /**
     * Run Migrations Only
     */
    public function migrate(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->view('deploy.unauthorized', ['token' => $request->query('token')], 403);
        }

        $token = $request->query('token') ?? $request->input('token') ?? env('DEPLOY_TOKEN', 'samrudhi2026');
        $startTime = microtime(true);

        try {
            Artisan::call('migrate', ['--force' => true]);
            $logs = ['migrate' => Artisan::output()];
            $duration = round(microtime(true) - $startTime, 2) . 's';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'output' => $logs['migrate']]);
            }

            return view('deploy.result', [
                'success' => true,
                'action' => 'Database Migrations Executed',
                'duration' => $duration,
                'logs' => $logs,
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2) . 's';
            return view('deploy.result', [
                'success' => false,
                'action' => 'Migration Failed',
                'duration' => $duration,
                'logs' => ['error' => $e->getMessage()],
                'token' => $token,
            ]);
        }
    }

    /**
     * Run Seeder Only
     */
    public function seed(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->view('deploy.unauthorized', ['token' => $request->query('token')], 403);
        }

        $token = $request->query('token') ?? $request->input('token') ?? env('DEPLOY_TOKEN', 'samrudhi2026');
        $startTime = microtime(true);

        try {
            Artisan::call('db:seed', ['--force' => true]);
            $logs = ['seed' => Artisan::output()];
            $duration = round(microtime(true) - $startTime, 2) . 's';

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'output' => $logs['seed']]);
            }

            return view('deploy.result', [
                'success' => true,
                'action' => 'Database Seeder Executed',
                'duration' => $duration,
                'logs' => $logs,
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2) . 's';
            return view('deploy.result', [
                'success' => false,
                'action' => 'Seeding Failed',
                'duration' => $duration,
                'logs' => ['error' => $e->getMessage()],
                'token' => $token,
            ]);
        }
    }

    /**
     * Clear & Optimize Application
     */
    public function optimize(Request $request)
    {
        if (!$this->verifyToken($request)) {
            return response()->view('deploy.unauthorized', ['token' => $request->query('token')], 403);
        }

        $token = $request->query('token') ?? $request->input('token') ?? env('DEPLOY_TOKEN', 'samrudhi2026');
        $startTime = microtime(true);

        try {
            Artisan::call('optimize:clear');
            $clearOut = Artisan::output();

            Artisan::call('config:cache');
            $cfgOut = Artisan::output();

            Artisan::call('route:cache');
            $rtOut = Artisan::output();

            Artisan::call('view:cache');
            $vwOut = Artisan::output();

            $duration = round(microtime(true) - $startTime, 2) . 's';

            return view('deploy.result', [
                'success' => true,
                'action' => 'Optimize & Cache Completed',
                'duration' => $duration,
                'logs' => [
                    'clear' => $clearOut,
                    'config' => $cfgOut,
                    'routes' => $rtOut,
                    'views' => $vwOut,
                ],
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2) . 's';
            return view('deploy.result', [
                'success' => false,
                'action' => 'Optimize Failed',
                'duration' => $duration,
                'logs' => ['error' => $e->getMessage()],
                'token' => $token,
            ]);
        }
    }
}
