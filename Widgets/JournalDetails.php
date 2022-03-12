<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\User;
use App\Models\JournalTransaction;
use Illuminate\Support\Facades\Auth;

class JournalDetails extends Widget
{
    protected $config = ['user' => null, 'limit' => null, 'card' => true];

    public function run()
    {
        $display_card = is_bool($this->config['card']) ? $this->config['card'] : true;
        $limit = is_numeric($this->config['limit']) ? $this->config['limit'] : 25;
        $user_id = filled($this->config['user']) && is_numeric($this->config['user']) ? $this->config['user'] : Auth::id();
        $units = DB_GetUnits();

        $user = User::with('journal')->where('id', $user_id)->first();

        $sum_credit = JournalTransaction::where('journal_id', $user->journal->id)->sum('credit');
        $sum_debit = JournalTransaction::where('journal_id', $user->journal->id)->sum('debit');
        $transactions = JournalTransaction::where('journal_id', $user->journal->id)->orderBy('created_at', 'desc')->take($limit)->get();

        return view('DBasic::widgets.journal_details', [
            'cur_balance'  => $user->journal->balance,
            'display_card' => $display_card,
            'limit'        => $limit,
            'sum_credit'   => money($sum_credit, $units['currency']),
            'sum_debit'    => money($sum_debit, $units['currency']),
            'transactions' => $transactions,
            'units'        => $units,
        ]);
    }
}
