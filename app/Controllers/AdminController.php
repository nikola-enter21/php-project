<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\UserModel;
use App\Models\QuoteModel;
use App\Models\LogModel;

class AdminController
{
    private UserModel $userModel;
    private QuoteModel $quoteModel;
    private LogModel $logModel;

    public function __construct(UserModel $userModel, QuoteModel $quoteModel, LogModel $logModel)
    {
        $this->userModel = $userModel;
        $this->quoteModel = $quoteModel;
        $this->logModel = $logModel;
    }

    public function dashboard(Request $req, Response $res)
    {
        $user = $req->session()->get('user');

        // Mock response for admin dashboard
        $data = [
            'totalUsers' => $this->userModel->getTotalCount(),
            'totalQuotes' => $this->quoteModel->getTotalCount(),
        ];

        $res->view('admin/dashboard', [
            'user' => $user,
            'data' => $data
        ]);
    }

    public function manageUsers(Request $req, Response $res)
    {
        $search = $req->query('search') ?? '';
        $user = $req->session()->get('user');
        $users = $this->userModel->searchUsersExcluding($search, $user['id']);

        $res->view('admin/users', [
            'users' => $users,
            'search' => $search,
            'title' => 'Manage Users',
            'user' => $user
        ]);
    }

    public function viewLogs(Request $req, Response $res)
    {
        $search = $req->query('search') ?? '';
        $logs = $this->logModel->getFilteredLogs($search);
        $res->view('admin/logs', [
            'logs' => $logs,
            'search' => $search,
        ]);
    }

    public function deleteLogById(Request $req, Response $res)
    {
        $logId = $req->param('id');
        if (!$logId) {
            return $res->json(['success' => false, 'message' => 'Log ID is required'], 400);
        }

        if ($this->logModel->delete($logId)) {
            return $res->json(['success' => true, 'message' => 'Log deleted successfully']);
        } else {
            return $res->json(['success' => false, 'message' => 'Failed to delete log'], 500);
        }
    }

    public function deleteLogs(Request $req, Response $res)
    {
        if (this->logModel->deleteAllLogs()) {
            $res->json(['success' => true, 'message' => 'All logs deleted successfully']);
        } else {
            $res->json(['success' => false, 'message' => 'Failed to delete logs'], 500);
        }
    }

    public function mostLikedQuotes(Request $req, Response $res)
    {
        $user = $req->session()->get('user');
        $quotes = $this->quoteModel->getMostLikedQuotes(10, $user['id']);
        $res->view('admin/quotes', ['quotes' => $quotes, 'title' => 'Most Liked Quotes', 'user' => $user]);
    }

    public function reportedQuotes(Request $req, Response $res)
    {
        $user = $req->session()->get('user');
        $quotes = $this->quoteModel->getReportedQuotes($user['id']);
        $res->view('admin/quotes', ['quotes' => $quotes, 'title' => 'Reported Quotes', 'user' => $user]);
    }

    public function updateUserRole(Request $req, Response $res)
    {
        $userId = $req->param('id');
        $role = $req->body('role');

        if (!$userId || !$role) {
            return $res->json(['success' => false, 'message' => 'Invalid parameters'], 400);
        }

        if ($this->userModel->updateUserRole($userId, $role)) {
            return $res->json(['success' => true, 'message' => 'User role updated successfully']);
        } else {
            return $res->json(['success' => false, 'message' => 'Failed to update user role'], 500);
        }
    }

}
