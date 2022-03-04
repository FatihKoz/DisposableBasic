@extends('app')
@section('title', __('DBasic::common.news'))

@section('content')
  @if(!$allnews->count())
    <div class="alert alert-info mb-1 p-1 px-2 fw-bold">No News!</div>
  @else
    <div class="row row-cols-lg-2">
      @foreach($allnews as $news)
        <div class="col-lg">
          <div class="card mb-2">
            <div class="card-header p-1">
              <h5 class="m-1">
                {{ $news->subject }}
                <i class="fas fa-book-open float-end"></i>
              </h5>
            </div>
            <div class="card-body p-1 text-start">
              {!! $news->body !!}
            </div>
            <div class="card-footer p-0 px-1 text-end small fw-bold">
              <span class="float-start">{{ optional($news->user)->name_private }}</span>
              {{ $news->created_at->format('d.M.Y H:i') }}
            </div>
          </div>
        </div>
      @endforeach
    </div>
    {{ $allnews->links('pagination.default') }}
  @endif
@endsection
