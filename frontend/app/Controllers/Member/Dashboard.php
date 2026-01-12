<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\BookModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = session('user');

        if (!$user || !isset($user['id'])) {
            return redirect()->to('/');
        }

        $tm = new TransactionModel();
        $summary = $tm->getDashboardSummary((int)$user['id']);

        $bm = new BookModel();
        $reco = $bm->getRecommendations(5);

        return view('member/dashboard', [
            'title'   => 'Member Dashboard',
            'user'    => $user,
            'summary' => $summary,
            'reco'    => $reco,
        ]);
    }
}
