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
        $curr_unit = setting('units.currency');

        $user = User::with('journal')->where('id', $user_id)->first();
        $journal_id = $user->journal->id;

        $sum_credit = JournalTransaction::where('journal_id', $journal_id)->sum('credit');
        $sum_debit = JournalTransaction::where('journal_id', $journal_id)->sum('debit');
        $transactions = JournalTransaction::where('journal_id', $journal_id)->orderBy('created_at', 'desc')->take($limit)->get();

        return view('DBasic::widgets.journal_details', [
            'cur_balance'  => $user->journal->balance,
            'curr_unit'    => $curr_unit,
            'display_card' => $display_card,
            'limit'        => $limit,
            'journal_id'   => $journal_id,
            'sum_credit'   => money($sum_credit, $curr_unit),
            'sum_debit'    => money($sum_debit, $curr_unit),
            'transactions' => $transactions,
        ]);
    }
}
