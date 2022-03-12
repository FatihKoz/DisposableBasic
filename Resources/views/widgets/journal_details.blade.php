{{-- Visible Card or Simple Text --}}
@if($display_card === true)
  <div class="card text-center mb-2">
    <div class="card-body p-2">
      <a href="#" data-bs-toggle="modal" data-bs-target="#JournalModal{{ $journal_id }}">{{ $cur_balance }}</a>
    </div>
    <div class="card-footer p-0 small fw-bold">
      Current Balance
    </div>
  </div>
@else
  <a href="#" data-bs-toggle="modal" data-bs-target="#JournalModal">{{ $cur_balance }}</a>
@endif

{{-- Transaction Modal --}}
<div class="modal fade" id="JournalModal{{ $journal_id }}" tabindex="-1" aria-labelledby="JournalModalLabel{{ $journal_id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header p-1">
        <h5 class="modal-title" id="JournalModalLabel{{ $journal_id }}">Journal Transactions & Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <table class="table table-sm table-borderless table-striped mb-0 text-center">
          <tr>
            <th class="text-start">Description / Memo</th>
            <th>Credit</th>
            <th>Debit</th>
            <th class="text-end">Date</th>
          </tr>
          @if($transactions->count() > 0)
            @foreach($transactions as $record)
              <tr>
                <td class="text-start">{{ $record->memo }}</td>
                <td>
                  @if(filled($record->credit))
                    {{ money($record->credit, $curr_unit) }}
                  @endif
                </td>
                <td>
                  @if(filled($record->debit))
                    {{ money($record->debit, $curr_unit) }}
                  @endif
                </td>
                <td class="text-end">{{ $record->created_at->format('d.m.Y H:i') }}</td>
              </tr>
            @endforeach
            <tr>
              <td colspan="4" class="text-end small">Only last {{ $limit }} entries are displayed</td>
            </tr>
          @else
            <tr>
              <td colspan="4">No Records Found</td>
            </tr>
          @endif
        </table>
        <table class="table table-sm table-borderless table-striped mb-0 text-center">
          <tr>
            <th>Total Credit</th>
            <th>Total Debit</th>
            <th>Current Balance</th>
          </tr>
          <tr>
            <td>{{ $sum_credit }}</td>
            <td>{{ $sum_debit }}</td>
            <td>{{ $cur_balance }}</td>
          </tr>
        </table>
      </div>
      <div class="modal-footer p-1">
        <button type="button" class="btn btn-sm btn-secondary m-0 mx-1 p-0 px-1" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>