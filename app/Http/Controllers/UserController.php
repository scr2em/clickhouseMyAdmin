<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            return view('errors.connection');
        }

        $databases = $ch->getDatabases();

        try {
            $users = $ch->getUsers();
        } catch (\Exception $e) {
            $users = [];
            $error = $e->getMessage();
        }

        return view('users.index', compact('databases', 'users') + ['error' => $error ?? null]);
    }

    public function create(ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            return view('errors.connection');
        }

        $databases = $ch->getDatabases();
        $roles = $ch->getRoles();
        $allDatabases = $databases;

        return view('users.create', compact('databases', 'roles', 'allDatabases'));
    }

    public function store(Request $request, ClickHouseService $ch)
    {
        $request->validate([
            'username' => ['required', 'regex:/^[a-zA-Z0-9_]+$/'],
            'password' => ['required', 'min:1', 'confirmed'],
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $defaultDatabase = $request->input('default_database');
        $roles = $request->input('roles', []);

        // Sanitize identifier
        $safeUser = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        $escapedPassword = addslashes($password);

        $sql = "CREATE USER `{$safeUser}` IDENTIFIED BY '{$escapedPassword}'";

        if ($defaultDatabase) {
            $safeDb = preg_replace('/[^a-zA-Z0-9_]/', '', $defaultDatabase);
            $sql .= " DEFAULT DATABASE `{$safeDb}`";
        }

        if (!empty($roles)) {
            $safeRoles = array_map(fn($r) => '`' . preg_replace('/[^a-zA-Z0-9_]/', '', $r) . '`', $roles);
            $sql .= ' DEFAULT ROLE ' . implode(', ', $safeRoles);
        }

        $result = $ch->statement($sql);

        if ($result['success']) {
            return redirect()->route('users.show', $safeUser)->with('success', "User '{$safeUser}' created successfully.");
        }

        return back()->withInput()->withErrors(['error' => $result['error'] ?? 'Failed to create user.']);
    }

    public function show(string $user, ClickHouseService $ch)
    {
        if (!$ch->ping()) {
            return view('errors.connection');
        }

        $databases = $ch->getDatabases();
        $userInfo = $ch->getUserInfo($user);

        if (empty($userInfo)) {
            return redirect()->route('users.index')->withErrors(['error' => "User '{$user}' not found."]);
        }

        $grants = $ch->getUserGrants($user);
        $roles = $ch->getRoles();
        $allDatabases = $databases;

        return view('users.show', compact('databases', 'user', 'userInfo', 'grants', 'roles', 'allDatabases'));
    }

    public function grant(Request $request, string $user, ClickHouseService $ch)
    {
        $request->validate([
            'privilege' => ['required', 'regex:/^[a-zA-Z_ ]+$/'],
        ]);

        $safeUser = preg_replace('/[^a-zA-Z0-9_]/', '', $user);
        $privilege = $request->input('privilege');
        $database = $request->input('database', '*');
        $table = $request->input('table', '*');

        $safeDb = $database && $database !== '*' ? '`' . preg_replace('/[^a-zA-Z0-9_]/', '', $database) . '`' : '*';
        $safeTbl = $table && $table !== '*' ? '`' . preg_replace('/[^a-zA-Z0-9_]/', '', $table) . '`' : '*';

        $target = "{$safeDb}.{$safeTbl}";
        $sql = "GRANT {$privilege} ON {$target} TO `{$safeUser}`";

        $result = $ch->statement($sql);

        if ($request->hasHeader('HX-Request')) {
            $grants = $ch->getUserGrants($user);
            return view('users.partials.grants', compact('user', 'grants'));
        }

        if ($result['success']) {
            return redirect()->route('users.show', $user)->with('success', 'Grant added.');
        }

        return back()->withErrors(['error' => $result['error'] ?? 'Failed to add grant.']);
    }

    public function revoke(Request $request, string $user, ClickHouseService $ch)
    {
        $grantStatement = $request->input('grant_statement', '');

        // Convert "GRANT ... TO user" → "REVOKE ... FROM user"
        $revokeStatement = preg_replace('/^GRANT\b/i', 'REVOKE', $grantStatement);
        $revokeStatement = preg_replace('/\bTO\b(?=[^()]*$)/i', 'FROM', $revokeStatement);

        $result = $ch->statement($revokeStatement);

        if ($request->hasHeader('HX-Request')) {
            $grants = $ch->getUserGrants($user);
            return view('users.partials.grants', compact('user', 'grants'));
        }

        if ($result['success']) {
            return redirect()->route('users.show', $user)->with('success', 'Grant revoked.');
        }

        return back()->withErrors(['error' => $result['error'] ?? 'Failed to revoke grant.']);
    }

    public function destroy(string $user, ClickHouseService $ch)
    {
        $safeUser = preg_replace('/[^a-zA-Z0-9_]/', '', $user);
        $result = $ch->statement("DROP USER IF EXISTS `{$safeUser}`");

        if ($result['success']) {
            return redirect()->route('users.index')->with('success', "User '{$safeUser}' dropped.");
        }

        return redirect()->route('users.index')->withErrors(['error' => $result['error'] ?? 'Failed to drop user.']);
    }
}
