@extends('layouts.app')
@section('content')

    <div class="mb-5 sel2-cstm">
        <select id="statistics-select" class="form-control select2">
            <option value="1" @if(request('period') == 1 || !request('period')) selected @endif>неделя</option>
            <option value="2" @if(request('period') == 2) selected @endif>2 недели</option>
            <option value="3" @if(request('period') == 3) selected @endif>месяц</option>
            <option value="4" @if(request('period') == 4) selected @endif>6 месяцев</option>
            <option value="5" @if(request('period') == 5) selected @endif>год</option>
        </select>
    </div>

    <div>
        <canvas id="statistics-chart" width="400" height="400"></canvas>
    </div>

    <script>
        let statisticsData = <?= json_encode($data) ?>;
    </script>

@endsection
